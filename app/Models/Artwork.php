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
        'name', 'color',
    ];


    public function museum()
    {
        return $this->belongsTo(Museum::class);
    }

    public function medias()
    {
        return $this->hasMany(Media::class);
    }
}
