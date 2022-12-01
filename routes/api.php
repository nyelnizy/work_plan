<?php

use App\Http\Controllers\WorkPlanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('get_works',[WorkPlanController::class,'getWorks']);
Route::get('work_status',[WorkPlanController::class,'getWorkStatus']);
Route::get('work_plans',[WorkPlanController::class,'getWorkPlans']);
Route::post('send_work',[WorkPlanController::class,'sendWork']);
Route::post('generate_invoice',[WorkPlanController::class,'generateInvoice']);
