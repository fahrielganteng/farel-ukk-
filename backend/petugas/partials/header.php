<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Petugas Panel - Alat Berat</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --secondary: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #0f172a;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --sidebar-width: 260px;
            --radius: 12px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-md: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--slate-100);
            color: var(--slate-700);
            margin: 0;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* Layout */
        .wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--slate-800);
            height: 100vh;
            position: fixed;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            color: #fff;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar .logo {
            padding: 30px 24px;
            font-size: 1.25rem;
            font-weight: 800;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            letter-spacing: -0.5px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        
        .sidebar .logo i {
            color: #818cf8;
            font-size: 1.75rem;
            margin-right: 12px;
        }

        .sidebar-wrapper {
            padding: 24px 0;
            flex: 1;
            overflow-y: auto;
        }

        .sidebar .nav {
            display: flex;
            flex-direction: column;
            padding: 0 16px;
            list-style: none;
        }

        .sidebar .nav-item {
            margin-bottom: 4px;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: #94a3b8;
            text-decoration: none;
            border-radius: var(--radius);
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .sidebar .nav-link i {
            margin-right: 14px;
            font-size: 1.25rem;
            width: 24px;
            text-align: center;
            opacity: 0.7;
        }

        .sidebar .nav-item.active .nav-link {
            background: var(--primary);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
        
        .sidebar .nav-item.active .nav-link i {
            opacity: 1;
        }

        .sidebar .nav-link:hover:not(.active) {
            background: rgba(255,255,255,0.05);
            color: #f8fafc;
        }
        
        /* Main Content */
        .main-panel {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: var(--slate-100);
        }

        /* Top Navbar */
        .top-navbar {
            background: #ffffff;
            height: 70px;
            display: flex;
            align-items: center;
            padding: 0 40px;
            border-bottom: 1px solid var(--slate-200);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .content-wrapper {
            padding: 40px;
            flex: 1;
        }

        /* Component: Admin Card */
        .card-admin {
            background: #ffffff;
            border-radius: var(--radius);
            border: 1px solid var(--slate-200);
            box-shadow: var(--shadow-sm);
            margin-bottom: 24px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }
        
        .card-admin:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }
        
        .card-header-admin {
            padding: 24px 30px;
            border-bottom: 1px solid var(--slate-100);
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--dark);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .card-body-admin {
            padding: 30px;
        }

        /* Tables Unified Style */
        .table-admin {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }
        
        .table-admin thead th {
            background: #f8f9fa !important;
            color: #334155 !important;
            text-transform: uppercase !important;
            font-size: 0.75rem !important;
            font-weight: 800 !important;
            letter-spacing: 0.075em !important;
            padding: 18px 24px !important;
            border-bottom: 3px solid #f59e0b !important; /* Premium Amber Border */
            border-top: none !important;
            white-space: nowrap;
        }
        
        .table-admin tbody td {
            padding: 20px 24px !important;
            vertical-align: middle !important;
            color: #475569 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .table-admin tbody tr:hover td {
            background-color: #f8fafc !important;
            color: #1e293b !important;
        }

        /* Fix scrolling for long tables */
        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: var(--radius);
        }

        /* Optional: Styled Scrollbar for better UX */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }
        .table-responsive::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Status Badges Standardized */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.025em;
            gap: 6px;
        }

        .status-tersedia, .status-available, .status-selesai, .status-disetujui {
            background: #ecfdf5;
            color: #059669;
        }

        .status-dipinjam, .status-pending, .status-warning {
            background: #fff7ed;
            color: #d97706;
        }

        .status-rusak, .status-ditolak, .status-danger {
            background: #fef2f2;
            color: #dc2626;
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 10px 20px;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);
        }
        
        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(79, 70, 229, 0.3);
        }

        /* Badges */
        .badge {
            padding: 6px 12px;
            font-weight: 600;
            border-radius: 6px;
        }

        /* Utility */
        .page-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            letter-spacing: -0.5px;
        }
        
        .page-title i {
            margin-right: 14px;
            color: var(--primary);
            background: var(--primary-light);
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                margin-left: calc(var(--sidebar-width) * -1);
            }
            .main-panel {
                width: 100%;
                margin-left: 0;
            }
            
            /* Sidebar Open state */
            body.sidebar-open .sidebar {
                margin-left: 0;
            }
            
            body.sidebar-open .sidebar-overlay {
                display: block;
            }
            
            .content-wrapper {
                padding: 20px;
            }
            
            .top-navbar {
                padding: 0 20px;
            }
        }
        
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            backdrop-filter: blur(2px);
        }
    </style>
</head>
<body>
<div class="wrapper">