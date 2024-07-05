<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google_Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ServicesController extends Controller
{

    // Send Push Notification
    public function SendPushNotification($deviceToken, $bod, $titl)
    {

        // $token = $request->input("token");
        // $body = $request->input("body");
        // $title = $request->input("title");

        $token = $deviceToken;
        $body = $bod;
        $title = $titl;

        if (!empty($body) && !empty($title) && !empty($token)) {

            // Specify the path to your service account JSON file in the storage folder
            $serviceAccountJsonFile = storage_path('app/swiftvend-a130f-firebase-adminsdk-hcjka-372df9c977.json');

            // Create a new Google Client
            $client = new Google_Client();
            $client->setAuthConfig($serviceAccountJsonFile);
            $client->setScopes(['https://www.googleapis.com/auth/firebase.messaging']);

            // Get the access token
            $accessToken = $client->fetchAccessTokenWithAssertion();

            if (isset($accessToken['error'])) {
                return response()->json(
                    ['error' => 'Failed to fetch access token'],
                    500
                );
            }

            // The access token
            $access_token = $accessToken['access_token'];

            // Define the FCM API URL
            $api_url = 'https://fcm.googleapis.com/v1/projects/swiftvend-a130f/messages:send';
            $message = [
                "message" => [
                    "token" => $token,
                    "notification" => [
                        "body" => $body,
                        "title" => $title
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json',
            ])->post($api_url, $message);

            if ($response->failed()) {
                Log::info($response->json());
            }


            return response()->json([
                'status' => 200,
                "message" => "Notification sent successfully"
            ], 200);

        } else {
            Log::info("Error Push");
            return response()->json([
                'status' => 400,
                'message' => "Empty fields"
            ], 400);
        }
    }


    // Send Push Notification
    public function SendPushNotificationApi(Request $request)
    {

        // $token = $request->input("token");
        // $body = $request->input("body");
        // $title = $request->input("title");

        $token = $request->token;
        $body = $request->body;
        $title = $request->title;

        if (!empty($body) && !empty($title) && !empty($token)) {

            // Specify the path to your service account JSON file in the storage folder
            $serviceAccountJsonFile = storage_path('app/swiftvend-a130f-firebase-adminsdk-hcjka-372df9c977.json');

            // Create a new Google Client
            $client = new Google_Client();
            $client->setAuthConfig($serviceAccountJsonFile);
            $client->setScopes(['https://www.googleapis.com/auth/firebase.messaging']);

            // Get the access token
            $accessToken = $client->fetchAccessTokenWithAssertion();

            if (isset($accessToken['error'])) {
                return response()->json(
                    ['error' => 'Failed to fetch access token'],
                    500
                );
            }

            // The access token
            $access_token = $accessToken['access_token'];

            // Define the FCM API URL
            $api_url = 'https://fcm.googleapis.com/v1/projects/swiftvend-a130f/messages:send';
            $message = [
                "message" => [
                    "token" => $token,
                    "notification" => [
                        "body" => $body,
                        "title" => $title
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json',
            ])->post($api_url, $message);

            if ($response->failed()) {
                Log::info($response->json());
            }


            return response()->json([
                'status' => 200,
                "message" => "Notification sent successfully"
            ], 200);

        } else {
            Log::info("Error Push");
            return response()->json([
                'status' => 400,
                'message' => "Empty fields"
            ], 400);
        }
    }


}
