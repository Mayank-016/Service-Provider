<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description'];

    protected $hidden = [
        'updated_at',
        'deleted_at',
        'created_at'
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
