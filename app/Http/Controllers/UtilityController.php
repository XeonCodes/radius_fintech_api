<?php

namespace App\Http\Controllers;

use App\Models\AdminModel;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UtilityController extends Controller
{
    /**
     * Get the authenticated user's details
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

     protected $bundlePlansController;
     protected $generateController;

     protected $apiServices;

    public function __construct(GenerateController $generateController, BundlePlansController $bundlePlansController, ApiServices $apiServices)
    {
        $this->bundlePlansController = $bundlePlansController;
        $this->generateController = $generateController;
        $this->apiServices = $apiServices;
    }


    // Get Data Plans
    public function GetDataPlans(Request $request){
    
        try {

            // Get the authenticated user
            $user = $request->user();

            // Check if token can read
            if (!$user->tokenCan('transact')) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Bad request'
                ], 401);
            }

            $plans = $user->account_type === "api" ? $this->bundlePlansController->DataPlansUserApi() : $this->bundlePlansController->DataPlansUser();

            // Return Response
            return response()->json([
                'status' => 200,
                'message' => 'Data bundle plans fetched successfully',
                'data' => $plans
            ], 200);

        } catch (Exception $th) {

            // Return Response
            return response()->json([
                'status' => 500,
                'message' => $th->getMessage()
            ], 500);

        }

    }


    // Buy Airtime
    public function BuyAirtime(Request $request){

        try {

            // Get the authenticated user
            $user = $request->user();

            // Validate input
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:50', 
                'phone_number' => 'required|string|regex:/^0[789][01]\d{8}$/',
                'network' => 'required|string|in:MTN,AIRTEL,GLO,9MOBILE'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Check if token can read
            if (!$user->tokenCan('transact')) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Bad request'
                ], 401);
            }

            $admin = AdminModel::where('id', "1")->first();
            if(!$admin){
                return response()->json([
                    'status' => 500,
                    'message' => 'All services are down'
                ], 500);
            }

            if($admin->airtime_status == 0){
                return response()->json([
                    'status' => 500,
                    'message' => 'Airtime sales is temporarily onhold'
                ], 500);
            }

            if($admin->all_services_status == 0){
                return response()->json([
                    'status' => 500,
                    'message' => 'All sales are temporarily onhold'
                ], 500);
            }

            try {

                // Begin Transaction
                DB::beginTransaction();
    
                // Check if balance is sufficient
                $beneficiary = User::where('id', $user->id)->lockForUpdate()->first();

                if ($beneficiary->balance < $request->input("amount")) {
                    return response()->json([
                        'status' => 403,
                        'message' => 'Insufficient balance.'
                    ], 403);
                }
    
                // Deduct sender balance
                $percent = env("AIRTIME_CASHBACK") / 100;
                $percent = $percent * $request->input('amount');
                $cash_back = $user->account_type == 'api' ? $percent : 0;
                $amount_charged = $request->input('amount') - $cash_back;

                $beneficiary->balance -= $amount_charged;
                $beneficiary->save();
    
                $txf = $this->generateController->GenerateTransferTransactionReference();
                $created_at = now();

                // Create transaction (sender)
                DB::table('transactions')->insertOrIgnore([
                    "customer_id" => $user->id,
                    "type" => "debit",
                    "txf" => $txf,
                    "amount" => $amount_charged,
                    "fee" => 0,
                    'bonus' => $percent,
                    "balance_before" => $user->balance,
                    "balance_after" => $beneficiary->balance,
                    "trans_type" => "airtime",
                    "beneficiary" => $request->input('phone_number'),
                    "status" => "pending",
                    "narration" => $request->amount . " naira airtime purchase processed",
                    "main_type" => "airtime",
                    "created_at" => $created_at,
                    "updated_at" => $created_at,
                ]);
    
                DB::commit();

            } catch (Exception $th) {
                DB::rollBack();
                return response()->json([
                    'status' => 500,
                    'message' => 'An error occurred while processing your request.'
                ], 500);
    
            }

            // Buy Airtime
            $buy = $this->apiServices->BuyAirtime1($request->input('amount'), $request->input('phone_number'), $request->input("network"), $this->generateController->GenerateUtilityReference());
            if(!$buy['status']){
                return response()->json([
                    'status' => 500,
                    'message' => $buy['message']
                ], 500);
            }

            // Return successful response
            return response()->json([
                "status" => 200,
                "message" => "Airtime purchase processed",
                "data" => [
                    "status" => "pending",
                    "amount" => $request->input("amount"),
                    "txf" => $txf
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);
        }

        
    }


}
