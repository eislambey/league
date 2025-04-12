<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'League') }}</title>

    @routes
    @vite(['resources/js/app.js', 'resources/css/app.scss'])
    @inertiaHead
</head>
<body class="d-flex flex-column h-100" style="background-color: #f8f9fa;">
<main class="flex-shrink-0">
    @inertia
</main>
<footer class="footer mt-auto py-3 bg-dark-subtle border-top">
    <div class="container">
        <span class="text-muted">All rights reserved</span>
    </div>
</footer>
</body>
</html>
