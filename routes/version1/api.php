<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['prefix' => 'v1/api/'], function() use ($router) {

    $router->get('/parking/slots', ['uses' => 'Parking\ParkingSlotController@get']);
    $router->delete('/parking/slots', ['uses' => 'Parking\ParkingSlotController@truncate']);

    $router->post('/park/{entryPointCoverage}', ['uses' => 'Parking\ParkingController@park']);
    $router->post('/unpark/{parkedCarId}', ['uses' => 'Parking\ParkingController@unpark']);

});
