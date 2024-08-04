<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'user_id', 'title', 'description', 'event_date'];
    protected $casts = [
        'event_date' => 'datetime',
    ];

    public function scopeSearch($query, $value){
        $query->where('title', 'like', "%{$value}%")->orWhere('description', 'like', "%{$value}%");
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function participants()
    {
        return $this->hasMany(EventParticipant::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public static function generateEventId()
    {
        $prefix = 'EVT';
        $date = now()->format('Ymd');
        $latestEvent = self::whereDate('created_at', now()->toDateString())
                           ->orderBy('created_at', 'desc')
                           ->first();

        $sequence = $latestEvent ? (int) substr($latestEvent->id, -4) + 1 : 1;
        $sequence = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$date}-{$sequence}";
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            $pattern = '/^EVT-\d{8}-\d{4}$/';

            if (!$event->id || !preg_match($pattern, $event->id)) {
                $event->id = self::generateEventId();
            }
        });

        //because observers cannot detected by serviceprovider, so here the alternative
        static::created(function ($event) {
            $reminderDate = Carbon::parse($event->event_date)->subMinutes(30);

            $event->reminders()->create([
                'user_id' => $event->user_id,
                'reminder_date' => $reminderDate,
                'message' => 'Reminder: ' . $event->title . ' is starting soon!'
            ]);
        });
    }
}
