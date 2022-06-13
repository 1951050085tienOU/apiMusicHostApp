<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\SingerController;
use App\Http\Controllers\SongController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use App\Models\Singer;

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

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {

    //////// Auth
    Route::post('/logout', [AuthController::class, 'logout']); //logout user

    //////// Singer
    Route::post('/singer', [SingerController::class, 'store']); //store a new singer row

    Route::put('/singer/{singer}', [SingerController::class, 'update']); //update a singer row

    Route::delete('/singer/{singer}', [SingerController::class, 'delete']); //destroy a singer row

    //////// Song
    Route::post('/song', [SongController::class, 'store']);  //store a new song

    Route::put('/song/{song}', [SongController::class, 'update']); //update a song info

    Route::delete('song/{song}', [SongController::class, 'delete']); //destroy a song

    //////// Playlist
    Route::get('/playlist', [PlaylistController::class, 'index']); //get all  playlist of an user

    Route::get('/playlist/{playlist}', [PlaylistController::class, 'find']); // get a playlist

    Route::post('/playlist', [PlaylistController::class, 'store']);  //add a new playlist

    Route::put('/playlist/{playlist}', [PlaylistController::class, 'update']); //update playlist    

    Route::post('/playlist/{playlist}/{song}', [PlaylistController::class, 'storeASong']); //store a new song to playlist

    Route::delete('/playlist/{playlist}/{song}', [PlaylistController::class, 'deleteASong']); //delete a song from playlist

    Route::delete('/playlist/{playlist}', [PlaylistController::class, 'delete']); //destroy a playlist

});

// Public routes
//////////////////////////////// Auth
Route::post('/login', [AuthController::class, 'login']); //login existed acc
Route::post('/register', [AuthController::class, 'register']); //register new acc


//////////////////////////////// Singer
Route::get('/singer', [SingerController::class, 'index']); //get all singers information
 
Route::get('/singer/{singer}', [SingerController::class, 'find']); //get singer by Id


//////////////////////////////// Songs
Route::get('/song', [SongController::class, 'index']); //get all songs

Route::get('/song/{song}', [SongController::class, 'find']); // get a song




////////////////////// Default
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});