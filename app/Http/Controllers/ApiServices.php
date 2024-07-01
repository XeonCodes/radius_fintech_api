<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiServices extends Controller
{

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

}
