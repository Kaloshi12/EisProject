<?php

return [

/*
|----------------------------------------------------------------------|
| Authentication Defaults
|----------------------------------------------------------------------|
| This option defines the default authentication "guard" and password |
| reset "broker" for your application. You may change these values     |
| as required, but they're a perfect start for most applications.     |
|----------------------------------------------------------------------|
*/

'defaults' => [
    'guard' => env('AUTH_GUARD', 'api'), // Default to 'api' for Sanctum authentication
    'passwords' => env('AUTH_PASSWORD_BROKER', 'users'), // Default password broker
],

/*
|----------------------------------------------------------------------|
| Authentication Guards
|----------------------------------------------------------------------|
| Here you may define every authentication guard for your application.|
| Sanctum and session are configured for your application.            |
|----------------------------------------------------------------------|
*/

'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'api' => [
        'driver' => 'jwt', // Use Sanctum for API guard
        'provider' => 'users',
    ],
],

/*
|----------------------------------------------------------------------|
| User Providers
|----------------------------------------------------------------------|
| Defines how the users are actually retrieved out of your database.   |
| For most applications, you'll be using the eloquent provider.       |
|----------------------------------------------------------------------|
*/

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => env('AUTH_MODEL', App\Models\User::class), // Ensure the model is correct (User model)
    ],
],

/*
|----------------------------------------------------------------------|
| Resetting Passwords
|----------------------------------------------------------------------|
| Configures the password reset behavior including the table name and |
| token expiration. The throttle setting prevents brute force token   |
| generation.                                                        |
|----------------------------------------------------------------------|
*/

'passwords' => [
    'users' => [
        'provider' => 'users',
        'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'), // Ensure the table is correct
        'expire' => 60,  // Token expiration time in minutes
        'throttle' => 60, // Throttle for password reset attempts
    ],
],

/*
|----------------------------------------------------------------------|
| Password Confirmation Timeout
|----------------------------------------------------------------------|
| Defines how long the password confirmation window lasts before it   |
| expires.                                                           |
|----------------------------------------------------------------------|
*/

'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800), // Default 3 hours

];
