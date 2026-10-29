<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $appends = ['image_url'];

    protected $fillable = [
        'name',
        'status',
        'image'
    ];

    public function getImageUrlAttribute()
    {
        if (empty($this->image)) {
            return 'https://placehold.co/600x600?text=No+Image';
        }

        return asset('uploads/category/' . $this->image);
    }
}