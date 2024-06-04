<?php

namespace App\Http\Controllers;

use App\Models\AccessTokenModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Models\AdminModel;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{

    /**
     * Update a specific column in the admin table where id = 1
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */


    public function updateAdminConfig(Request $request)
    {

        // Define allowed columns to be updated
        $allowedColumns = ['data_status', 'airtime_status', 'electricity_status', 'cable_status', 'education_status', 'transport_status', 'internet_status', 'link_status', 'transfer_status', 'all_services_status', 'onboard_status', 'daily_bonus', 'bonus']; // Update with your actual columns

        // Validate the request
        $validator = Validator::make($request->all(), [
            'column' => 'required|string|in:' . implode(',', $allowedColumns),
            'value' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = $request->user();
        if (!$user->tokenCan('manage_admin')) {
            return response()->json([
                "status" => 403,
                "message" => "Bad request"
            ], 403);
        }

        try {

            // Fetch the column name and value from the request
            $column = $request->input('column');
            $value = $request->input('value');

            // Find the admin record
            $admin = AdminModel::find(1);

            if (!$admin) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Admin record not found.'
                ], 404);
            }

            // Update the admin record
            $admin->$column = $value;
            $admin->save();

            // Invalidate the cache
            Cache::forget('admin_settings');

            return response()->json([
                'status' => 200,
                'message' => 'Admin setting updated successfully.'
            ], 200);

        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to update admin setting', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while updating the admin setting.'
            ], 500);

        }

    }


    // Suspend user from transacting.
    public function SuspendTokenFromTransacting(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'ident' => 'required|email:rfc,dns',
                'type' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => 404,
                    "message" => "Bad request"
                ], 404);
            }

            $user = $request->user();
            if (!$user->tokenCan('manage_admin')) {
                return response()->json([
                    "status" => 403,
                    "message" => "Bad request"
                ], 403);
            }

            // Resolve customer
            $customer = User::where("email", $request->input('ident'))->first();
            if (!$customer) {
                return response()->json([
                    "status" => 403,
                    "message" => "Bad request"
                ], 403);
            }

            // Update token ability
            $accessToken = AccessTokenModel::where('id', $customer->id)->first();
            if (!$accessToken) {
                return response()->json([
                    "status" => 403,
                    "message" => "Bad request"
                ], 403);
            }

            if ($request->input("type") === "deactivate") {

                $accessToken->update([
                    'abilities' => ['read']
                ]);
                User::where('id', $customer->id)->update([
                    'status' => 0
                ]);

            } elseif ($request->input("type") === "activate") {
                $accessToken->update([
                    'abilities' => ['read', 'transact']
                ]);
                User::where('id', $customer->id)->update([
                    'status' => 1
                ]);

            } else {
                return response()->json([
                    "status" => 403,
                    "message" => "Bad request"
                ], 403);
            }


            // Response
            return response()->json([
                "status" => 200,
                "message" => "Updated successfully"
            ], 200);

        } catch (Exception $th) {
            return response()->json([
                "status" => 500,
                "message" => $th->getMessage()
            ], 500);
        }

    }


}
