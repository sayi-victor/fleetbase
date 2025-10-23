<?php

namespace App\Http\Controllers;

use Fleetbase\FleetOps\Models\FuelReport;
use Fleetbase\FleetOps\Models\Driver;
use Fleetbase\FleetOps\Models\Vehicle;

class DashboardStatController extends Controller
{
    public function index()
    {
        $data = [
            [
                "name" => "Fuel Costs",
                "value" => $this->thisMonthFuelCosts(),
                "hint" => "This month",
            ],
            [
                "name" => "Drivers",
                "value" => Driver::all()->count()
            ],
            [
                "name" => "Vehicles",
                "value" => Vehicle::all()->count()
            ],
            [
                "name" => "Open Issues",
                "value" => 10
            ]
        ];

        $fuel_trends = $this->monthlyFuelCostTrend();

        return response()->json(["stats" => $data, "fuel_trends" => $fuel_trends]);
    }


    protected function thisMonthFuelCosts()
    {
        $start_of_this_month = now()->startOfMonth();
        $end_of_this_month   = now()->endOfMonth();

        $report = FuelReport::whereBetween('created_at', [$start_of_this_month, $end_of_this_month])
            ->where('status', 'approved')
            ->sum('amount');

        return $report;
    }

    protected function monthlyFuelCostTrend()
    {
        return FuelReport::where('status', 'approved')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}
