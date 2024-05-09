<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <meta name="description" content="Manage your Taiko experience">
        <meta name="theme-color" content="#94ab67">

        <link rel="manifest" href="/build/manifest.webmanifest" crossorigin="use-credentials">

        <!-- Favicon -->
        <link rel="icon" href="/favicon.ico" sizes="48x48">
        <link rel="icon" href="/images/favicon_black_64.png" media="(prefers-color-scheme: light)" />
        <link rel="icon" href="/images/favicon_white_64.png" media="(prefers-color-scheme: dark)" />
        <link rel="apple-touch-icon" href="/images/favicon_white_180.png" sizes="180x180">
        <link rel="mask-icon" href="/images/favicon_white_192.png" color="#FFFFFF">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    </head>

    <body>
        {{ $slot }}
    </body>
</html>
