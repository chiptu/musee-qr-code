<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Artwork;

class Media extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    protected $table = 'medias';

    protected $fillable = [
        'name','type', 'metadata','url','artwork_id','content'
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

    public function setUrlAttribute($value)
    {
        $attribute_name = "url";
        $disk = "public";
        $destination_path = "/media/audio";

        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);

        // return $this->attributes[{$attribute_name}]; // uncomment if this is a translatable field
    }
}
