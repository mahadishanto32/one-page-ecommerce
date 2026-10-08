<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeSlider extends Model
{
    use HasFactory;

    protected $table = 'home_slider';

    protected $fillable = [
        'name',
        'slider_type',
        'file_path',
        'serial',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'serial' => 'integer',
    ];

    public static function getSliderTypes(): array
    {
        return ['image', 'video'];
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('serial', 'asc');
    }

    public function isImage(): bool
    {
        return $this->slider_type === 'image';
    }

    public function isVideo(): bool
    {
        return $this->slider_type === 'video';
    }
}
