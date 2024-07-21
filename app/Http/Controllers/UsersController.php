<?php

namespace App\Http\Controllers;

use App\Mail\OTPMail;
use App\Models\AccessTokenModel;
use App\Models\AdminModel;
use App\Models\User;
use App\Models\VirtualAccountsModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class UsersController extends Controller
{

    /**
     * Get the authenticated user's details
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    // Generate Controllers
    protected $GenerateServices;

    // Api Services
    protected $ApiServices;

    public function __construct(GenerateController $GenerateServices, ApiServices $ApiServices)
    {
        $this->GenerateServices = $GenerateServices;
        $this->ApiServices = $ApiServices;
    }


    // Get User
    public function getUser(Request $request)
    {
        try {

            // Get the authenticated user
            $user = $request->user();

            // Check if token can read
            if (!$user->tokenCan('read')) {
                throw new Exception('Unauthorized', 401);
            }

            // Fetch wallets
            $getWallets = VirtualAccountsModel::where('user_id', $user->id)->get();

            // Return Response
            return response()->json([
                'status' => 200,
                'message' => 'User fetched successfully',
                'data' => [
                    'first_name' => $user->first_name,
                    'receive_email' => $user->receive_emails == '1' ? true : false,
                    'receive_push' => $user->receive_push == '1' ? true : false,
                    'last_name' => $user->last_name,
                    'balance' => $user->balance,
                    'phone_number' => $user->phone_number,
                    'username' => $user->username,
                    'email' => $user->email,
                    "id" => $user->id,
                    'level' => $user->level,
                    'promo_code' => $user->promo_code,
                    'created_at' => $user->created_at,
                    'token' => $user->token,
                    'device_id' => $user->device_id,
                    'wallet' => $getWallets,
                ]
            ], 200);

        } catch (Exception $th) {

            // Return Response
            return response()->json([
                'status' => 500,
                'message' => $th->getMessage()
            ], 500);

        }
    }


    // Store Users
    public function store(Request $request)
    {

        try {

            DB::beginTransaction();

            // Validate Request
            $validate = Validator::make($request->all(), [
                'first_name' => 'required|string|max:15',
                'last_name' => 'required|string|max:15',
                'phone_number' => 'required|numeric|unique:users|regex:/^([0-9\s\-\+\(\)]*)$/',
                'username' => 'required|unique:users|max:10|min:4|string|regex:/^[a-zA-Z0-9]+$/',
                'email' => 'required|email|unique:users|email:rfc,dns',
                'password' => 'required|min:6|max:20|string',
            ]);

            // Check Validation
            if ($validate->fails()) {
                throw new Exception($validate->errors()->first(), 400);
            }

            // Promo Code
            $promo_code = $this->GenerateServices->GeneratePromoCode();

            // Check admin onboard user status
            $admin = AdminModel::where('id', 1)->first();
            if ($admin->onboard_status == 0) {
                throw new Exception('Cannot onboard users at this time', 403);
            }

            // Create User
            DB::table('users')->insertOrIgnore([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone_number' => $request->phone_number,
                'username' => strtolower($request->username),
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'promo_code' => $promo_code,
                'added_by' => $request->added_by,
                'created_at' => now(),
            ]);


            // Get User
            $user = User::where('phone_number', $request->phone_number)->first();
            $token = $user->createToken('mobile-app', ['read'])->plainTextToken;


            // Create Notification
            DB::table('notifications')->insertOrIgnore([
                'user_id' => $user->id,
                'ref' => $this->GenerateServices->GenerateNotificationReference(),
                'message' => 'You are welcome to SwiftVend. We are glad to have you on board. Enjoy our services. Your Promo Code is ' . $promo_code . '. Share with friends to earn more.',
                'img' => getenv("APP_URL") . '/assets/img/logo.png',
            ]);

            DB::commit();

            // Update user
            $user->token = $token;
            $user->save();


            // Generate OTP
            $otp = $this->GenerateServices->GenerateOTP($user->id);

            // Send OTP to user
            Mail::to($user->email)->send(new OTPMail($otp));

            // Return Response
            return response()->json([
                'status' => 201,
                'message' => 'User Created Successfully',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ], 201);


        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            // Return Response
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ], $e->getCode());
        }

    }


    // Update User
    public function updateDeviceId(Request $request)
    {

        try {

            // Get the authenticated user
            $user = $request->user();

            // Validate Request
            $validate = Validator::make($request->all(), [
                'device_id' => 'required|string',
            ]);

            // Check Validation
            if ($validate->fails()) {
                throw new Exception($validate->errors()->first(), 400);
            }

            // Remove existing device id
            User::where('device_id', $request->device_id)->update([
                'device_id' => ""
            ]);

            // Update User
            User::where('id', $user->id)->update([
                'device_id' => $request->device_id
            ]);


            // Return Response
            return response()->json([
                'status' => 200,
                'message' => 'DeviceID updated successfully',
            ], 200);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            // Return Response
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ], $e->getCode());
        }

    }


    // Login User
    public function login(Request $request)
    {
        try {

            // Validate Request
            $validate = Validator::make($request->all(), [
                'email' => 'required|email|email:rfc,dns',
                'password' => 'required|min:6|max:20|string',
            ]);

            // Check Validation
            if ($validate->fails()) {
                throw new Exception($validate->errors()->first(), 400);
            }

            // Check Password and authenticate user
            if (!Auth::attempt($request->only('email', 'password'))) {
                throw new Exception('Email or Password is incorrect', 401);
            }

            // Check if user exists
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                throw new Exception('Bad request', 403);
            }

            // Generate OTP
            $otp = $this->GenerateServices->GenerateOTP($user->id);

            // Send OTP to user
            Mail::to($user->email)->send(new OTPMail($otp));

            // Return Response
            return response()->json([
                'status' => 200,
                'message' => 'User Logged In Successfully',
                'data' => [
                    'id' => $user->id,
                    'token' => $user->token
                ]
            ], 200);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            // Return Response
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }


    // Request reset password otp
    public function requestResetPasswordOtp(Request $request)
    {
        try {

            // Validate Request
            $validate = Validator::make($request->all(), [
                'email' => 'required|email|email:rfc,dns',
            ]);

            // Check Validation
            if ($validate->fails()) {
                throw new Exception($validate->errors()->first(), 400);
            }

            // Check if user exists
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                throw new Exception('Email could not be found', 404);
            }

            // Generate OTP
            $otp = $this->GenerateServices->GenerateOTP($user->id);

            // Send OTP to user
            Mail::to($user->email)->send(new OTPMail($otp));

            // Return Response
            return response()->json([
                'status' => 200,
                'message' => 'OTP sent successfully',
                'data' => [
                    'otp' => $otp
                ]
            ], 200);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            // Return Response
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }


    // Password Reset
    public function verifyOtpPasswordReset(Request $request)
    {
        try {

            // Validate Request
            $validate = Validator::make($request->all(), [
                'password' => 'required|min:6|max:20|string',
                'email' => 'required|email|email:rfc,dns',
                'otp' => 'required|numeric',
            ]);

            // Check Validations
            if ($validate->fails()) {
                throw new Exception($validate->errors()->first(), 400);
            }

            // Check if user exists
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                throw new Exception('Email could not be found', 404);
            }

            // Check if OTP updated at is less than 5 minutes
            if (now()->diffInMinutes($user->otp_updated_at) > 5) {
                throw new Exception('OTP has expired. Request a new one.', 400);
            }

            // Check if OTP is valid
            if (!password_verify($request->otp, $user->otp)) {
                throw new Exception('Invalid OTP', 400);
            }

            // Update Password
            User::where('email', $request->email)->update([
                'password' => bcrypt($request->password)
            ]);

            // Return Response
            return response()->json([
                'status' => 200,
                'message' => 'Password reset successfully',
            ], 200);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            // Return Response
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }


    // Verify OTP
    public function verifyLoginOtp(Request $request)
    {
        try {

            // Validate Request
            $validate = Validator::make($request->all(), [
                'otp' => 'required|numeric',
                'email' => 'required|email|email:rfc,dns',
            ]);

            // Check Validations
            if ($validate->fails()) {
                throw new Exception($validate->errors()->first(), 400);
            }

            // Check if user exists
            $user = User::where('email', $request->input('email'))->first();
            if (!$user) {
                throw new Exception('Email could not be found', 404);
            }

            // Check if OTP updated at is less than 5 minutes
            if (now()->diffInMinutes($user->otp_updated_at) > 5) {
                throw new Exception('OTP has expired. Request a new one.', 400);
            }

            // Check if OTP is valid
            if (!password_verify($request->input('otp'), $user->otp)) {
                throw new Exception('Invalid OTP', 400);
            }

            // Return Response
            return response()->json([
                'status' => 200,
                'message' => 'OTP verified successfully',
                'data' => [
                    'id' => $user->id,
                    'token' => $user->token
                ]
            ], 200);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            // Return Response
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }


    // Verify OTP
    public function emailOtpVerification(Request $request)
    {

        try {

            // Validate Request
            $validate = Validator::make($request->all(), [
                'otp' => 'required|numeric',
                'email' => 'required|email|email:rfc,dns',
            ]);

            // Check Validations
            if ($validate->fails()) {
                throw new Exception($validate->errors()->first(), 400);
            }

            // Check if user exists
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                throw new Exception('Email could not be found', 404);
            }

            // Check if OTP updated at is less than 5 minutes
            if (now()->diffInMinutes($user->otp_updated_at) > 5) {
                throw new Exception('OTP has expired. Request a new one.', 400);
            }

            // Check if OTP is valid
            if (!password_verify($request->otp, $user->otp)) {
                throw new Exception('Invalid OTP', 400);
            }

            // Verify user
            $user->status = 1;
            $user->email_v_status = 1;
            $user->save();

            // Update token ability
            $accessToken = AccessTokenModel::where('tokenable_id', $user->id)->first();
            $accessToken->update([
                'abilities' => ['read', 'transact']
            ]);

            // Return Response
            return response()->json([
                'status' => 200,
                'message' => 'OTP verified successfully',
                'data' => [
                    'id' => $user->id,
                    'token' => $user->token
                ]
            ], 200);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            // Return Response
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ], $e->getCode());
        }

    }


    // Create Pin
    public function createPin(Request $request)
    {
        try {

            // Validate Request
            $validate = Validator::make($request->all(), [
                'pin' => 'required|numeric',
                'email' => 'required|email|email:rfc,dns',
            ]);

            // Check Validations
            if ($validate->fails()) {
                throw new Exception($validate->errors()->first(), 400);
            }

            // Check if user exists
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                throw new Exception('Email could not be found', 404);
            }

            // Update Pin
            User::where('email', $request->email)->update([
                'pin' => bcrypt($request->pin)
            ]);

            // Return Response
            return response()->json([
                'status' => 200,
                'message' => 'Pin created successfully',
                'data' => [
                    'id' => $user->id,
                    'token' => $user->token
                ]
            ], 200);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            // Return Response
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }


    // Verify Pin
    public function verifyPin(Request $request)
    {
        try {

            // Validate Request
            $validate = Validator::make($request->all(), [
                'pin' => 'required|numeric',
                'id' => 'required|numeric',
            ]);

            // Check Validations
            if ($validate->fails()) {
                throw new Exception($validate->errors()->first(), 400);
            }

            // Check if user exists
            $user = User::where('id', $request->id)->first();
            if (!$user) {
                throw new Exception('Account not found', 404);
            }

            // Check if Pin is valid
            if (!password_verify($request->pin, $user->pin)) {
                throw new Exception('Incorrect pin. Try again', 400);
            }

            // Return Response
            return response()->json([
                'status' => 200,
                'message' => 'Pin verified successfully',
            ], 200);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            // Return Response
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }


    // Update User Preferences
    public function UpdatePreferences(Request $request){
        try {

            // Validate Request
            $validate = Validator::make($request->all(), [
                'type' => 'required|string',
                'value' => 'required|string',
                'id' => 'required|exists:users,id',
            ]);

            // Check Validations
            if ($validate->fails()) {
                throw new Exception($validate->errors()->first(), 400);
            }

            $allowedAttributes = ['receive_emails', 'receive_push']; // Add the attributes you want to allow
            $attribute = $request->type;

            if (!in_array($attribute, $allowedAttributes)) {
                throw new Exception("Bad request!", 400);
            }

            // Check if user exists
            $user = User::where('id', $request->id)->first();
            if (!$user) {
                throw new Exception('Account not found', 404);
            }



            $user->update([$request->input('type') => $request->input('value') ]);
            

            // Return Response
            return response()->json([
                'status' => 200,
                'message' => 'Preference updated successfully',
            ], 200);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            // Return Response
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }


    // Generate Virtual account
    public function ReserveVirtualAccount (Request $request){

        try {

            // Get the authenticated user
            $user = $request->user();

            // Check if token can read
            if (!$user->tokenCan('transact')) {
                return response()->json([
                    'status' => 401,
                    'message' => "Bad request"
                ], 401);
            }

            // Validate Request
            $validate = Validator::make($request->all(), [
                'tier' => 'required|string'
            ]);


            // Check Validations
            if ($validate->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' => $validate->errors()->first()
                ], 400);
            }


            // Generate account reference
            $txf = $this->GenerateServices->GenerateAccountReference();

            // Generate account
            if($request->tier === "1"){

                // Check if customer has not generated tier 1 account before.
                $check = VirtualAccountsModel::where("tier", '1')
                    ->where("user_id", $user->id)
                    ->lockForUpdate()->
                    first();

                if($check){
                    return response()->json([
                        'status' => 400,
                        'message' => "Bad request"
                    ], 400);
                }

                $Generate = $this->ApiServices->CreateVirtualAccountFlutterwave($user->id, $user->email, $user->first_name, $user->last_name, $user->phone_number, $txf);
                // If fails
                if(!$Generate['status']){
                    return response()->json([
                        'status' => 403,
                        'message' => $Generate['message']
                    ], 403);
                }

            }elseif($request->tier === "2"){

                // Check if customer has not generated tier 1 account before.
                $check = VirtualAccountsModel::where("tier", '2')
                    ->where("user_id", $user->id)
                    ->first();

                if($check){
                    return response()->json([
                        'status' => 400,
                        'message' => "Bad request"
                    ], 400);
                }

                return response()->json([
                    'status' => 500,
                    'message' => "Currently not available."
                ], 500);

            }else{
                return response()->json([
                    'status' => 400,
                    'message' => "Bad request"
                ], 400);
            }

            // Return Response
            return response()->json([
                'status' => 200,
                'message' => 'Account created successfully',
            ], 200);

        } catch (Exception $th) {

            // Return Response
            return response()->json([
                'status' => 500,
                'message' => "Something went wrong!"
            ], 500);

        }

    }



}
