<?php

namespace App\Services;

use App\Models\ProgramaAuditoria;
use App\Models\AuditoriaInterna;
use App\Models\ChecklistaAuditoria;
use App\Models\HallazgoAuditoria;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuditoriaInternaService
{
    /**
     * Crear un programa de auditoría anual.
     */
    public function crearPrograma(array $data): ProgramaAuditoria
    {
        $data['creado_por'] = Auth::id();
        $data['modificado_por'] = Auth::id();
        $data['estado']     = $data['estado'] ?? 'programada';

        $programa = ProgramaAuditoria::create($data);
        return $programa->fresh(['auditorJefe']);
    }

    /**
     * Crear una auditoría dentro de un programa y generar código AUD-YYYY-NNNN.
     */
    public function crearAuditoria(ProgramaAuditoria $programa, array $data): AuditoriaInterna
    {
        $data['programa_auditoria_id'] = $programa->id;
        $data['creado_por'] = Auth::id();
        $data['estado'] = $data['estado'] ?? 'programada';

        // Generar código
        $year  = now()->year;
        $last  = AuditoriaInterna::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->value('codigo');

        $num = 1;
        if ($last) {
            $lastNum = (int) last(explode('-', $last));
            $num = $lastNum + 1;
        }

        $data['codigo'] = sprintf('AUD-%s-%04d', $year, $num);

        $auditoria = AuditoriaInterna::create($data);
        return $auditoria->fresh(['programa', 'responsableAuditor']);
    }

    /**
     * Agregar un item al checklist de una auditoría.
     */
    public function agregarChecklistItem(
        AuditoriaInterna $auditoria,
        array $data
    ): ChecklistaAuditoria {
        $data['auditoria_interna_id'] = $auditoria->id;
        $data['creado_por'] = Auth::id();
        $data['modificado_por'] = Auth::id();

        $item = ChecklistaAuditoria::create($data);
        return $item;
    }

    /**
     * Eliminar un item del checklist.
     */
    public function eliminarChecklistItem(ChecklistaAuditoria $item): bool
    {
        $item->delete();
        return true;
    }

    /**
     * Registrar un hallazgo en una auditoría.
     */
    public function registrarHallazgo(
        AuditoriaInterna $auditoria,
        array $data
    ): HallazgoAuditoria {
        $data['auditoria_interna_id'] = $auditoria->id;
        $data['creado_por'] = Auth::id();
        $data['modificado_por'] = Auth::id();

        // Generar número de hallazgo dentro de la auditoría
        $lastNum = HallazgoAuditoria::where('auditoria_interna_id', $auditoria->id)
            ->whereNotNull('numero')
            ->orderBy('numero', 'desc')
            ->value('numero');

        $data['numero'] = is_numeric($lastNum) ? (int) $lastNum + 1 : 1;

        $hallazgo = HallazgoAuditoria::create($data);
        return $hallazgo->fresh(['auditoria']);
    }

    /**
     * Generar un informe completo de la auditoría con estadísticas.
     */
    public function generarInforme(AuditoriaInterna $auditoria): array
    {
        $checklistTotal = $auditoria->checklistItems()->count();
        $checklistConforme    = $auditoria->checklistItems()->where('cumplimiento', 'conforme')->count();
        $checklistNoConforme  = $auditoria->checklistItems()->where('cumplimiento', 'no_conforme')->count();

        $hallazgosTotal = $auditoria->hallazgos()->count();
        $hallazgosConforme    = $auditoria->hallazgos()->conforme()->count();
        $hallazgosNCMayor     = $auditoria->hallazgos()->noConformeMayor()->count();
        $hallazgosNCMenor     = $auditoria->hallazgos()->noConformeMenor()->count();
        $hallazgosObservacion = $auditoria->hallazgos()->observacion()->count();

        $ncAsociadas = $auditoria->hallazgos()->whereNotNull('nc_id')->count();

        // Porcentaje de cumplimiento
        $porcentajeCumplimiento = $checklistTotal > 0
            ? round(($checklistConforme / $checklistTotal) * 100, 1)
            : 0;

        return [
            'auditoria' => [
                'codigo'         => $auditoria->codigo,
                'area_auditar'   => $auditoria->area_auditar,
                'estado'         => $auditoria->estado,
                'cumplimiento'   => $auditoria->cumplimiento_general,
                'fecha_programada' => $auditoria->fecha_programada?->format('d/m/Y'),
                'fecha_real_fin'  => $auditoria->fecha_real_fin?->format('d/m/Y'),
            ],
            'resumen' => [
                'checklist_total'       => $checklistTotal,
                'checklist_conforme'    => $checklistConforme,
                'checklist_no_conforme' => $checklistNoConforme,
                'porcentaje_cumplimiento'=> $porcentajeCumplimiento,
            ],
            'hallazgos' => [
                'total'         => $hallazgosTotal,
                'conformes'     => $hallazgosConforme,
                'nc_mayor'      => $hallazgosNCMayor,
                'nc_menor'      => $hallazgosNCMenor,
                'observaciones' => $hallazgosObservacion,
                'nc_asociadas'  => $ncAsociadas,
            ],
            'nivel_auditoria' => $this->determinarNivelAuditoria(
                $hallazgosNCMayor,
                $hallazgosNCMenor,
                $porcentajeCumplimiento
            ),
        ];
    }

    /**
     * Determinar el nivel de la auditoría según hallazgos.
     */
    private function determinarNivelAuditoria(int $ncMayor, int $ncMenor, float $porcentaje): string
    {
        if ($ncMayor > 0) {
            return 'insatisfactorio';
        }

        if ($ncMayor === 0 && $ncMenor <= 2 && $porcentaje >= 90) {
            return 'satisfactorio';
        }

        if ($ncMayor === 0 && $porcentaje >= 80) {
            return 'parcialmente_satisfactorio';
        }

        return 'insatisfactorio';
    }

    /**
     * Iniciar una auditoría.
     */
    public function iniciarAuditoria(AuditoriaInterna $auditoria): AuditoriaInterna
    {
        $auditoria->iniciar();
        return $auditoria->fresh();
    }

    /**
     * Completar una auditoría.
     */
    public function completarAuditoria(
        AuditoriaInterna $auditoria,
        array $data
    ): AuditoriaInterna {
        $cumplimiento = $data['cumplimiento'] ?? 100.00;
        $auditoria->completar($cumplimiento);
        return $auditoria->fresh();
    }

    /**
     * Estadísticas de auditorías del tenant.
     */
    public function stats(): array
    {
        $query = AuditoriaInterna::withoutSoftDeletes();

        $programados      = (clone $query)->programadas()->count();
        $en_curso         = (clone $query)->enCurso()->count();
        $completados      = (clone $query)->completadas()->count();
        $cancelados       = (clone $query)->canceladas()->count();
        $total            = (clone $query)->count();

        // Hallazgos totales
        $hallazgosTotal     = HallazgoAuditoria::query()->count();
        $hallazgosConforme  = HallazgoAuditoria::query()->conforme()->count();
        $hallazgosNCMayor   = HallazgoAuditoria::query()->noConformeMayor()->count();
        $hallazgosNCMenor   = HallazgoAuditoria::query()->noConformeMenor()->count();
        $hallazgosObs       = HallazgoAuditoria::query()->observacion()->count();

        // NC asociadas
        $ncAsociadas = HallazgoAuditoria::query()->whereNotNull('nc_id')->count();

        // Programas
        $programasTotal     = ProgramaAuditoria::query()->count();
        $programasEnCurso   = ProgramaAuditoria::query()->enCurso()->count();
        $programasCompletados = ProgramaAuditoria::query()->completados()->count();

        // Cumplimiento promedio de auditorías completadas
        $cumplimientoPromedio = AuditoriaInterna::completadas()
            ->whereNotNull('cumplimiento_general')
            ->avg('cumplimiento_general');

        return [
            'total_auditorias'        => $total,
            'programadas'             => $programados,
            'en_curso'                => $en_curso,
            'completadas'             => $completados,
            'canceladas'              => $cancelados,
            'programas_total'          => $programasTotal,
            'programas_en_curso'       => $programasEnCurso,
            'programas_completados'    => $programasCompletados,
            'hallazgos_totales'        => $hallazgosTotal,
            'hallazgos_conformes'      => $hallazgosConforme,
            'nc_mayor'                 => $hallazgosNCMayor,
            'nc_menor'                 => $hallazgosNCMenor,
            'observaciones'            => $hallazgosObs,
            'nc_asociadas'             => $ncAsociadas,
            'cumplimiento_promedio'    => round($cumplimientoPromedio ?? 0, 1),
        ];
    }
}
