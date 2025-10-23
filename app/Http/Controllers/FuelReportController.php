<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFuelReportRequest;
use Fleetbase\FleetOps\Models\Vehicle;
use Illuminate\Http\Request;
use Fleetbase\FleetOps\Models\Driver;
use Fleetbase\FleetOps\Models\FuelReport;
use Fleetbase\FleetOps\Http\Resources\v1\FuelReport as FuelReportResource;
use Fleetbase\FleetOps\Http\Controllers\Api\v1\FuelReportController as Controller;
use Illuminate\Support\Carbon;

class FuelReportController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 100;
        $start_date = filled($request->start_date) ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $end_date = filled($request->end_date) ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();


        $query = FuelReport::query();
        $query->whereBetween("created_at", [$start_date, $end_date]);
        $reports = $query->paginate($limit);

        $additional = [
            "start" => $start_date->format('Y-m-d'),
            "end" => $end_date->format('Y-m-d')
        ];

        return FuelReportResource::collection($reports)->additional(["date_range" => $additional]);
    }
    public function store(StoreFuelReportRequest $request)
    {
        // get request input
        $input = $request->only([
            'location',
            'odometer',
            'volume',
            'metric_unit',
            'amount',
            'currency',
            'status',
        ]);

        $driver = Driver::where("public_id", $request->input('driver'))->first();
        $vehicle = Vehicle::where("public_id", $request->input('vehicle'))->first();

        // get the user uuid
        $company = auth()->user()->company ?? null;
        $input['company_uuid']      = $company->uuid;
        $input['driver_uuid']       = $driver->uuid ?? null;
        $input['reported_by_uuid']  = $driver->user_uuid ?? null;
        $input['vehicle_uuid']      = $vehicle->uuid ?? null;
        $input["currency"] = "KES";
        $input['metric_unit'] = "L"; 

        // create the fuel report
        $fuelReport = FuelReport::create($input);

        // response the driver resource
        return new FuelReportResource($fuelReport);
    }
}
