<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDriverRequest;
use Fleetbase\FleetOps\Models\Driver;
use Illuminate\Support\Facades\Hash;
use Fleetbase\Models\User;
use Illuminate\Http\Request;
use Fleetbase\FleetOps\Support\Utils;
use Fleetbase\FleetOps\Http\Controllers\Api\v1\DriverController as Controller;
use Fleetbase\FleetOps\Http\Resources\v1\Driver as DriverResource;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 100;

        $query = Driver::query();
        $drivers = $query->paginate($limit);

        return DriverResource::collection($drivers);
    }

    public function store(StoreDriverRequest $request)
    {
        // get request input
        $input = $request->except(['name', 'password', 'email', 'phone', 'location', 'altitude', 'heading', 'speed', 'meta']);

        // get user details for driver
        $userDetails = $request->only(['name', 'password', 'email', 'phone', 'timezone']);

        // Get current company session
        $company = auth()->user()->company ?? null;

        // Debugging: Ensure company is retrieved correctly
        if (!$company) {
            return response()->apiError('Company not found.');
        }

        // Apply user infos
        $userDetails = User::applyUserInfoFromRequest($request, $userDetails);

        // create user account for driver
        $user = User::create($userDetails);

        if ($request->filled("password")) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        // Assign company
        if ($company) {
            $user->assignCompany($company);
        } else {
            $user->deleteQuietly();

            return response()->apiError('Unable to assign driver to company.');
        }

        // Set user type
        $user->setUserType('driver');

        // assign driver role
        $user->assignSingleRole('Driver');

        // set user id
        $input['user_uuid']    = $user->uuid;
        $input['company_uuid'] = $company->uuid;  // Ensure correct company_uuid is set

        // vehicle assignment public_id -> uuid
        if ($request->has('vehicle')) {
            $input['vehicle_uuid'] = Utils::getUuid('vehicles', [
                'public_id'    => $request->input('vehicle'),
                'company_uuid' => $company->uuid,  // Use $company->uuid instead of session
            ]);
        }

        // set default online
        if (!isset($input['online'])) {
            $input['online'] = 0;
        }

        // latitude / longitude
        if ($request->has(['latitude', 'longitude'])) {
            $input['location'] = Utils::getPointFromCoordinates($request->only(['latitude', 'longitude']));
        }

        // create the driver
        $driver = Driver::create($input);

        // load user
        $driver = $driver->load(['user', 'vehicle', 'vendor', 'currentJob']);

        // response the driver resource
        return new DriverResource($driver);
    }
}
