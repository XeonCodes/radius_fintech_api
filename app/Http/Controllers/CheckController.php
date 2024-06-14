<?php

namespace App\Http\Controllers;

use App\Models\TransactionsModel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Google\Service\Sheets\NumberFormat;

class CheckController extends Controller
{

    // Check Daily transfer Limits
    public function CheckDailyLimit($id, $amount)
    {
        $today = Carbon::today();

        // Calculate the total amount of successful debit transfers made today
        $totalTransferredToday = TransactionsModel::where('customer_id', $id)
            ->where('type', 'debit')
            ->where('status', 'successful')
            ->where('main_type', 'transfer')
            ->whereDate('created_at', $today)
            ->sum('amount');

        $dailyLimit = env('DAILY_LIMITS', 10000); // Default limit if not set in .env

        // Check if the total transferred amount exceeds the daily limit
        if ($totalTransferredToday >= $dailyLimit) {
            return [
                'status' => false,
                'message' => 'Daily transfer limit exceeded. Upgrade your account',
            ];
        } elseif ($totalTransferredToday + $amount > $dailyLimit) {
            return [
                'status' => false,
                'message' => 'You cannot send above ' . $this->NumberFormat($dailyLimit) . ' today. Upgrade your account',
            ];
        }

        return [
            'status' => true,
            'message' => 'Daily transfer limit not exceeded.',
        ];
    }


    // Example of a number formatting function
    protected function NumberFormat($number)
    {
        return number_format($number, 2);
    }



}
