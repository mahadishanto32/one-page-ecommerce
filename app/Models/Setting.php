<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'app_name',
        'phone_number',
        'whatsapp_number',
        'address',
        'alternative_address',
        'email',
        'logo',
        'favicon',
        'facebook_link',
        'instagram_link',
        'youtube_link',
        'x_id',
        'google_map_link',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
