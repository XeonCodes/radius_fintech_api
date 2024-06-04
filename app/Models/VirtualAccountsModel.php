<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualAccountsModel extends Model
{
    use HasFactory;

    protected $table = 'virtual_accounts';

    protected $fillable = [
        'customer_id',
        'account_id',
        'account_reference',
        'account_number',
        'account_name',
        'bank_name',
        'bank_code'
    ];

}
