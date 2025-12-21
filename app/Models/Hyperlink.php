<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasUUID;

class Hyperlink extends Model
{
    protected $table = "hyperlinks";
    protected $fillable = [
        "name",
        "description",
        "url",
        "target",
        "user_id"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
