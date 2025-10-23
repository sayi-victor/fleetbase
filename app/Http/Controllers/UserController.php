<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use Fleetbase\Http\Controllers\Internal\v1\UserController as Controller;
class UserController extends Controller
{
    public function store(StoreUserRequest $request)
    {
        return $this->createRecord($request);
    }
}
