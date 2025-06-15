<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'service_id',
        'provider_id',
        'price',
        'status',
        'start_time',
        'end_time',
        'booking_date'
    ];

    protected $hidden = ['updated_at','status'];

    const STATUS_LABELS = [
        BOOKING_STATUS_CONFIRMED => 'confirmed',
        BOOKING_STATUS_CANCELLED => 'cancelled',
    ];

    // Keep the original status attribute intact, but add a new accessor
    protected $appends = ['status_label'];

    // Return the label for API output
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->attributes['status']] ?? 'unknown';
    }

    protected $casts = [
        'booking_date' => 'date:Y-m-d',
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
    ];
    

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
