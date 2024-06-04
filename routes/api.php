<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\EnsureTokenIsValid;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// Group Middleware
Route::middleware(['auth:sanctum'])->group(function () {

    // Get User
    Route::get('/user', [UsersController::class, 'getUser']);

    // Update User
    Route::put('/deviceid', [UsersController::class, 'updateDeviceId']);

    // Resolve P2P username
    Route::get('/transfer/username', [TransferController::class, "GetUsername"]);

    Route::get('/transfer', [TransferController::class, "FetchPaginatedHistory"]);

    // Transfer Username
    Route::post('/transfer/username', [TransferController::class, "TransferUsername"]);

    // Update Admin Config
    Route::put('/admin', [AdminController::class, "updateAdminConfig"]);

    // Suspend Transact Abilities
    Route::put('/admin/user/suspend_transact', [AdminController::class, "SuspendTokenFromTransacting"]);

});


// Store User
Route::post('/user', [UsersController::class, 'store']);

// Login User
Route::post('/login', [UsersController::class, 'login']);

// Forgot Password
Route::post('/request_otp_password_reset', [UsersController::class, 'requestResetPasswordOtp']);

// Forgot Password
Route::post('/request_otp_password_reset', [UsersController::class, 'requestResetPasswordOtp']);

// Password Reset
Route::post('/verify_otp_password_reset', [UsersController::class, 'verifyOtpPasswordReset']);


// Verify OTP
Route::post('/verify_otp', function (Request $request) {

    // Validate request parameters, including email and id
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'otp' => 'required|numeric'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 403,
            'message' => 'Bad request'
        ], 403);
    }

    // Retrieve user record from the database based on the provided ID
    $user = User::where("email", $request->input("email"))->first();
    if (!$user) {
        return response()->json([
            'status' => 403,
            'message' => 'Bad request'
        ], 403);
    }

    // Check if the provided email matches the user's email
    if ($user->email !== $request->email) {
        return response()->json([
            'status' => 400,
            'message' => 'Bad request'
        ], 400);
    }

    // Check if the user's email is verified
    if ($user->email_v_status != 1) {
        // Call emailOtpVerification method if the email is not verified
        return app(UsersController::class)->emailOtpVerification($request);
    } else {
        // Call verifyLoginOtp method if the email is verified
        return app(UsersController::class)->verifyLoginOtp($request);
    }
});


// Create pin
Route::post('/create_pin', [UsersController::class, 'createPin']);


// Create pin
Route::post('/verify_pin', [UsersController::class, 'verifyPin']);


// Test Push Notification
Route::get('/test/send_push', [ServicesController::class, 'SendPushNotification']);
