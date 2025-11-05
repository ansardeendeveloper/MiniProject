<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Staff Panel')</title>

    <!-- Bootstrap, Material Icons & Toastr -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
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

        .top-header i,
        .top-header .material-symbols-outlined {
            color: var(--accent);
            vertical-align: middle;
        }

      
        .main-content {
            margin-left: 250px;
            padding: 30px;
            min-height: calc(100vh - 60px);
        }

       
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
            <span class="material-symbols-outlined" style="font-size:48px;">engineering</span>
            <h4>Staff Panel</h4>
        </div>

        <ul class="nav flex-column">
            <li>
                <a class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}" href="{{ route('staff.dashboard') }}">
                    <span class="material-symbols-outlined">dashboard</span> Dashboard
                </a>
            </li>

            <li>
                <a class="nav-link {{ request()->routeIs('staff.services.create') ? 'active' : '' }}" href="{{ route('staff.services.create') }}">
                    <span class="material-symbols-outlined">add_circle</span> Add Service
                </a>
            </li>

            <li>
                <a class="nav-link {{ request()->routeIs('staff.services.index') ? 'active' : '' }}" href="{{ route('staff.services.index') }}">
                    <span class="material-symbols-outlined">build_circle</span> My Services
                </a>
            </li>

            <li>
                <form action="{{ route('staff.logout') }}" method="POST" class="m-0 p-0">@csrf
                    <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                        <span class="material-symbols-outlined">logout</span> Logout
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    
    <header class="top-header">
        <div>
            <span class="material-symbols-outlined me-1">account_circle</span>
            {{ request()->input('staff_name', 'Staff User') }}
        </div>
        <div>
            <button onclick="window.history.back()" class="btn-custom">
                <span class="material-symbols-outlined me-1">arrow_back</span> Back
            </button>
        </div>
    </header>

    <main class="main-content">
        @yield('content')
    </main>

    
    <footer>&copy; {{ date('Y') }} Vehicle Service Management — Staff Panel @Ansardeen</footer>

   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right"
        };

        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif
    </script>

    @yield('scripts')
</body>
</html>
