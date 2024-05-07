<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <meta name="description" content="Manage your Taiko experience">
        <meta name="theme-color" content="#94ab67">

        <!-- Favicon -->
        <link rel="icon" href="/images/favicon_black.png" media="(prefers-color-scheme: light)" />
        <link rel="icon" href="/images/favicon_white.png" media="(prefers-color-scheme: dark)" />
        <link rel="apple-touch-icon" href="/images/favicon_black.png" sizes="180x180">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100" id="app"></div>
        @vite(['resources/js/app.js'])
    </body>
</html>
