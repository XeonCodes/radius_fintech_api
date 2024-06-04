<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationsModel extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'customer_id',
        'type',
        'message',
        'status',
        'link',
        'img',
        'ref',
    ];

}
