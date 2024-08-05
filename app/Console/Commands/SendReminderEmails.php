<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Reminder;
use App\Mail\ReminderEmail;
use App\Jobs\SendReminderEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReminderEmails extends Command
{
    protected $signature = 'reminders:dispatch';
    protected $description = 'Dispatch email reminder to be sent via email';

    public function handle()
    {
        $now = Carbon::now();
        $date = $now->toDateString();
        $time = $now->format('H:i');

        $reminders = Reminder::whereDate('reminder_date', $date)
                            ->whereTime('reminder_date', $time)
                            ->where('sent', 0)
                            ->get();

        foreach ($reminders as $reminder) {
            $participants = $reminder->event->participants;
            foreach ($participants as $participant) {
                SendReminderEmail::dispatch($participant->email, $reminder);
                $this->info('Reminders to '.$participant->email.' dispatched successfully.');
            }

            $reminder->update(['sent' => 1]);
        }

    }
}
