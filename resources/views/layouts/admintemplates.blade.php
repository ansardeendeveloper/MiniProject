<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>

    <!-- Bootstrap, FontAwesome & Toastr -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        :root {
            --primary: #007bff;
            --primary-dark: #0056b3;
            --accent: #ff6600;
            --bg: #f5f7fa;
            --dark: #1e1e2f;
            --light: #ffffff;
            --shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            --radius: 14px;
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--bg);
            margin: 0;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            background: linear-gradient(180deg, var(--dark), #2a2a45);
            color: white;
            height: 100vh;
            position: fixed;
            width: 250px;
            padding-top: 25px;
            box-shadow: var(--shadow);
        }

        .sidebar .text-center h4 {
            font-weight: 600;
            color: var(--accent);
        }

        .sidebar .nav-link {
            color: white;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 10px;
            margin: 5px 10px;
            transition: var(--transition);
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: var(--accent);
            transform: translateX(5px);
            color: white !important;
        }

        /* Header */
        .top-header {
            background: rgba(30, 30, 47, 0.95);
            backdrop-filter: blur(8px);
            color: white;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-left: 250px;
            box-shadow: var(--shadow);
        }

        .top-header i {
            color: var(--accent);
        }

        /* Content */
        .main-content {
            margin-left: 250px;
            padding: 30px;
            min-height: calc(100vh - 60px);
        }

        .metric-card {
            background: var(--light);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 25px;
            text-align: center;
            border-left: 6px solid var(--primary);
            transition: var(--transition);
        }

        .metric-card:hover { transform: translateY(-4px); }

        .metric-card h3 {
            color: var(--primary);
            font-weight: 700;
            font-size: 2rem;
        }

        /* Buttons */
        .btn-custom {
            background: var(--primary);
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 8px;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .btn-custom:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            text-align: center;
            padding: 12px 0;
            margin-top: 40px;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        @media (max-width: 768px) {
            .sidebar { position: relative; width: 100%; height: auto; }
            .top-header, .main-content { margin-left: 0; }
        }
    </style>

    @yield('head')
</head>
<body>
    <nav class="sidebar">
        <div class="text-center mb-4">
            <i class="fas fa-user-shield fa-3x mb-2"></i>
            <h4>Admin Panel</h4>
        </div>

        <ul class="nav flex-column">
            <li><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a class="nav-link {{ request()->routeIs('admin.staff') ? 'active' : '' }}" href="{{ route('admin.staff') }}"><i class="fas fa-users"></i> Staff</a></li>
            <li><a class="nav-link {{ request()->routeIs('admin.owners') ? 'active' : '' }}" href="{{ route('admin.owners') }}"><i class="fas fa-user-tie"></i> Owners</a></li>

            <li><a class="nav-link {{ request()->routeIs('admin.assign')?'active':'' }}" href="{{ route('admin.assign') }}"><i class="fas fa-tasks"></i>Work Assign</a></li>
            <li><a class="nav-link {{ request()->routeIs('admin.vehicles') ? 'active' : '' }}" href="{{ route('admin.vehicles') }}"><i class="fas fa-car"></i> Vehicles</a></li>
            <li><a class="nav-link {{ request()->routeIs('admin.services') ? 'active' : '' }}" href="{{ route('admin.services') }}"><i class="fas fa-tools"></i> Services</a></li>
            <li><a class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}" href="{{ route('admin.reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
            <li><a class="nav-link" href="{{ route('admin.logout') }}"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>

    <header class="top-header">
        <div><i class="fas fa-user-circle me-2"></i> {{ request()->input('admin_name', 'Admin User') }}</div>
        <div><button onclick="window.history.back()" class="btn-custom"><i class="fas fa-arrow-left me-1"></i> Back</button></div>
    </header>

    <main class="main-content">
        @yield('content')
    </main>

    <footer>&copy; {{ date('Y') }} Vehicle Service Management — All Rights Reserved @Ansardeen</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        toastr.options = { "closeButton": true, "progressBar": true, "positionClass": "toast-top-right" };
        @if(session('success')) toastr.success("{{ session('success') }}"); @endif
        @if(session('error')) toastr.error("{{ session('error') }}"); @endif
        @if($errors->any()) @foreach($errors->all() as $error) toastr.error("{{ $error }}"); @endforeach @endif
    </script>

    @yield('scripts')
</body>
</html>
                       