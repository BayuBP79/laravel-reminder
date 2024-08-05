<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EventParticipant;
use Illuminate\Support\Facades\DB;

class EventCSVController extends Controller
{
    public function uploadForm()
    {
        return view('event.upload-form');
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->path(), 'r');
        $headers = fgetcsv($handle);

        DB::beginTransaction();

        try {
            $events = [];

            while (($row = fgetcsv($handle)) !== false) {
                $eventKey = $row[0] . $row[2];

                if (!isset($events[$eventKey])) {
                    $events[$eventKey] = Event::create([
                        'title' => $row[0],
                        'description' => $row[1],
                        'event_date' => $row[2],
                        'user_id' => auth()->id(),
                    ]);
                }

                EventParticipant::create([
                    'event_id' => $events[$eventKey]->id,
                    'name' => $row[3],
                    'email' => $row[4],
                ]);
            }

            DB::commit();
            fclose($handle);

            return redirect()->back()->with('success', 'CSV imported successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);

            return redirect()->back()->with('error', 'Error importing CSV: ' . $e->getMessage());
        }
    }
}
