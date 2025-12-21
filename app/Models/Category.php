<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Category extends Model
{
    use HasUuids;

    protected $table = "categories";
    
    protected $fillable = [
        'name',
        'slug',
        'type',
    ];

    public function hyperlinks()
    {
        return $this->hasMany(Hyperlink::class);
    }
}