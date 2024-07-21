<?php

namespace App\Http\Controllers;

use App\Models\AccessTokenModel;
use App\Models\AdminModel;
use App\Models\TransactionsModel;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class TransferController extends Controller
{

    /**
     * Get the authenticated user's details
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    protected $GenerateServices;
    protected $Services;

    protected $Checks;

    public function __construct(GenerateController $GenerateServices, ServicesController $services, CheckController $Checks)
    {
        $this->GenerateServices = $GenerateServices;
        $this->Services = $services;
        $this->Checks = $Checks;
    }

    /* == 
        Get 
        Username
    ==*/

    // Resolve Username
    public function GetUsername(Request $request){

        
        // Validate input
        $validator = Validator::make($request->all(), [
            'username' => 'required|max:10|min:4|string|regex:/^[a-zA-Z0-9]+$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {

            // Get the authenticated user
            $user = $request->user();

            // Check if user has transact ability
            if (!$user->tokenCan('transact')) {
                return response()->json([
                    'status' => 403,
                    'message' => 'You cannot transfer at this time'
                ], 403);
            }

            // Retrieve admin settings from cache
            $admin = Cache::remember('admin_settings', 60, function () {
                return AdminModel::find(1);
            });

            if (!$admin) {
                return response()->json([
                    'status' => 503,
                    'message' => 'Services are temporarily disabled.'
                ], 503);
            }

            // Check if all services are enabled
            if ($admin->all_services_status != 1) {
                return response()->json([
                    'status' => 503,
                    'message' => 'Services are temporarily disabled.'
                ], 503);
            }

            // Check if transfer service is enabled
            if ($admin->transfer_status != 1) {
                return response()->json([
                    'status' => 503,
                    'message' => 'Transfers are temporarily disabled.'
                ], 503);
            }

            // Check if not sending to self
            if (strtolower($request->username) == strtolower($user->username)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'You cannot send to self.'
                ], 400);
            }

            // Resolve username
            $targetUser = User::where("username", strtolower($request->username))->first();
            if (!$targetUser) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Account could not be found.'
                ], 404);
            }

            // Check if user status is 1
            if ($targetUser->status != 1) {
                return response()->json([
                    'status' => 403,
                    'message' => "Invalid receiver username"
                ], 403);
            }

            // Return successful response
            return response()->json([
                "status" => 200,
                "message" => "Username resolved successfully",
                "data" => [
                    "fullname" => $targetUser->first_name . " " . $targetUser->last_name
                ]
            ], 200);

        } catch (Exception $th) {
            // Log the exception
            Log::error('Error resolving username', ['exception' => $th]);

            // Return generic error response to avoid exposing sensitive information
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while processing your request.'
            ], 500);
        }

    }


    // Get Bank Account
    public function GetBankAccount(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'bank_code' => 'required|string|max:10|regex:/^[0-9]+$/',
            'account_number' => 'required|string|digits:10|regex:/^[0-9]+$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {

            // Get the authenticated user
            $user = $request->user();

            // Check if user has transact ability
            if (!$user->tokenCan('transact')) {
                return response()->json([
                    'status' => 403,
                    'message' => 'You cannot transfer at this time'
                ], 403);
            }

            // Retrieve admin settings from cache
            $admin = Cache::remember('admin_settings', 60, function () {
                return AdminModel::find(1);
            });

            if (!$admin) {
                return response()->json([
                    'status' => 503,
                    'message' => 'Services are temporarily disabled.'
                ], 503);
            }

            // Check if all services are enabled
            if ($admin->all_services_status != 1) {
                return response()->json([
                    'status' => 503,
                    'message' => 'Services are temporarily disabled.'
                ], 503);
            }

            // Check if transfer service is enabled
            if ($admin->transfer_status != 1) {
                return response()->json([
                    'status' => 503,
                    'message' => 'Transfers are temporarily disabled.'
                ], 503);
            }

            // Resolve username
            $targetUser = User::where("username", strtolower($request->username))->first();
            if (!$targetUser) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Account could not be found.'
                ], 404);
            }

            // Check if user status is 1
            if ($targetUser->status != 1) {
                return response()->json([
                    'status' => 403,
                    'message' => "Invalid receiver username"
                ], 403);
            }

            // Return successful response
            return response()->json([
                "status" => 200,
                "message" => "Username resolved successfully",
                "data" => [
                    "fullname" => $targetUser->first_name . " " . $targetUser->last_name
                ]
            ], 200);

        } catch (Exception $th) {
            // Log the exception
            Log::error('Error resolving username', ['exception' => $th]);

            // Return generic error response to avoid exposing sensitive information
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while processing your request.'
            ], 500);
        }
    }



    // Transfer to username
    public function TransferUsername(Request $request)
    {

        // Validate input
        $validator = Validator::make($request->all(), [
            'username' => 'required|max:10|min:4|string|regex:/^[a-zA-Z0-9]+$/',
            'amount' => 'required|numeric|min:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {

            // Begin Transaction
            DB::beginTransaction();


            // Get the authenticated user
            $user = $request->user();

            // Check if user has transact ability
            if (!$user->tokenCan('transact')) {
                return response()->json([
                    'status' => 403,
                    'message' => 'You cannot transfer at this time'
                ], 403);
            }

            // Retrieve admin settings from cache
            $admin = Cache::remember('admin_settings', 60, function () {
                return AdminModel::find(1);
            });

            if (!$admin) {
                return response()->json([
                    'status' => 503,
                    'message' => 'Services are temporarily disabled.'
                ], 503);
            }

            // Check if all services are enabled
            if ($admin->all_services_status != 1) {
                return response()->json([
                    'status' => 503,
                    'message' => 'Services are temporarily disabled.'
                ], 503);
            }

            // Check if transfer service is enabled
            if ($admin->transfer_status != 1) {
                return response()->json([
                    'status' => 503,
                    'message' => 'Transfers are temporarily disabled.'
                ], 503);
            }

            // Check if not sending to self
            if (strtolower($request->username) == strtolower($user->username)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'You cannot send to self.'
                ], 400);
            }

            // Resolve username
            $targetUser = User::where("username", strtolower($request->username))->first();
            if (!$targetUser) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Account could not be found.'
                ], 404);
            }

            // Check if receiver status is 1
            if ($targetUser->status != 1) {
                return response()->json([
                    'status' => 403,
                    'message' => "Invalid receiver username"
                ], 403);
            }

            // Check if balance is sufficient
            $sender = User::where('id', $user->id)->lockForUpdate()->first();
            if ($sender->balance < $request->input("amount")) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Insufficient balance.'
                ], 403);
            }


            // Check for daily limits
            $dailyLimit = $this->Checks->CheckDailyLimit($user->id, $request->input("amount"));
            if (!$dailyLimit['status']) {
                return response()->json([
                    'status' => 403,
                    'message' => $dailyLimit['message']
                ], 403);
            }

            // Deduct sender balance
            $sender->balance -= $request->input("amount");
            $sender->save();

            $targetUserBalanceBefore = $targetUser->balance;

            // Credit username
            $targetUser->balance += $request->input("amount");
            $targetUser->save();

            $txf = $this->GenerateServices->GenerateTransferTransactionReference();
            $created_at = now();

            // Create transaction (receiver)
            DB::table('transactions')->insertOrIgnore([
                "customer_id" => $targetUser->id,
                "type" => "credit",
                "txf" => $txf,
                "amount" => $request->input("amount"),
                "fee" => 0,
                "balance_before" => $targetUserBalanceBefore,
                "balance_after" => $targetUser->balance,
                "trans_type" => "transfer_p2p",
                "username" => $user->username,
                "account_type" => $targetUser->account_type,
                "status" => "successful",
                'sender' => "",
                "description" => $request->input("description"),
                "narration" => "From: " . $sender->first_name . " " . $sender->last_name,
                "account_name" => $user->first_name . " " . $user->last_name,
                "main_type" => "transfer",
                "created_at" => $created_at,
                "updated_at" => $created_at,
            ]);


            // Create transaction (sender)
            DB::table('transactions')->insertOrIgnore([
                "customer_id" => $user->id,
                "type" => "debit",
                "txf" => $txf,
                "amount" => $request->input("amount"),
                "fee" => 0,
                'sender' => $user->first_name . " " . $user->last_name,
                "balance_before" => $user->balance,
                "balance_after" => $sender->balance,
                "trans_type" => "transfer_p2p",
                "account_type" => $user->account_type,
                "username" => $targetUser->username,
                "status" => "successful",
                "description" => $request->input("description"),
                "narration" => "To: " . $targetUser->first_name . " " . $targetUser->last_name,
                "account_name" => $targetUser->first_name . " " . $targetUser->last_name,
                "main_type" => "transfer",
                "created_at" => $created_at,
                "updated_at" => $created_at,
            ]);


            // Send Email (Sender and Receiver)

            // Create Notification (Sender)
            DB::table('notifications')->insertOrIgnore([
                "user_id" => $user->id,
                "type" => "transferp2p",
                "message" => "You have successfully sent " . number_format($request->input("amount"), 2, '.', '') . " to " . strtoupper($request->input("username")),
                "ref" => $txf,
                "created_at" => $created_at
            ]);

            DB::commit();

            // Send Push to receiver (Receiver)
            $targetUser->receive_push == 1 && $this->Services->SendPushNotification($targetUser->device_id, "Your account have been successfully credited with the sum of NGN" . number_format($request->input("amount"), 2, '.', ''), "Credit Alert");
            

            // Return successful response
            return response()->json([
                "status" => 200,
                "message" => "Transfer processed",
                "data" => [
                    "status" => "successful",
                    "amount" => $request->input("amount"),
                    "txf" => $txf,
                    'fee' => 0,
                    "account_name" => $targetUser->first_name . " " . $targetUser->last_name,
                    'created_at' => $created_at,
                    "narration" => "To: " . $targetUser->first_name . " " . $targetUser->last_name,
                    'beneficiary_id' => $targetUser->username,
                    'destination' => $request->input("description"),
                    'bank_name' => "",
                    'sender' => $user->first_name . " " . $user->last_name,
                    "username" => $targetUser->username,
                    'balance_after' => $sender->balance,
                    'trans_type' => "transfer_p2p",
                    "main_type" => "transfer",
                    'description' => $request->input("description")
                ]
            ], 200);

        } catch (Exception $th) {
            DB::rollBack();
            // Log the exception
            Log::error('Error resolving username', ['exception' => $th]);
            // Return generic error response to avoid exposing sensitive information
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while processing your request.'
            ], 500);

        }


    }


    // Fetch Paginated Transaction History
    public function FetchPaginatedHistory(Request $request)
    {

        try {

            // Get the authenticated user
            $user = $request->user();

            // Set the number of items per page, default to 10
            $perPage = $perPage = 10;

            // Fetch transactions for the authenticated user with pagination
            $transactions = TransactionsModel::where('customer_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            // Check if no transactions were found
            if ($transactions->isEmpty()) {
                return response()->json([
                    "status" => 200,
                    "message" => "Not found",
                    "page" => $transactions->total()
                ], 200);
            }

            // Return paginated response as JSON
            return response()->json([
                "status" => 200,
                "message" => "History fetched successfully",
                "data" => $transactions->items()
            ], 200);

        } catch (Exception $e) {
            // Handle any exceptions that occur
            return response()->json([
                "status" => 200,
                "message" => "An error occurred while fetching the transactions.",
                "error" => $e->getMessage(),
            ], 200);
        }

    }



}
