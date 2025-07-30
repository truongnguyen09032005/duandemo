<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quản trị Admin</title>
    <link rel="stylesheet" href="{{ asset('admins/plugins/bootstrap/css/bootstrap.min.css') }}">
    <!-- thêm các file css/js nếu có -->
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    @include('admins.blocks.header')
    @include('admins.blocks.nav')
    @include('admins.blocks.sidebar')

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        @yield('content')
    </div>
    <!-- /.content-wrapper -->
      {{-- <div class="content-wrapper">
        @yield('category')
    </div> --}}

    @include('admins.blocks.footer')

</div>

<!-- thêm js nếu cần -->
<script src="{{ asset('admins/plugins/jquery/jquery.min.js') }}"></script>
</body>
</html>
