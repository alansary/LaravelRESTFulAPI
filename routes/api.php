<?php

use Illuminate\Http\Request;

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

// User Routes
Route::get('/user/getAll', 'UsersController@getAll');
Route::get('/user/id', 'UsersController@getById');
Route::get('/user/username', 'UsersController@getByUsername');
Route::post('/user/insert', 'UsersController@insert');
Route::post('/user/update/id', 'UsersController@updateId');
Route::post('/user/update/username', 'UsersController@updateUsername');
Route::post('/user/update/password', 'UsersController@updatePassword');
Route::post('/user/update/created', 'UsersController@updateCreated');
Route::post('/user/update/modified', 'UsersController@updateModified');
Route::post('/user/delete/id', 'UsersController@deleteById');
Route::post('/user/delete/username', 'UsersController@deleteByUsername');

// Work Routes
Route::get('/work/getAll', 'WorksController@getAll');
Route::get('/work/id', 'WorksController@getById');
Route::get('/work/user_id', 'WorksController@getByUserId');
Route::post('/work/insert', 'WorksController@insert');
Route::post('/work/update/id', 'WorksController@updateId');
Route::post('/work/update/user_id', 'WorksController@updateUserId');
Route::post('/work/update/description', 'WorksController@updateDescription');
Route::post('/work/update/created', 'WorksController@updateCreated');
Route::post('/work/update/modified', 'WorksController@updateModified');
Route::post('/work/delete/id', 'WorksController@deleteById');
Route::post('/work/delete/user_id', 'WorksController@deleteByUserId');

// Milestone Routes
Route::get('/milestone/getAll', 'MilestonesController@getAll');
Route::get('/milestone/id', 'MilestonesController@getById');
Route::get('/milestone/user_id', 'MilestonesController@getByUserId');
Route::get('/milestone/work_id', 'MilestonesController@getByWorkId');
Route::post('/milestone/insert', 'MilestonesController@insert');
Route::post('/milestone/update/id', 'MilestonesController@updateId');
Route::post('/milestone/update/deliverables', 'MilestonesController@updateDeliverables');
Route::post('/milestone/update/payment', 'MilestonesController@updatePayment');
Route::post('/milestone/update/work_id', 'MilestonesController@updateWorkId');
Route::post('/milestone/update/deadline', 'MilestonesController@updateDeadline');
Route::post('/milestone/update/image', 'MilestonesController@updateImage');
Route::post('/milestone/update/created', 'MilestonesController@updateCreated');
Route::post('/milestone/update/modified', 'MilestonesController@updateModified');
Route::post('/milestone/delete/id', 'MilestonesController@deleteById');
Route::post('/milestone/delete/work_id', 'MilestonesController@deleteByWorkId');
Route::post('/milestone/delete/user_id', 'MilestonesController@deleteByUserId');
