<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('v1')->group(function () {

    //note: get is used instead of post for easier access, if better security is needed, use post instead
    // also authentication is dissmissed in favor of simplicity and public access

    //statistics route(s)
    Route::group(['prefix' => 'stats'], function () {
        Route::get('{region}', 'PropertyAnalyticStatsController@getStatistics');
    });

    //records manipulation routes
    Route::group(['prefix' => 'update'], function () {
        Route::get('property', 'PropertyController@store');
        Route::get('analytic_type', 'AnalyticTypeController@store');
        Route::get('property_analytic', 'PropertyAnalyticController@store');
    });

});

