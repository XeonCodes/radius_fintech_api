<?php

namespace App\Http\Controllers;

use App\Models\NotificationsModel;
use App\Models\TransactionsModel;
use App\Models\User;
use Illuminate\Http\Request;

class GenerateController extends Controller
{

    // Generate Promo Code 6 Alphabeth
    public function GeneratePromoCode()
    {
        do {
            $promo_code = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
        } while (User::where('promo_code', $promo_code)->exists());
        return $promo_code;
    }


    // Generate Notification Reference
    public function GenerateNotificationReference()
    {
        do {
            $notification_reference = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 15);
        } while (NotificationsModel::where('ref', $notification_reference)->exists());
        return $notification_reference;
    }


    // Generate One Time Password
    public function GenerateOTP($cus_id)
    {

        $otp = rand(100000, 999999);
        $hash = password_hash($otp, PASSWORD_DEFAULT);
        // Update OTP and updated at
        User::where('id', $cus_id)->update([
            'otp' => $hash,
            'otp_updated_at' => now()
        ]);
        return $otp;

    }


    // Generate P2P transaction ref
    public function GenerateTransferTransactionReference()
    {
        do {
            $p2ptransaction = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 14);
        } while (TransactionsModel::where('txf', $p2ptransaction)->exists());
        return $p2ptransaction;
    }



}
