<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionsModel extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'customer_id',
        'type',
        'txf',
        'x_ref',
        'session_id',
        'trans_type',
        'account_type',
        'beneficiary',
        'status',
        'narration',
        'account_name',
        'account_number',
        'bank_name',
        'bank_code',
        'amount',
        'fee',
        'balance_before',
        'balance_after'
    ];

}
