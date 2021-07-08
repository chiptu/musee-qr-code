<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
//use Intervention\Image\ImageManagerStatic as Image;


use App\Models\User;
use App\Models\Artwork;

class Museum extends Model
{

    //test
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    protected $table = 'museum';

    protected $fillable = [
        'name','adress', 'logo', 'description', 'qrCodeSize'
    ];



    protected $casts = [
        'adress' => 'json'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function artworks()
    {
        return $this->hasMany(Artwork::class);
    }

    public function setLogoAttribute($value)
    {
        $attribute_name = "logo";
        $disk = "public";
        $destination_path="/museum/logo";

        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }
}
