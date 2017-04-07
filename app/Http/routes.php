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
$app->post('/signup', 'Controller@userSignup');
$app->post('/login', 'Controller@userLogin');

$app->post('/logout', 'Controller@userLogout');
$app->post('/update', 'Controller@userUpdate');
$app->post('/patientInsert', 'Controller@addPatient');
$app->post('/changepassword', 'Controller@changePass');

$app->post('/addCompany', 'Controller@add_company');

$app->post('/getCompany', 'Controller@get_company');
$app->post('/getselectedCompany', 'Controller@get_select_company');
$app->post('/editselectedCompany', 'Controller@edit_selected_company');
$app->post('/addtrancoadmin', 'Controller@add_tranco_admin');
$app->post('/gettrancoadmin', 'Controller@get_tranco_admin');
$app->post('/forgetpass', 'Controller@forgot_password');

$app->post('/getselectedtrancoadmin', 'Controller@get_selected_tranco_admin');
$app->post('/editselectedtrancoadmin', 'Controller@edit_selected_tranco_admin');

$app->post('/adddoctor', 'Controller@add_doctor');
$app->post('/getdoctor', 'Controller@get_doctor');
$app->post('/getselecteddoctor', 'Controller@get_selected_doctor');
$app->post('/editselecteddoctor', 'Controller@edit_selected_doctor');

$app->post('/addtranscriber', 'Controller@add_transcriber');

$app->post('/gettranscriber', 'Controller@get_transcriber');
$app->post('/getselectedtranscriber', 'Controller@get_selected_transcriber');
$app->post('/editselectedtranscriber', 'Controller@edit_selected_transcriber');

$app->post('/addqa', 'Controller@add_qa');
$app->post('/getqa', 'Controller@get_qa');
$app->post('/getselectedqa', 'Controller@get_selected_qa');
