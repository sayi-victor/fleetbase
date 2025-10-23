<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardStatController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\FuelReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::controller(DashboardStatController::class)->prefix("dashboard")->name("dashboard.")
    ->group(
        function () {
            Route::get("/", "index")->name("index");
        });

Route::controller(UserController::class)->prefix("user")->name("user.")
    ->group(
        function () {
            Route::post("/store", "store")->name("store");
        });

Route::controller(DriverController::class)->prefix("driver")->name("driver.")
    ->group(
        function () {
            Route::get("/all", "index")->name("index");
            Route::post("/store", "store")->name("store");
        });

Route::controller(VehicleController::class)->prefix("vehicle")->name("vehicle.")
    ->group(
        function () {
            Route::get("/all", "index")->name("index");
            Route::post("/store", "store")->name("store");
        });

Route::controller(FuelReportController::class)->prefix("fuel-report")->name("fuel-report.")
    ->group(
        function () {
            Route::get("/all", "index")->name("index");
            Route::post("/store", "store")->name("store");
        });

Route::controller(AuthController::class)->prefix("auth")->name("auth.")
    ->group(
        function () {
            Route::post("/sign-in", "signIn")->name("sign-in")->withoutMiddleware(["auth:sanctum"]);
        });
