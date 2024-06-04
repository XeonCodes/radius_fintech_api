<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeneficiariesModel extends Model
{
    use HasFactory;

    protected $table = 'beneficiaries';

    protected $fillable = [
        'customer_id',
        'trans_type',
        'beneficiary',
        'merchant',
    ];

}
