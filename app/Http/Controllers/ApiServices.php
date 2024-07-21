<?php

namespace App\Http\Controllers;

use App\Models\TransactionsModel;
use App\Models\User;
use App\Models\VirtualAccountsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiServices extends Controller
{


    protected $GenerateServices;
    protected $Services;

    protected $Checks;

    public function __construct(GenerateController $GenerateServices, ServicesController $services, CheckController $Checks)
    {
        $this->GenerateServices = $GenerateServices;
        $this->Services = $services;
        $this->Checks = $Checks;
    }


    // Resolve Bank (1)
    public function ResolveBankHaven($code, $number, $token)
    {

        $post = Http::withHeaders([
            "ClientID" => env("HAVEN_CLIENT_ID"),
            "content-type" => "application/json",
            "accept" => "application/json",
            "Authorization" => "Bearer " . $token
        ])->post(env("HAVEN_BASE_URL") . "/transfers/name-enquiry", [

                ]);

        $postJson = $post->json();

        if ($postJson['statusCode'] == 200) {

        } else {

        }

    }

    // Create Virtual account (Flutterwave)
    public function CreateVirtualAccountFlutterwave($id, $email, $first_name, $last_name, $phone_number, $txf)
    {

        // Make API call to create a virtual account number
        $responses = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('FLW_SEC_KEY'),
            'Content-Type' => 'application/json',
        ])->post(
                'https://api.flutterwave.com/v3/virtual-account-numbers',
                [
                    "email" => $email,
                    "is_permanent" => true,
                    "bvn" => "22366804906",
                    "tx_ref" => $txf,
                    "phonenumber" => $phone_number,
                    "firstname" => $first_name,
                    "lastname" => $last_name,
                    "narration" => $last_name . " " . $first_name
                ]
            );
        
            
        $jsonResponse = $responses->json();

        // Check if the API request was successful
        if ($responses->successful()) {

            $accountNumber = $jsonResponse['data']['account_number'];
            $bank_name = $jsonResponse['data']['bank_name'];

            DB::table('virtual_account')->insertOrIgnore([
                "user_id" => $id,
                "account_reference" => $txf,
                "account_number" => $accountNumber,
                "bank_name" => $bank_name,
                "account_name" => $last_name . " " . $first_name,
                "tier" => "1",
                "created_at" => now()
            ]);


            return [
                'status' => true,
                'message' => "Created successfully"
            ];

            
        } else {
            return [
                'status' => false,
                'message' => $jsonResponse->message
            ];
        }

    }


    // Hooks
    public function FlwHook(Request $request)
    {

        $secretHash = env('FLW_HASH');
        $signature = $request->header('verif-hash');

        $payload = $request->all();
        Log::info($payload);

        // if (!$signature || !hash_equals($signature, $secretHash)) {
        //     abort(401);
        // }

        // Ensure 'tx_ref' exists in the request data
        if (!isset($request->data['tx_ref'])) {
            exit();
        }

        // Check if transaction reference already exist
        $check = TransactionsModel::where('x_ref', $request->data['id'])->first();
        if ($check) {
            Log::info('Not on Radius');
            abort(401);
        }

        // Check if event was bank deposit
        if ($request->event !== "charge.completed") {
            abort(400);
        }

        // Verify transaction
        $verify = Http::withHeaders([
            "Content-Type" => "application/json",
            "Authorization" => env("FLW_SEC_KEY")
        ])->get("https://api.flutterwave.com/v3/transactions/{$request->data['id']}/verify");

        $verifyJson = $verify->json();
        if ($verifyJson['status'] !== "success") {
            abort(400);
        }

        // Create Transaction for incoming transaction
        if ($request->data['status'] === 'successful') {

            // Get the customer to whom the money was sent to
            $account_ref = VirtualAccountsModel::where('account_reference', $request->data['tx_ref'])->first();
            if (!$account_ref) {
                abort(404);
            }

            $beneficiary = User::where('id', $account_ref->user_id)->first();

            // Calculate amount to credit the customer
            $sent_amount = $request->data['amount'] - $request->data['app_fee'];

            $old_balance = $beneficiary->balance;
            $new_balance = $beneficiary->balance + $sent_amount;

            // Credit the customer
            $beneficiary->balance += $sent_amount;
            $beneficiary->save();

            // Create Transaction in transaction table
            DB::table('transactions')->insertOrIgnore([
                'type' => "credit",
                'customer_id' => $beneficiary->id,
                'txf' => $request->data['tx_ref'],
                'x_ref' => $request->data['id'],
                'balance_before' => $old_balance,
                'balance_after' => $new_balance,
                'fee' => $request->data['app_fee'],
                'amount' => $sent_amount,
                'amount_sent' => $request->data['amount'],
                'status' => "successful",
                'created_at' => $request->data['created_at'],
                'narration' => $sent_amount . " deposited to account"
            ]);


        }


    }


    // Buy Airtime
    public function BuyAirtime1($amount, $phone_number, $network, $ref) {

        $net = "";
        if($network == "MTN"){
            $net = "01";
        }elseif($network == "GLO") {
            $net = "02";
        }elseif($network == "AIRTEL"){
            $net = "04";
        }elseif($network == "9MOBILE"){
            $net = "03";
        }

        $Buy = Http::get('https://www.nellobytesystems.com/APIAirtimeV1.asp', [
            'UserID' => getenv('CLUB_KON_ID'),
            'APIKey' => getenv('CLUB_KON'),
            'MobileNetwork' => $net,
            'Amount' => $amount,
            'MobileNumber' => $phone_number,
            'RequestID' => $ref,
            'CallBackURL' => env("APP_URL") . "/api/callback/clubkon"
        ]);

        $BuyJson = $Buy->json();

        Log::info($BuyJson);

        if ($BuyJson['status'] != "ORDER_RECEIVED") {
            return [
                "status" => false
            ];
        } else {
            return [
                "status" => true,
                "amount" => $BuyJson['amount'],
                "order_id" => $BuyJson['orderid']
            ];
        }


    }


    // CallBack Clubkon
    public function ClubKon(Request $request) {
        Log::info($request->all());
    }



}
