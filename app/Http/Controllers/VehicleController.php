<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use Fleetbase\FleetOps\Http\Controllers\Api\v1\VehicleController as Controller;
use Fleetbase\FleetOps\Http\Resources\v1\Vehicle as VehicleResource;
use Fleetbase\FleetOps\Models\Driver;
use Fleetbase\FleetOps\Models\Vehicle;
use Fleetbase\FleetOps\Support\Utils;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 100;

        $query = Vehicle::query();
        $vehicles = $query->paginate($limit);

        return VehicleResource::collection($vehicles);
    }

    public function store(StoreVehicleRequest $request)
    {
        
        // get request input
        $input = $request->only([
            'status', 'make', 'model', 'year', 
            'trim', 'type', 'plate_number', 'vin', 
            'meta', 'online', 'location', 'altitude', 'heading', 'speed'
        ]);
        // make sure company is set
        $input['company_uuid'] = auth()->user()->company->uuid ?? null;;

        // create instance of vehicle model
        $vehicle = new Vehicle();

        // set default online
        if (!isset($input['online'])) {
            $input['online'] = 0;
        }

        // latitude / longitude
        if ($request->has(['latitude', 'longitude'])) {
            $input['location'] = Utils::getPointFromCoordinates($request->only(['latitude', 'longitude']));
        }

        // apply user input to vehicle
        $vehicle = $vehicle->fill($input);

        // save the vehicle
        $vehicle->save();

        // driver assignment
        if ($request->filled('driver')) {
            // set this vehicle to the driver    
            $driver = Driver::where("public_id", $request->input('driver'))->firstOrFail();
            $driver->vehicle_uuid = $vehicle->uuid;
            $driver->save();
        }

        // response the driver resource
        return new VehicleResource($vehicle);

    }
}
