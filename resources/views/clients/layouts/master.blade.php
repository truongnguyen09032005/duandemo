<!DOCTYPE html>
<html lang="en">
<head>
    @include('clients.layouts.header')
</head>
<body>

    {{-- Nội dung chính --}}
    @yield('content')

    {{-- Footer và script --}}
    @include('clients.layouts.footer')

</body>
</html>
