<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminModel extends Model
{
    use HasFactory;

    protected $table = 'admin';

    protected $fillable = [
        'data_status',
        'airtime_status',
        'electricity_status',
        'cable_status',
        'education_status',
        'transport_status',
        'internet_status',
        'link_status',
        'daily_bonus',
        'all_services_status',
    ];

}
