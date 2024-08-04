<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;
    protected $fillable = ['event_id', 'user_id', 'reminder_date', 'message'];
    protected $casts = [
        'reminder_date' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function scopeSearch($query, $value){
        $query->where('message', 'like', "%{$value}%");
    }
}
