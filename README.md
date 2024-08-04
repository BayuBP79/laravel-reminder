<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Project Title

This is laravel event reminder app that automatically send email at specified time

## Features

- Broadcast email to all event participants
- Offline Create Event

## Installation

1.  Clone the repository:
    ```sh
    git clone https://github.com/BayuBP79/laravel-reminder.git
    cd laravel-reminder

2.  Install dependencies:
    composer install
    npm install

3.  Set up environment variables:
    cp .env.example .env
    php artisan key:generate

4.  Configure your database in the `.env` file.

5.  Run migrations
    php artisan migrate

6.  Build assets
    npm run dev => for continuous refresh on changes
    npm run build => for manual refresh asset changes
