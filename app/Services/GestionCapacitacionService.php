<?php

namespace App\Services;

use App\Models\Capacitacion;
use App\Models\ParticipanteCapacitacion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GestionCapacitacionService
{
    /**
     * Crear una nueva capacitación.
     */
    public function crearCapacitacion(array $data): Capacitacion
    {
        $data['creado_por'] = Auth::id();
        $data['modificado_por'] = Auth::id();
        $data['estado']     = $data['estado'] ?? 'programada';

        $capacitacion = Capacitacion::create($data);
        return $capacitacion->fresh(['instructorMod']);
    }

    /**
     * Actualizar una capacitación existente.
     */
    public function actualizarCapacitacion(Capacitacion $capacitacion, array $data): Capacitacion
    {
        $data['modificado_por'] = Auth::id();

        // Si se cambia el instructor_id, actualizar referencia
        if (isset($data['instructor_id'])) {
            $data['instructor_nombre'] = User::find($data['instructor_id'])?->name;
        }

        $capacitacion->update($data);
        return $capacitacion->fresh(['instructorMod']);
    }

    /**
     * Agregar un participante a la capacitación.
     * Retorna el ParticipanteCapacitacion creado o existente.
     */
    public function agregarParticipante(Capacitacion $capacitacion, int $usuarioId): ParticipanteCapacitacion
    {
        return $capacitacion->agregarParticipante($usuarioId, [
            'tenant_id' => Auth::user()->business_instance_id,
        ]);
    }

    /**
     * Remover un participante de la capacitación.
     */
    public function removerParticipante(Capacitacion $capacitacion, int $partId): bool
    {
        $participante = $capacitacion->participantes()->find($partId);

        if (!$participante) {
            return false;
        }

        $participante->delete();
        return true;
    }

    /**
     * Registrar asistencia de un participante y su puntuación.
     */
    public function registrarAsistencia(Capacitacion $capacitacion, int $partId, int $puntaje): ?ParticipanteCapacitacion
    {
        $participante = $capacitacion->participantes()->find($partId);

        if (!$participante) {
            return null;
        }

        $participante->estado    = 'asistio';
        $participante->puntuacion = $puntaje;
        $participante->fecha_evaluacion = now()->toDateString();
        $participante->saveQuietly();

        $participante->load('usuario');
        return $participante;
    }

    /**
     * Otorgar certificado al participante si aprobó (puntuación >= 70).
     */
    public function otorgarCertificado(ParticipanteCapacitacion $participante): bool
    {
        try {
            $participante->otorgarCertificado();
            return true;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }

    /**
     * Estadísticas de capacitaciones del tenant.
     */
    public function stats(): array
    {
        $query = Capacitacion::withoutSoftDeletes();

        $programadas   = (clone $query)->programadas()->count();
        $en_curso      = (clone $query)->enCurso()->count();
        $completadas   = (clone $query)->completadas()->count();
        $canceladas    = (clone $query)->cancelar()->count();
        $total         = (clone $query)->count();

        // Total participantes
        $totalParticipantes  = ParticipanteCapacitacion::count();
        $inscritos           = ParticipanteCapacitacion::inscritos()->count();
        $asistieron          = ParticipanteCapacitacion::asistio()->count();
        $noAsistieron        = ParticipanteCapacitacion::noAsistio()->count();
        $certificados        = ParticipanteCapacitacion::conCertificado()->count();

        // Promedio de asistencia por modalidad
        $porModalidad = Capacitacion::selectRaw(
            'modalidad, COUNT(*) as total, AVG(asistencia_percentage) as avg_asist'
        )
            ->groupBy('modalidad')
            ->get()
            ->map(fn ($row) => [
                'total'    => (int) $row->total,
                'avg_asist'=> round((float) $row->avg_asist, 1),
            ])
            ->toArray();

        $promedioGeneralAsistencia = 0;
        if ($completadas > 0) {
            $promedioGeneralAsistencia = round(
                Capacitacion::completadas()->avg('asistencia_percentage') ?? 0, 1
            );
        }

        return [
            'total'              => $total,
            'programadas'        => $programadas,
            'en_curso'           => $en_curso,
            'completadas'        => $completadas,
            'canceladas'         => $canceladas,
            'total_participantes'=> $totalParticipantes,
            'inscritos'          => $inscritos,
            'asistieron'         => $asistieron,
            'no_asistieron'      => $noAsistieron,
            'certificados'       => $certificados,
            'promedio_asistencia_global' => $promedioGeneralAsistencia,
            'por_modalidad'      => $porModalidad,
        ];
    }
}
