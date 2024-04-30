<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TaikoBot</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')

    <!-- Favicon -->
    <link rel="icon" href="/images/favicon_black_64.png" media="(prefers-color-scheme: light)" />
    <link rel="icon" href="/images/favicon_white_64.png" media="(prefers-color-scheme: dark)" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
</head>

<body>
    <div id="app"></div>
</body>

</html>
