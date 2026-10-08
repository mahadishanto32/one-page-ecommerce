<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- DataTables CSS (Bootstrap 4 theme) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">

    <!-- App / Admin styles (must load after the CDNs so it can override them) -->
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">

    @stack('styles')
</head>
<body>
    <div class="dashboard">
        @include('admin.partials.sidebar')

        <div class="main-content">
            @include('admin.partials.header')

            <div class="container-fluid-admin">
                @if (session('success'))
                    <div class="admin-card" data-auto-dismiss style="border-left:4px solid var(--admin-success); margin-bottom:20px;">
                        <div class="admin-card-body" style="padding:14px 20px; display:flex; align-items:center; gap:10px; color: var(--admin-success); font-weight:600;">
                            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="admin-card" data-auto-dismiss style="border-left:4px solid var(--admin-danger); margin-bottom:20px;">
                        <div class="admin-card-body" style="padding:14px 20px; display:flex; align-items:center; gap:10px; color: var(--admin-danger); font-weight:600;">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

    <!-- Chart.js (used by the dashboard's revenue chart) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

    <!-- CKEditor 5 (classic build) -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>

    <!-- App / Admin scripts -->
    <script src="{{ asset('assets/js/admin.js') }}"></script>

    @stack('scripts')
    @yield('scripts')
</body>
</html>