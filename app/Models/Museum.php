<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Artwork;

class Museum extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','adress', 'metadata',
    ];

    protected $attributes = [
        'metadata' => '[]'
    ];

    protected $casts = [
        'metadata' => 'json',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function artworks()
    {
        return $this->hasMany(Artwork::class);
    }
}
