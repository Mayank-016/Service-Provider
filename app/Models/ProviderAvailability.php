<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderAvailability extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'provider_id',
        'date',
        'start_time',
        'end_time',
        'slot_duration'
    ];

    protected $hidden = ['updated_at'];

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}
