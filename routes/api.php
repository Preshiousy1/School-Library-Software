<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ManagerController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'prefix' => 'student'
], function ($router) {
    Route::post('/login', [StudentController::class, 'login']);
    Route::post('/register', [StudentController::class, 'register']);
    Route::post('/logout', [StudentController::class, 'logout']);
    Route::post('/refresh', [StudentController::class, 'refresh']);
    Route::get('/profile', [StudentController::class, 'userProfile']);


    Route::post('/books/{book_id}/request', [StudentController::class, 'requestBook']);
    Route::post('/books/{request_id}/return', [StudentController::class, 'returnBook']);
    Route::get('/books/borrowed', [StudentController::class, 'getBorrowedBooks']);

    Route::get('/books', [BookController::class, 'getAvailableBooks']);
    Route::get('/books/search/', [BookController::class, 'searchBooks']);
});

Route::group([
    'prefix' => 'admin'
], function ($router) {
    Route::post('/login', [AdminController::class, 'login']);
    Route::post('/logout', [AdminController::class, 'logout']);
    Route::post('/refresh', [AdminController::class, 'refresh']);
    Route::get('/profile', [AdminController::class, 'userProfile']);

    Route::post('/createAdmin', [AdminController::class, 'createAdmin']);
    Route::post('/createManager', [AdminController::class, 'createManager']);
    Route::delete('/deleteManager/{manager_id}', [AdminController::class, 'deleteManager']);
    Route::post('/createBook', [AdminController::class, 'createBook']);


    Route::get('/managers', [AdminController::class, 'getManagers']);
    Route::get('/students', [AdminController::class, 'getStudents']);
});


Route::group([
    'prefix' => 'manager'
], function ($router) {
    Route::post('/login', [ManagerController::class, 'login']);
    Route::post('/logout', [ManagerController::class, 'logout']);
    Route::post('/refresh', [ManagerController::class, 'refresh']);
    Route::get('/profile', [ManagerController::class, 'userProfile']);

    Route::get('/books', [ManagerController::class, 'getBooks']);
    Route::post('/books/setAvailable/{book_id}', [ManagerController::class, 'setBookAvailability']);

    Route::get('/book_requests', [ManagerController::class, 'getBookRequests']);
    Route::post('/book_requests/setApproval/{request_id}', [ManagerController::class, 'setBookRequestApproval']);



    Route::get('/students', [ManagerController::class, 'getStudents']);
    Route::post('/students/setSuspend/{student_id}', [ManagerController::class, 'setStudentSuspendStatus']);
});
