<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Heavy Gear Rental System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --heavy-yellow: #ffcc00;
            --heavy-dark: #1a1a1a;
            --heavy-gray: #333333;
            --accent-blue: #0056b3;
        }
        
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)), 
                        url('https://images.unsplash.com/photo-1581091226033-d5c48150dbaa?auto=format&fit=crop&q=80&w=1600'); /* BG Excavator besar */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
            position: relative;
        }
        
        /* Floating Heavy Equipment Images */
        .floating-equipment {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 5;
        }
        
        .equipment-item {
            position: absolute;
            opacity: 0.15;
            filter: drop-shadow(0 0 20px rgba(255,204,0,0.3));
            animation: floatAnimation 20s infinite ease-in-out;
        }
        
        @keyframes floatAnimation {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(5deg); }
        }
        
        .excavator-1 {
            top: 10%;
            left: 5%;
            width: 300px;
            animation-delay: 0s;
        }
        
        .bulldozer-1 {
            bottom: 5%;
            right: 5%;
            width: 350px;
            animation-delay: 2s;
        }
        
        .crane-1 {
            top: 20%;
            right: 10%;
            width: 250px;
            animation-delay: 4s;
        }
        
        .dump-truck-1 {
            bottom: 15%;
            left: 10%;
            width: 320px;
            animation-delay: 6s;
        }
        
        .wheel-loader {
            top: 60%;
            left: 20%;
            width: 280px;
            animation-delay: 3s;
        }
        
        /* Gambar Alat Berat di sisi login card */
        .side-equipment {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 15;
        }
        
        .side-left {
            position: absolute;
            left: 50px;
            top: 50%;
            transform: translateY(-50%);
            width: 250px;
            opacity: 0.9;
            filter: drop-shadow(0 0 30px rgba(255,204,0,0.5));
            animation: sideLeftMove 6s infinite alternate;
        }
        
        @keyframes sideLeftMove {
            from { transform: translateY(-50%) translateX(0); }
            to { transform: translateY(-50%) translateX(20px); }
        }
        
        .side-right {
            position: absolute;
            right: 50px;
            top: 50%;
            transform: translateY(-50%);
            width: 250px;
            opacity: 0.9;
            filter: drop-shadow(0 0 30px rgba(255,204,0,0.5));
            animation: sideRightMove 6s infinite alternate;
        }
        
        @keyframes sideRightMove {
            from { transform: translateY(-50%) translateX(0); }
            to { transform: translateY(-50%) translateX(-20px); }
        }
        
        /* Login Container */
        .login-container {
            width: 100%;
            max-width: 450px;
            perspective: 1000px;
            position: relative;
            z-index: 30;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.8), 0 0 0 2px var(--heavy-yellow);
            overflow: hidden;
            border-top: 6px solid var(--heavy-yellow);
            animation: slideUp 0.6s ease-out, cardPulse 3s infinite;
            backdrop-filter: blur(5px);
            position: relative;
        }
        
        @keyframes cardPulse {
            0%, 100% { box-shadow: 0 25px 50px rgba(0, 0, 0, 0.8), 0 0 0 2px var(--heavy-yellow); }
            50% { box-shadow: 0 30px 60px rgba(0, 0, 0, 0.9), 0 0 0 4px var(--heavy-yellow); }
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px) rotateX(10deg); }
            to { opacity: 1; transform: translateY(0) rotateX(0); }
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--heavy-dark) 0%, #000 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
            border-bottom: none;
            position: relative;
            overflow: hidden;
        }
        
        .card-header::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,204,0,0.2) 0%, transparent 70%);
            animation: rotate 10s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .card-header::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 10px;
            background: repeating-linear-gradient(
                45deg,
                #ffcc00,
                #ffcc00 10px,
                #1a1a1a 10px,
                #1a1a1a 20px
            );
        }
        
        .brand-logo {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.8rem;
            letter-spacing: 2px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            position: relative;
            z-index: 2;
            text-shadow: 0 0 10px rgba(255,204,0,0.5);
        }
        
        .brand-logo i {
            color: var(--heavy-yellow);
            filter: drop-shadow(0 0 10px rgba(255, 204, 0, 0.8));
            font-size: 2.5rem;
            animation: spin 4s ease-in-out infinite;
        }
        
        @keyframes spin {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(15deg); }
            75% { transform: rotate(-15deg); }
        }
        
        .welcome-text {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
            font-weight: 700;
            position: relative;
            z-index: 2;
            animation: glow 2s ease-in-out infinite;
        }
        
        @keyframes glow {
            0%, 100% { text-shadow: 0 0 5px rgba(255,204,0,0.5); }
            50% { text-shadow: 0 0 20px rgba(255,204,0,0.8); }
        }
        
        .card-body {
            padding: 40px;
            position: relative;
        }
        
        /* Gambar kecil di dalam card */
        .card-equipment {
            position: absolute;
            bottom: 10px;
            right: 10px;
            width: 80px;
            opacity: 0.3;
            transform: rotate(-10deg);
            transition: all 0.3s;
        }
        
        .card-equipment:hover {
            opacity: 1;
            transform: rotate(0deg) scale(1.2);
        }
        
        .card-equipment.left {
            left: 10px;
            right: auto;
            transform: rotate(10deg);
        }
        
        .form-label {
            font-weight: 700;
            color: var(--heavy-dark);
            font-size: 0.9rem;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-label i {
            color: var(--heavy-yellow);
        }
        
        .input-group-custom {
            position: relative;
            margin-bottom: 25px;
        }
        
        .input-group-custom .form-control {
            padding-left: 50px;
            height: 55px;
            border-radius: 10px;
            border: 2px solid #ddd;
            font-weight: 600;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .input-group-custom .form-control:focus {
            border-color: var(--heavy-yellow);
            box-shadow: 0 0 20px rgba(255, 204, 0, 0.4);
            outline: none;
            transform: scale(1.02);
            background: white;
        }
        
        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            font-size: 1.2rem;
            z-index: 10;
            transition: all 0.3s;
        }
        
        .input-group-custom:hover .input-icon {
            color: var(--heavy-yellow);
            transform: translateY(-50%) scale(1.2);
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--heavy-dark) 0%, #000 100%);
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-weight: 700;
            font-size: 1rem;
            color: white;
            text-transform: uppercase;
            width: 100%;
            transition: all 0.3s;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            position: relative;
            overflow: hidden;
            border: 1px solid var(--heavy-yellow);
        }
        
        .btn-login::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255,204,0,0.3);
            transform: translate(-50%, -50%);
            transition: width 0.5s, height 0.5s;
        }
        
        .btn-login:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-login:hover {
            background: #000;
            color: var(--heavy-yellow);
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255,204,0,0.3);
        }
        
        .btn-login i {
            color: var(--heavy-yellow);
            transition: all 0.3s;
        }
        
        .btn-login:hover i {
            transform: rotate(360deg);
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            font-weight: 600;
        }

        .register-link a {
            color: var(--accent-blue);
            text-decoration: none;
            border-bottom: 2px solid transparent;
            transition: all 0.3s;
        }

        .register-link a:hover {
            border-bottom-color: var(--accent-blue);
            letter-spacing: 1px;
        }

        .alert-custom {
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 20px;
            border-left: 5px solid;
            animation: alertSlide 0.5s ease-out;
        }
        
        @keyframes alertSlide {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        /* Loading overlay untuk gambar */
        .image-loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--heavy-dark);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeOut 1s forwards 2s;
            pointer-events: none;
        }
        
        @keyframes fadeOut {
            to { opacity: 0; visibility: hidden; }
        }
        
        .loading-text {
            color: var(--heavy-yellow);
            font-family: 'Orbitron', sans-serif;
            font-size: 2rem;
            animation: loadingPulse 1s infinite;
        }

        @media (max-width: 1200px) {
            .side-left, .side-right {
                display: none;
            }
        }
        
        @media (max-width: 500px) {
            .login-container { padding: 15px; }
            .card-body { padding: 25px; }
            .floating-equipment { opacity: 0.1; }
        }
    </style>
