<?php
// ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// KONEKSI DATABASE
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'peminjaman_alat_berat';
$connect = mysqli_connect($hostname, $username, $password, $database);

if (!$connect) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// AMBIL DATA ALAT BERAT TERSEDIA
$qBarang = "SELECT b.*, k.nama_kategori 
            FROM barang b 
            LEFT JOIN kategori k ON b.kategori_id = k.id 
            WHERE b.status = 'tersedia' 
            ORDER BY b.id DESC LIMIT 6";
$resultBarang = mysqli_query($connect, $qBarang);

// AMBIL DATA TESTIMONIAL (contoh statis, bisa diganti dari DB nanti)
$testimonials = [
    ['name' => 'Budi Santoso', 'position' => 'Project Manager PT Wijaya Karya', 'content' => 'Pelayanan sangat memuaskan, alat berat berkualitas dan tepat waktu. Sangat membantu proyek kami.', 'avatar' => 'BS'],
    ['name' => 'Siti Nurhaliza', 'position' => 'Site Manager PT PP', 'content' => 'Proses sewa cepat dan mudah. Operator berpengalaman, unit terawat. Highly recommended!', 'avatar' => 'SN'],
    ['name' => 'Ahmad Fauzi', 'position' => 'Owner CV Bangun Karya', 'content' => 'Harga kompetitif, pelayanan ramah. Sudah langganan sejak 2022. Terima kasih HeavyHire.', 'avatar' => 'AF'],
    ['name' => 'Dewi Sartika', 'position' => 'Direktur PT Adhi Karya', 'content' => 'Solusi rental alat berat terbaik di Indonesia. Profesional dan terpercaya.', 'avatar' => 'DS']
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HeavyHire - Solusi Peminjaman Alat Berat Professional</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --primary-light: #818cf8;
            --secondary: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #0f172a;
            --light: #f8fafc;
            --white: #ffffff;
            --gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #a855f7 100%);
            --gradient-light: linear-gradient(135deg, #818cf8 0%, #a78bfa 100%);
            --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--white);
            color: var(--dark);
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 5px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }

        /* Loading Screen */
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--white);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: opacity 0.5s, visibility 0.5s;
        }
        .loading-screen.hidden {
            opacity: 0;
            visibility: hidden;
        }
        .loader {
            width: 60px;
            height: 60px;
            border: 5px solid #f3f3f3;
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Navbar */
        .navbar {
            padding: 20px 0;
            transition: all 0.3s ease;
            position: absolute;
            width: 100%;
            z-index: 1000;
        }
        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 12px 0;
            position: fixed;
            box-shadow: var(--shadow-md);
            border-bottom: 1px solid rgba(79, 70, 229, 0.1);
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from { transform: translateY(-100%); }
            to { transform: translateY(0); }
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 1.75rem;
            letter-spacing: -1px;
            color: var(--white) !important;
            transition: 0.3s;
        }
        .navbar.scrolled .navbar-brand {
            color: var(--primary) !important;
        }
        .nav-link {
            font-weight: 600;
            color: rgba(255,255,255,0.8) !important;
            margin: 0 15px;
            transition: 0.3s;
            position: relative;
            padding: 5px 0;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s;
        }
        .nav-link:hover::after {
            width: 100%;
        }
        .navbar.scrolled .nav-link {
            color: var(--secondary) !important;
        }
        .navbar.scrolled .nav-link:hover {
            color: var(--primary) !important;
        }
        .btn-nav-login {
            background: var(--white);
            color: var(--primary);
            font-weight: 700;
            padding: 10px 24px;
            border-radius: 50px;
            text-decoration: none;
            transition: 0.3s;
            border: 2px solid transparent;
        }
        .navbar.scrolled .btn-nav-login {
            background: var(--gradient);
            color: var(--white);
            border: none;
        }
        .btn-nav-login:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
            background: transparent;
            color: var(--primary);
        }
        .navbar.scrolled .btn-nav-login:hover {
            background: var(--primary-dark);
            color: var(--white);
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            min-height: 700px;
            background: var(--dark);
            position: relative;
            display: flex;
            align-items: center;
            overflow: hidden;
            color: var(--white);
        }
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://images.unsplash.com/photo-1579412691523-9993309ce184?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;
            opacity: 0.3;
            z-index: 1;
            animation: zoom 20s infinite alternate;
        }
        @keyframes zoom {
            from { transform: scale(1); }
            to { transform: scale(1.1); }
        }
        .hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, var(--dark) 30%, transparent 100%);
            z-index: 2;
        }
        .hero-content {
            position: relative;
            z-index: 10;
        }
        .hero-title {
            font-size: 4.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 24px;
            letter-spacing: -2px;
            animation: fadeInUp 1s;
        }
        .hero-title span {
            color: var(--primary-light);
            position: relative;
            display: inline-block;
        }
        .hero-title span::after {
            content: '';
            position: absolute;
            bottom: 10px;
            left: 0;
            width: 100%;
            height: 8px;
            background: rgba(79, 70, 229, 0.3);
            z-index: -1;
        }
        .hero-subtitle {
            font-size: 1.25rem;
            color: rgba(255,255,255,0.7);
            margin-bottom: 40px;
            max-width: 600px;
            line-height: 1.6;
            animation: fadeInUp 1s 0.2s both;
        }
        .hero-buttons {
            animation: fadeInUp 1s 0.4s both;
        }
        .btn-hero {
            padding: 16px 40px;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        .btn-hero:hover::before {
            left: 100%;
        }
        .btn-hero-primary {
            background: var(--gradient);
            color: var(--white);
            border: none;
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.4);
        }
        .btn-hero-primary:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(79, 70, 229, 0.6);
        }
        .btn-hero-outline {
            background: transparent;
            color: var(--white);
            border: 2px solid rgba(255,255,255,0.3);
        }
        .btn-hero-outline:hover {
            background: var(--white);
            color: var(--primary);
            border-color: var(--white);
            transform: translateY(-5px);
        }

        /* Floating Elements */
        .floating-element {
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(79,70,229,0.2) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 3;
        }
        .floating-1 {
            top: -150px;
            right: -150px;
            animation: float 6s infinite;
        }
        .floating-2 {
            bottom: -150px;
            left: -150px;
            animation: float 8s infinite reverse;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        /* Scroll Indicator */
        .scroll-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            text-align: center;
            cursor: pointer;
        }
        .mouse {
            width: 30px;
            height: 50px;
            border: 2px solid var(--white);
            border-radius: 20px;
            position: relative;
            margin: 0 auto 10px;
        }
        .mouse::before {
            content: '';
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 10px;
            background: var(--white);
            border-radius: 2px;
            animation: scrollDown 2s infinite;
        }
        @keyframes scrollDown {
            0% { opacity: 1; transform: translateX(-50%) translateY(0); }
            100% { opacity: 0; transform: translateX(-50%) translateY(20px); }
        }

        /* Stats Section */
        .stats-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
            position: relative;
            overflow: hidden;
        }
        .stat-item {
            text-align: center;
            padding: 30px;
        }
        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 10px;
            animation: countUp 2s ease-out;
        }
        .stat-label {
            font-size: 1.1rem;
            color: var(--secondary);
            font-weight: 500;
        }

        /* Features Section */
        .section-padding {
            padding: 100px 0;
            position: relative;
        }
        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            letter-spacing: -1px;
            position: relative;
            display: inline-block;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 80px;
            height: 4px;
            background: var(--gradient);
            border-radius: 2px;
        }
        .section-title-center {
            text-align: center;
        }
        .section-title-center::after {
            left: 50%;
            transform: translateX(-50%);
        }
        .feature-card {
            padding: 40px 30px;
            border-radius: 24px;
            background: var(--white);
            border: 1px solid #f1f5f9;
            transition: all 0.4s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--gradient);
            transform: translateX(-100%);
            transition: transform 0.4s;
        }
        .feature-card:hover::before {
            transform: translateX(0);
        }
        .feature-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: var(--shadow-xl);
            border-color: var(--primary-light);
        }
        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient-light);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            font-size: 2rem;
            margin-bottom: 24px;
            transition: 0.3s;
        }
        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
        }

        /* Equipment Cards */
        .equipment-section {
            background: #f8fafc;
            position: relative;
            overflow: hidden;
        }
        .eq-card {
            background: var(--white);
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid #f1f5f9;
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
        }
        .eq-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-xl);
        }
        .eq-img {
            height: 200px;
            background: linear-gradient(45deg, #e2e8f0, #cbd5e1);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        .eq-img i {
            font-size: 5rem;
            color: #94a3b8;
            transition: 0.3s;
        }
        .eq-card:hover .eq-img i {
            transform: scale(1.2);
            color: var(--primary);
        }
        .eq-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: var(--success);
            color: white;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            z-index: 1;
            box-shadow: var(--shadow-md);
        }
        .eq-body {
            padding: 24px;
        }
        .eq-category {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            margin-bottom: 8px;
            display: block;
        }
        .eq-name {
            font-size: 1.25rem;
            font-weight: 750;
            margin-bottom: 12px;
            color: var(--dark);
        }
        .eq-price {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--dark);
        }
        .eq-price span {
            font-size: 0.85rem;
            color: var(--secondary);
            font-weight: 500;
        }
        .btn-rent {
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            font-weight: 700;
            margin-top: 20px;
            background: #f1f5f9;
            color: var(--dark);
            border: none;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .btn-rent::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        .eq-card:hover .btn-rent {
            background: var(--gradient);
            color: var(--white);
        }
        .eq-card:hover .btn-rent::before {
            left: 100%;
        }

        /* Testimonials Section */
        .testimonials-section {
            padding: 100px 0;
        }
        .testimonial-card {
            background: var(--white);
            border-radius: 24px;
            padding: 30px;
            border: 1px solid #f1f5f9;
            transition: 0.3s;
            height: 100%;
        }
        .testimonial-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-xl);
        }
        .testimonial-content {
            font-size: 1rem;
            line-height: 1.7;
            color: var(--secondary);
            margin-bottom: 20px;
            font-style: italic;
        }
        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .author-avatar {
            width: 50px;
            height: 50px;
            background: var(--gradient);
            color: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }
        .author-info h5 {
            font-weight: 700;
            margin-bottom: 5px;
        }
        .author-info p {
            font-size: 0.85rem;
            color: var(--secondary);
            margin: 0;
        }

        /* Gallery Section */
        .gallery-section {
            padding: 100px 0;
            background: #f8fafc;
        }
        .gallery-item {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            cursor: pointer;
            height: 250px;
        }
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.5s;
        }
        .gallery-item:hover img {
            transform: scale(1.1);
        }
        .gallery-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top, rgba(79,70,229,0.9), transparent);
            display: flex;
            align-items: flex-end;
            padding: 20px;
            opacity: 0;
            transition: 0.3s;
        }
        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }
        .gallery-overlay h4 {
            color: var(--white);
            transform: translateY(20px);
            transition: 0.3s;
        }
        .gallery-item:hover .gallery-overlay h4 {
            transform: translateY(0);
        }

        /* CTA Section */
        .cta-section {
            background: var(--gradient);
            padding: 80px 0;
            position: relative;
            overflow: hidden;
        }
        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.1"><path d="M20 20 L80 20 L50 80 Z" fill="white"/></svg>') repeat;
            animation: slide 20s linear infinite;
        }
        @keyframes slide {
            from { background-position: 0 0; }
            to { background-position: 100px 100px; }
        }
        .cta-content {
            position: relative;
            z-index: 1;
            text-align: center;
            color: var(--white);
        }
        .cta-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 20px;
        }
        .cta-subtitle {
            font-size: 1.2rem;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        .btn-cta {
            background: var(--white);
            color: var(--primary);
            padding: 16px 40px;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: 0.3s;
            display: inline-block;
            margin: 0 10px;
        }
        .btn-cta:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
            background: var(--dark);
            color: var(--white);
        }
        .btn-cta-outline {
            background: transparent;
            border: 2px solid var(--white);
            color: var(--white);
        }
        .btn-cta-outline:hover {
            background: var(--white);
            color: var(--primary);
        }

        /* Partner Logos */
        .partner-section {
            padding: 60px 0;
            background: var(--white);
        }
        .partner-logo {
            filter: grayscale(100%);
            opacity: 0.5;
            transition: 0.3s;
            font-size: 2rem;
            font-weight: 700;
            color: var(--secondary);
        }
        .partner-logo:hover {
            filter: grayscale(0);
            opacity: 1;
            color: var(--primary);
            transform: scale(1.1);
        }

        /* Footer */
        footer {
            background: var(--dark);
            color: rgba(255,255,255,0.6);
            padding: 80px 0 30px;
            position: relative;
        }
        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--gradient);
        }
        .footer-logo {
            color: var(--white);
            font-weight: 800;
            font-size: 1.75rem;
            margin-bottom: 24px;
            display: block;
            text-decoration: none;
        }
        .footer-link {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            display: block;
            margin-bottom: 12px;
            transition: 0.3s;
        }
        .footer-link:hover {
            color: var(--white);
            transform: translateX(5px);
        }
        .social-link {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.05);
            color: var(--white);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 10px;
            transition: 0.3s;
        }
        .social-link:hover {
            background: var(--gradient);
            transform: translateY(-5px) rotate(360deg);
        }

        /* Back to Top */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: 0.3s;
            z-index: 99;
            border: none;
            box-shadow: var(--shadow-lg);
        }
        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
        }
        .back-to-top:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }

        /* Floating Contact */
        .floating-contact {
            position: fixed;
            bottom: 30px;
            left: 30px;
            z-index: 99;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .floating-contact a {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            transition: 0.3s;
            box-shadow: var(--shadow-lg);
        }
        .floating-contact a.whatsapp {
            background: #25D366;
        }
        .floating-contact a.phone {
            background: var(--gradient);
        }
        .floating-contact a:hover {
            transform: scale(1.1) translateY(-5px);
        }

        /* Animation Classes */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .fade-in-up {
            animation: fadeInUp 1s ease;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .hero-title { font-size: 3rem; }
            .navbar { 
                background: rgba(255,255,255,0.95); 
                backdrop-filter: blur(10px); 
                position: fixed;
                box-shadow: var(--shadow-md);
            }
            .navbar-brand { color: var(--primary) !important; }
            .nav-link { color: var(--secondary) !important; }
        }
    </style>
</head>
<body>

    <!-- Loading Screen -->
    <div class="loading-screen" id="loading">
        <div class="loader"></div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg" id="navbar">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-truck-monster me-2"></i>HeavyHire
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#katalog">Katalog</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tentang">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
                    <li class="nav-item ms-lg-4">
                        <a href="pages/auth/login.php" class="btn-nav-login">
                            <i class="fas fa-sign-in-alt me-2"></i>Login Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="hero">
        <div class="floating-element floating-1"></div>
        <div class="floating-element floating-2"></div>
        <div class="container hero-content">
            <div class="row">
                <div class="col-lg-8">
                    <span class="badge bg-primary px-3 py-2 mb-4 rounded-pill">
                        <i class="fas fa-star me-2"></i>#1 Equipment Rental Solution 2024
                    </span>
                    <h1 class="hero-title">
                        Solusi Praktis <span>Rental Alat Berat</span> & Konstruksi
                    </h1>
                    <p class="hero-subtitle">Membantu proyek Anda berjalan lebih efisien dengan armada alat berat terlengkap, terawat, dan siap pakai kapanpun Anda butuhkan.</p>
                    <div class="hero-buttons">
                        <a href="#katalog" class="btn btn-hero btn-hero-primary me-3">
                            <i class="fas fa-calendar-alt me-2"></i>Pinjam Sekarang
                        </a>
                        <a href="#" class="btn btn-hero btn-hero-outline">
                            <i class="fas fa-play-circle me-2"></i>Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="scroll-indicator" onclick="scrollToCatalog()">
            <div class="mouse"></div>
            <small>Scroll</small>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="stat-item" data-aos="fade-up">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Unit Tersedia</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item" data-aos="fade-up" data-aos-delay="100">
                        <div class="stat-number">250+</div>
                        <div class="stat-label">Klien Korporasi</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item" data-aos="fade-up" data-aos-delay="200">
                        <div class="stat-number">15+</div>
                        <div class="stat-label">Tahun Pengalaman</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item" data-aos="fade-up" data-aos-delay="300">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Layanan Darurat</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section-padding">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title section-title-center">Mengapa Memilih HeavyHire?</h2>
                <p class="text-muted">Kami memberikan solusi terbaik untuk kebutuhan alat berat Anda</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                        <h4>Armada Terjamin</h4>
                        <p class="text-muted">Semua unit kami melewati inspeksi rutin dan perawatan berkala untuk memastikan performa maksimal di lapangan.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                        <h4>Proses Cepat</h4>
                        <p class="text-muted">Sistem peminjaman yang simpel dan terintegrasi memungkinkan Anda mendapatkan alat yang dibutuhkan dalam hitungan jam.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-tag"></i></div>
                        <h4>Harga Kompetitif</h4>
                        <p class="text-muted">Dapatkan penawaran harga terbaik dengan sistem sewa harian, mingguan, maupun bulanan yang fleksibel.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Equipment Catalog -->
    <section id="katalog" class="equipment-section section-padding">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-5">
                <div>
                    <h2 class="section-title mb-0">Unit Siap Pakai</h2>
                    <p class="text-muted mt-2">Daftar alat berat yang tersedia untuk disewa hari ini.</p>
                </div>
                <a href="#" class="btn btn-outline-primary rounded-pill px-4">
                    Lihat Semua <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
            
           <div class="row g-4">
    <?php if ($resultBarang && mysqli_num_rows($resultBarang) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($resultBarang)): ?>
        <div class="col-lg-4 col-md-6" data-aos="fade-up">
            <div class="eq-card">
                <div class="eq-img" style="background: none;">
                    <span class="eq-badge">Tersedia</span>
                    
                    <?php if (!empty($row['gambar'])): ?>
                        <img src="/farel-ukk-/storages/alat/<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama_barang']); ?>" class="img-fluid" style="width: 100%; height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div style="width: 100%; height: 200px; background: linear-gradient(45deg, #e2e8f0, #cbd5e1); display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                            <i class="fas fa-truck-monster" style="font-size: 5rem;"></i>
                        </div>
                    <?php endif; ?>

                </div>
                            <div class="eq-body">
                                <span class="eq-category"><?php echo htmlspecialchars($row['nama_kategori']); ?></span>
                                <h4 class="eq-name"><?php echo htmlspecialchars($row['nama_barang']); ?></h4>
                                <div class="eq-price">
                                    Rp <?php echo number_format($row['harga_sewa_perhari'], 0, ',', '.'); ?> <span>/ hari</span>
                                </div>
                                <a href="pages/auth/login.php" class="btn-rent">
                                    <i class="fas fa-shopping-cart me-2"></i>Sewa Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5" data-aos="fade-up">
                        <img src="https://illustrations.popsy.co/gray/container-ship.svg" height="200" class="mb-4">
                        <h3>Maaf, saat ini belum ada unit tersedia.</h3>
                        <p class="text-muted">Silakan hubungi admin kami untuk informasi lebih lanjut.</p>
                        <a href="#kontak" class="btn btn-primary mt-3">Hubungi Kami</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="tentang" class="testimonials-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title section-title-center">Apa Kata Klien Kami</h2>
                <p class="text-muted">Kepercayaan klien adalah prioritas utama kami</p>
            </div>
            <div class="row g-4">
                <?php foreach ($testimonials as $index => $testimonial): ?>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <i class="fas fa-quote-left text-primary me-2 opacity-50"></i>
                            <?php echo htmlspecialchars($testimonial['content']); ?>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar"><?php echo $testimonial['avatar']; ?></div>
                            <div class="author-info">
                                <h5><?php echo htmlspecialchars($testimonial['name']); ?></h5>
                                <p><?php echo htmlspecialchars($testimonial['position']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title section-title-center">Gallery Proyek</h2>
                <p class="text-muted">Dokumentasi proyek-proyek yang telah kami dukung</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up">
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Proyek 1">
                        <div class="gallery-overlay">
                            <h4>Proyek Bendungan</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Proyek 2">
                        <div class="gallery-overlay">
                            <h4>Konstruksi Jalan Tol</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Proyek 3">
                        <div class="gallery-overlay">
                            <h4>Proyek Pertambangan</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Partner Logos -->
    <section class="partner-section">
        <div class="container">
            <div class="row justify-content-center align-items-center g-4">
                <div class="col-6 col-md-2 text-center">
                    <div class="partner-logo">WIKA</div>
                </div>
                <div class="col-6 col-md-2 text-center">
                    <div class="partner-logo">PP</div>
                </div>
                <div class="col-6 col-md-2 text-center">
                    <div class="partner-logo">ADHI</div>
                </div>
                <div class="col-6 col-md-2 text-center">
                    <div class="partner-logo">TOTAL</div>
                </div>
                <div class="col-6 col-md-2 text-center">
                    <div class="partner-logo">PERTAMINA</div>
                </div>
                <div class="col-6 col-md-2 text-center">
                    <div class="partner-logo">ASTRA</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Siap Memulai Proyek Anda?</h2>
                <p class="cta-subtitle">Hubungi tim kami sekarang untuk konsultasi gratis dan penawaran terbaik</p>
                <div>
                    <a href="#" class="btn-cta">
                        <i class="fas fa-phone-alt me-2"></i>Hubungi Kami
                    </a>
                    <a href="#" class="btn-cta btn-cta-outline">
                        <i class="fab fa-whatsapp me-2"></i>WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="kontak">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <a href="#" class="footer-logo">
                        <i class="fas fa-truck-monster me-2 text-primary"></i>HeavyHire
                    </a>
                    <p>Solusi terpercaya untuk kebutuhan infrastruktur dan proyek pembangunan Anda. Menyediakan layanan sewa alat berat berkualitas tinggi sejak 2024.</p>
                    <div class="mt-4">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-2">
                    <h5 class="text-white mb-4">Layanan</h5>
                    <a href="#" class="footer-link">Sewa Excavator</a>
                    <a href="#" class="footer-link">Sewa Bulldozer</a>
                    <a href="#" class="footer-link">Sewa Crane</a>
                    <a href="#" class="footer-link">Sewa Wheel Loader</a>
                    <a href="#" class="footer-link">Sewa Dump Truck</a>
                </div>
                <div class="col-lg-2">
                    <h5 class="text-white mb-4">Informasi</h5>
                    <a href="#" class="footer-link">Katalog Alat</a>
                    <a href="#" class="footer-link">Cara Peminjaman</a>
                    <a href="#" class="footer-link">Kebijakan Privasi</a>
                    <a href="#" class="footer-link">Syarat & Ketentuan</a>
                    <a href="#" class="footer-link">FAQ</a>
                </div>
                <div class="col-lg-4">
                    <h5 class="text-white mb-4">Kontak Kami</h5>
                    <p><i class="fas fa-map-marker-alt me-3 text-primary"></i> Jl. Konstruksi No. 123, Jakarta Selatan</p>
                    <p><i class="fas fa-phone me-3 text-primary"></i> +62 21 5555 8888</p>
                    <p><i class="fas fa-envelope me-3 text-primary"></i> hello@heavyhire.com</p>
                    <p><i class="fas fa-clock me-3 text-primary"></i> Senin - Jumat, 08:00 - 17:00</p>
                </div>
            </div>
            <hr class="my-5" style="border-color: rgba(255,255,255,0.1);">
            <div class="text-center">
                <p class="mb-0">© 2024 HeavyHire Ecosystem. All rights reserved. | Designed with <i class="fas fa-heart text-danger"></i> for better construction</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top -->
    <button class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Floating Contact -->
    <div class="floating-contact">
        <a href="#" class="whatsapp" title="WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
        <a href="#" class="phone" title="Telepon">
            <i class="fas fa-phone"></i>
        </a>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });

        // Loading Screen
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('loading').classList.add('hidden');
            }, 500);
        });

        // Navbar Scroll
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                document.querySelector('.navbar').classList.add('scrolled');
            } else {
                document.querySelector('.navbar').classList.remove('scrolled');
            }

            // Back to Top Button
            if (window.scrollY > 500) {
                document.getElementById('backToTop').classList.add('visible');
            } else {
                document.getElementById('backToTop').classList.remove('visible');
            }
        });

        // Back to Top
        document.getElementById('backToTop').addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Scroll to Catalog
        function scrollToCatalog() {
            document.getElementById('katalog').scrollIntoView({
                behavior: 'smooth'
            });
        }

        // Smooth Scroll for Nav Links
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId && targetId !== '#') {
                    const target = document.querySelector(targetId);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                }
            });
        });

        // Auto-hide loading screen if page loads slowly
        setTimeout(function() {
            document.getElementById('loading').classList.add('hidden');
        }, 3000);

        // Add active class to nav links on scroll
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('section');
            const navLinks = document.querySelectorAll('.nav-link');
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 100;
                const sectionBottom = sectionTop + section.offsetHeight;
                const scroll = window.scrollY;

                if (scroll >= sectionTop && scroll < sectionBottom) {
                    const id = section.getAttribute('id');
                    navLinks.forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === '#' + id) {
                            link.classList.add('active');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>