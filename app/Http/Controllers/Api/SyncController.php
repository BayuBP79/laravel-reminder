<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SyncController extends Controller
{
    public function syncEvents(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $event = Event::create([
            'title' => $request->title,
            'user_id' => $request->user_id,
            'description' => $request->description,
            'event_date' => Carbon::parse($request->event_date)->format('Y-m-d\TH:i'),
        ]);

        return response()->json([
            'message' => 'Event created successfully',
            'event' => $event
        ], 201);
    }
}
