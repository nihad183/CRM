<?php

namespace App\Services;

use App\Models\FichePropose;
use App\Models\FicheProposeResume;
use Carbon\Carbon;

class DashboardService
{
    public function build($user)
    {
        $prospectsCount = FichePropose::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('is_fiche_client', false)
                  ->orWhereNull('is_fiche_client');
            })->count();

        $clientsCount = FichePropose::where('user_id', $user->id)
            ->where('is_fiche_client', true)
            ->count();

        $resumesTodayCount = FicheProposeResume::where('user_id', $user->id)
            ->whereDate('created_at', now())->count();

        $convertedThisMonthCount = FichePropose::where('user_id', $user->id)
            ->whereNotNull('converted_to_client_at')
            ->whereYear('converted_to_client_at', now()->year)
            ->whereMonth('converted_to_client_at', now()->month)
            ->count();

        $months = collect(range(1, 12))->map(function ($month) use ($user) {
            return [
                'label' => Carbon::create(now()->year, $month, 1)->format('M'),
                'prospects' => FichePropose::where('user_id', $user->id)
                    ->whereMonth('created_at', $month)
                    ->count(),
                'clients' => FichePropose::where('user_id', $user->id)
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