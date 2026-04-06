<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HeavyHire - User Panel</title>
    
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
            
            --indigo-50: #eef2ff;
            --indigo-100: #e0e7ff;
            --indigo-500: #6366f1;
            --indigo-600: #4f46e5;
            --indigo-700: #4338ca;
            
            --amber-400: #fbbf24;
            --amber-500: #f59e0b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--slate-50);
            color: var(--slate-900);
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: var(--slate-900);
            color: white;
            z-index: 1000;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            padding: 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-brand span {
            color: var(--amber-400);
        }

        .sidebar-nav {
            padding: 1.5rem 1rem;
            flex-grow: 1;
            overflow-y: auto;
        }

        .nav-heading {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--slate-500);
            margin: 1.5rem 0 0.75rem 1rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.875rem 1.25rem;
            color: var(--slate-400);
            text-decoration: none;
            font-weight: 600;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
            margin-bottom: 0.25rem;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.05);
        }

        .nav-link.active {
            color: white;
            background: var(--indigo-600);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .nav-link.active i {
            color: var(--amber-400);
        }

        .nav-link i {
            font-size: 1.125rem;
            transition: color 0.2s ease;
        }

        /* Content Wrapper */
        .content-wrapper {
            margin-left: 280px;
            padding: 2.5rem;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-280px);
            }
            .content-wrapper {
                margin-left: 0;
            }
            body.sidebar-open .sidebar {
                transform: translateX(0);
            }
            body.sidebar-open .sidebar-overlay {
                display: block;
            }
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            z-index: 999;
            display: none;
        }

        /* Mobile Header */
        .mobile-header {
            display: none;
            padding: 1rem 1.5rem;
            background: white;
            border-bottom: 1px solid var(--slate-200);
            position: sticky;
            top: 0;
            z-index: 900;
        }

        @media (max-width: 1024px) {
            .mobile-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
        }

        /* Utility Classes */
        .card-admin {
            background: white;
            border-radius: 1.25rem;
            border: 1px solid var(--slate-200);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .card-header-admin {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--slate-100);
            font-weight: 800;
            color: var(--slate-800);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .page-title {
            font-size: 1.875rem;
            font-weight: 900;
            color: var(--slate-900);
            margin-bottom: 2rem;
            letter-spacing: -0.025em;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-title i {
            color: var(--indigo-600);
        }

        /* Status Badges */
        .status-badge {
            padding: 0.375rem 1rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .status-pending { background: #fffbeb; color: #b45309; }
        .status-dipinjam { background: #eff6ff; color: #1d4ed8; }
        .status-selesai { background: #f0fdf4; color: #15803d; }
        .status-ditolak { background: #fef2f2; color: #b91c1c; }

        /* Buttons */
        .btn-indigo {
            background: var(--indigo-600);
            color: white;
            border: none;
            font-weight: 700;
            padding: 0.625rem 1.5rem;
            border-radius: 0.75rem;
            transition: all 0.2s;
        }

        .btn-indigo:hover {
            background: var(--indigo-700);
            color: white;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <header class="mobile-header">
        <a href="#" class="sidebar-brand" style="color: var(--slate-900)">Heavy<span>Hire</span></a>
        <button class="btn border-0 shadow-none" id="sidebarToggle">
            <i class="fas fa-bars fa-lg"></i>
        </button>
    </header>