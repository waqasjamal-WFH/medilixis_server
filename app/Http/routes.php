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

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->get('/a', 'Controller@hello');



//routes for restful api
// $app->post('/signup', 'Controller@userSignup');
// $app->post('/login', 'Controller@userLogin');

// $app->post('/logout', 'Controller@userLogout');
// $app->post('/update', 'Controller@userUpdate');
// $app->post('/patientInsert', 'Controller@addPatient');
// $app->post('/changepassword', 'Controller@changePass');