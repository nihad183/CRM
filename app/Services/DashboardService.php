<?php

namespace App\Services;

use App\Models\FichePropose;
use App\Models\FicheProposeResume;
use Carbon\Carbon;

class DashboardService
{
    public function build($user)
    {
        $prospectsCount = FichePropose::query()
            ->where(function ($q) {
                $q->where('is_fiche_client', false)
                  ->orWhereNull('is_fiche_client');
            })->count();

        $clientsCount = FichePropose::query()
            ->where('is_fiche_client', true)
            
            ->count();

        $resumesTodayCount = FicheProposeResume::query()
            ->whereDate('created_at', now())->count();

        $convertedThisMonthCount = FichePropose::query()
            ->whereNotNull('converted_to_client_at')
            ->whereYear('converted_to_client_at', now()->year)
            ->whereMonth('converted_to_client_at', now()->month)
            ->count();

        $months = collect(range(1, 12))->map(function ($month) use ($user) {
            return [
                'label' => Carbon::create(now()->year, $month, 1)->format('M'),
                'prospects' => FichePropose::query()
                    ->whereMonth('created_at', $month)
                    ->count(),
                'clients' => FichePropose::query()
                    ->where('is_fiche_client', true)
                    ->whereMonth('created_at', $month)
                    ->count(),
            ];
        });

        return [
            'prospectsCount' => $prospectsCount,
            'clientsCount' => $clientsCount,
            'resumesTodayCount' => $resumesTodayCount,
            'convertedThisMonthCount' => $convertedThisMonthCount,
            'months' => $months,
        ];
    }
}
