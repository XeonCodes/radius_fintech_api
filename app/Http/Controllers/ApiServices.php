<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiServices extends Controller
{

    // Resolve Bank (1)
    public function ResolveBankHaven($code, $number)
    {

        $post = Http::withHeaders([

        ])->post("", [

                ]);

    }

}
