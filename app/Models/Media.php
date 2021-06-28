<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Artwork;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','type', 'metadata','url'
    ];

    protected $attributes = [
        'metadata' => '[]'
    ];

    protected $casts = [
        'metadata' => 'json',
    ];

    public function artwork()
    {
        return $this->belongsTo(Artwork::class);
    }
}
