<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'category_id'];
    
    protected $hidden = ['updated_at'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function providerServices()
    {
        return $this->hasMany(ProviderService::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
