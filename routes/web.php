<?php

use App\Models\Reminder;
use App\Mail\ReminderEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// Route::get('sendemail/{id}', function ($id) {
//     $reminder = Reminder::find($id);

//     if ($reminder) {
//         Mail::to('yuucodesofficial@gmail.com')->send(new ReminderEmail($reminder));
//         return 'Email sent successfully';
//     } else {
//         return 'Reminder not found';
//     }
// });

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::group(['prefix' => 'event', 'as' => 'event.'], function () {
    Route::get('/', function () {
        return view('event.index');
    })->name('index')->middleware(['auth']);
    Route::get('{event}/reminder', function () {
        return view('event.reminder');
    })->name('reminder')->middleware(['auth']);
});

Route::get('/offline', function () {
    return view('offline');
});

require __DIR__ . '/auth.php';
