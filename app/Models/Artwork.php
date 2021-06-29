<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Medias;

class Artwork extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    protected $table = 'artworks';

    protected $fillable = [
        'name', 'metadata',
    ];

    protected $attributes = [
        'metadata' => '[]'
    ];

    protected $casts = [
        'metadata' => 'json',
    ];

    public function museum()
    {
        return $this->belongsTo(Museum::class);
    }

    public function medias()
    {
        return $this->hasMany(Medias::class);
    }

    public function openGoogle($crud = false)
    {
        return '  
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://museum.app/'.$this->id.' " >
        ';
    }
}
