<?php

namespace App\Jobs;

use App\Models\Reminder;
use App\Mail\ReminderEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendReminderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $reminder;

    public function __construct($email, Reminder $reminder)
    {
        $this->email = $email;
        $this->reminder = $reminder;
    }

    public function handle()
    {
        Mail::to($this->email)->send(new ReminderEmail($this->reminder));
    }
}
