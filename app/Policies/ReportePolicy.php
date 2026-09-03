<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReportePolicy
{
    /**
     * Determine whether the user can view any reports.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('reportes.view');
    }

    /**
     * Determine whether the user can view the report.
     */
    public function view(User $user): bool
    {
        return $user->can('reportes.view');
    }

    /**
     * Determine whether the user can export a report to PDF.
     */
    public function exportarPdf(User $user): bool
    {
        return $user->can('reportes.view');
    }

    /**
     * Determine whether the user can export a report to Excel.
     */
    public function exportarExcel(User $user): bool
    {
        return $user->can('reportes.view');
    }

    /**
     * Determine whether the user can export to CSV.
     */
    public function exportarCsv(User $user): bool
    {
        return $user->can('reportes.view');
    }
}