</head>

<body>
    <!-- Loading Screen -->
    <div class="image-loading">
        <div class="loading-text">
            <i class="fas fa-cog fa-spin"></i> LOADING HEAVY EQUIPMENT...
        </div>
    </div>
    
    <!-- Floating Heavy Equipment Images (Background) -->
    <div class="floating-equipment">
        <img src="https://www.volvoce.com/-/media/volvoce/global/products/excavators/crawler-excavators/ec480/images/volvo-ec480-crawler-excavator-product-image-hero.jpg?mw=1920" 
             class="equipment-item excavator-1" alt="Excavator" style="opacity: 0.2;">
        
        <img src="https://www.volvoce.com/-/media/volvoce/global/products/dozers/wheel-dozers/l150h/images/volvo-l150h-wheel-loader-product-image.jpg?mw=1920" 
             class="equipment-item bulldozer-1" alt="Wheel Loader" style="opacity: 0.2;">
        
        <img src="https://www.liebherr.com/shared/media/construction-machines/products/crawler-excavators/crawler-excavators/liebherr-r-980-crawler-excavator-machine-front.jpg" 
             class="equipment-item crane-1" alt="Crawler Excavator" style="opacity: 0.2;">
        
        <img src="https://www.volvoce.com/-/media/volvoce/global/products/articulated-haulers/a60h/images/volvo-a60h-articulated-hauler-product-image.jpg?mw=1920" 
             class="equipment-item dump-truck-1" alt="Dump Truck" style="opacity: 0.2;">
        
        <img src="https://www.volvoce.com/-/media/volvoce/global/products/wheel-loaders/l350h/images/volvo-l350h-wheel-loader-product-image.jpg?mw=1920" 
             class="equipment-item wheel-loader" alt="Wheel Loader" style="opacity: 0.2;">
    </div>
    
    <!-- Side Equipment (Close to Login Card) -->
    <div class="side-equipment">
        <img src="https://www.volvoce.com/-/media/volvoce/global/products/excavators/crawler-excavators/ec950/images/volvo-ec950-crawler-excavator-product-image.jpg?mw=1920" 
             class="side-left" alt="Excavator">
        
        <img src="https://www.volvoce.com/-/media/volvoce/global/products/dozers/track-dozers/volvo-td-track-dozer-family.jpg?mw=1920" 
             class="side-right" alt="Bulldozer">
    </div>

    <div class="login-container">
        <div class="login-card">
            <!-- Small Equipment Icons inside card -->
            <img src="https://www.volvoce.com/-/media/volvoce/global/products/excavators/crawler-excavators/ec480/images/volvo-ec480-crawler-excavator-product-image-icon.png" 
                 class="card-equipment left" alt="Excavator Icon">
            <img src="https://www.volvoce.com/-/media/volvoce/global/products/articulated-haulers/a60h/images/volvo-a60h-articulated-hauler-product-image-icon.png" 
                 class="card-equipment" alt="Hauler Icon">
            
            <div class="card-header">
                <div class="brand-logo">
                    <i class="fas fa-hard-hat"></i>
                    <span>HEAVY <span style="color: var(--heavy-yellow)">GEAR</span><br><small style="font-size: 0.8rem;">RENTAL SYSTEM</small></span>
                </div>
                <p class="welcome-text">Heavy Equipment Rental Management</p>
            </div>
            
            <div class="card-body">
                <?php 
                if (isset($_GET['pesan']) && $_GET['pesan'] == 'gagal'): 
                    $error = $_SESSION['login_error'] ?? "Akses Ditolak! Kredensial Salah.";
                ?>
                    <div class="alert alert-danger alert-custom border-0 shadow-sm" style="border-left: 5px solid #e74a3b !important;">
                        <i class="fas fa-triangle-exclamation me-2"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php 
                    unset($_SESSION['login_error']);
                endif; 
                ?>

                <?php if (isset($_SESSION['register_success'])): ?>
                    <div class="alert alert-success alert-custom border-0 shadow-sm" style="border-left: 5px solid #1cc88a !important;">
                        <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($_SESSION['register_success']) ?>
                    </div>
                <?php 
                    unset($_SESSION['register_success']);
                endif; 
                ?>

                <form action="../../../backend/action/auth/login_proses.php" method="POST" id="loginForm">
                    
                    <label class="form-label">
                        <i class="fas fa-user-hard-hat"></i> Username Operator
                    </label>
                    <div class="input-group-custom">
                        <i class="fas fa-user-shield input-icon"></i>
                        <input type="text" class="form-control" name="username" placeholder="Masukkan username" required>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <label class="form-label">
                            <i class="fas fa-lock"></i> Sandi Keamanan
                        </label>
                        <a href="#" class="text-muted small" style="text-decoration:none">
                            <i class="fas fa-question-circle"></i> Lupa Sandi?
                        </a>
                    </div>
                    <div class="input-group-custom">
                        <i class="fas fa-key input-icon"></i>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan Password" required>
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-unlock-alt"></i>
                        <span>Operasikan Sistem</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                    
                    <div class="register-link">
                        <i class="fas fa-helmet-safety"></i> Belum punya akses? 
                        <a href="register.php">Hubungi Admin / Daftar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animasi Loading saat submit
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = this.querySelector('.btn-login');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memverifikasi...';
            btn.style.opacity = '0.8';
            btn.style.pointerEvents = 'none';
        });
        
        // Preload images
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.querySelector('.image-loading').style.display = 'none';
            }, 2000);
        });
        
        // Random equipment movement
        const equipment = document.querySelectorAll('.equipment-item');
        equipment.forEach(item => {
            item.addEventListener('mouseover', function() {
                this.style.opacity = '0.4';
            });
            item.addEventListener('mouseout', function() {
                this.style.opacity = '0.2';
            });
        });
        
        // Dynamic year
        document.addEventListener('DOMContentLoaded', function() {
            const yearSpan = document.createElement('span');
            yearSpan.className = 'text-muted small';
            yearSpan.style.position = 'fixed';
            yearSpan.style.bottom = '10px';
            yearSpan.style.right = '10px';
            yearSpan.style.color = '#fff';
            yearSpan.style.zIndex = '1000';
            yearSpan.innerHTML = '© ' + new Date().getFullYear() + ' Heavy Gear Rental';
            document.body.appendChild(yearSpan);
        });
    </script>
</body>
</html>