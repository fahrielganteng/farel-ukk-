<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Peminjaman Alat Berat</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #f6c23e; /* Construction Yellow */
            --secondary-color: #1cc88a;
            --danger-color: #e74a3b;
            --dark-color: #2c3e50;
            --light-color: #f8f9fc;
            --accent-color: #f39c12;
        }
        
        body {
            /* Background bertema industrial/konstruksi */
            background: linear-gradient(135deg, #2c3e50 0%, #000000 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        
        .register-container {
            width: 100%;
            max-width: 800px;
        }
        
        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            animation: fadeIn 0.5s ease-out;
            border-top: 5px solid var(--primary-color);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .card-header {
            background: #1a1a1a;
            color: white;
            padding: 30px;
            text-align: center;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .brand-logo {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            color: var(--primary-color);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .brand-logo i {
            background: var(--primary-color);
            color: #1a1a1a;
            width: 45px;
            height: 45px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: rotate(-5deg);
        }
        
        .welcome-text {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 0;
            color: #eee;
        }
        
        .card-body {
            padding: 40px;
        }
        
        .form-title {
            color: var(--dark-color);
            font-weight: 700;
            margin-bottom: 30px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 22px;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
        }
        
        .form-label.required:after {
            content: " *";
            color: var(--danger-color);
        }
        
        .input-group-custom {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .input-group-custom .form-control {
            padding-left: 45px;
            height: 50px;
            border-radius: 8px;
            border: 2px solid #dee2e6;
            transition: all 0.3s;
        }
        
        .input-group-custom .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(246, 194, 62, 0.2);
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            color: #adb5bd;
            font-size: 1.1rem;
            z-index: 4;
        }
        
        .btn-register {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            border: none;
            border-radius: 8px;
            padding: 15px;
            font-weight: 700;
            font-size: 1.1rem;
            color: #1a1a1a;
            width: 100%;
            transition: all 0.3s;
            text-transform: uppercase;
            margin-top: 15px;
        }
        
        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(246, 194, 62, 0.4);
            color: #000;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            background: none;
            border: none;
            color: #adb5bd;
            cursor: pointer;
            z-index: 5;
        }
        
        .terms-checkbox {
            background: #fff9e6;
            border: 1px dashed var(--primary-color);
            border-radius: 8px;
            padding: 15px;
            margin: 25px 0;
        }

        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 8px;
            transition: width 0.3s ease;
        }

        .strength-weak { background: #e74a3b; width: 33%; }
        .strength-medium { background: #f6c23e; width: 66%; }
        .strength-strong { background: #1cc88a; width: 100%; }

        @media (max-width: 576px) {
            .card-body { padding: 20px; }
            .brand-logo { font-size: 1.4rem; }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="register-card">
            <div class="card-header">
                <div class="brand-logo">
                    <i class="fas fa-hard-hat"></i>
                    <span>PEMINJAMAN
ALAT BERAT</span>
                </div>
                <p class="welcome-text">Sistem Manajemen Peminjaman Alat Berat Perusahaan</p>
            </div>
            
            <div class="card-body">
                <h3 class="form-title">
                    <i class="fas fa-file-signature"></i>
                    Registrasi Operator / Client
                </h3>

                <?php 
                if (isset($_SESSION['register_errors'])): 
                ?>
                    <div class="alert alert-danger mb-4 border-0 shadow-sm" style="border-left: 5px solid #e74a3b !important;">
                        <ul class="mb-0 fw-600">
                            <?php foreach ($_SESSION['register_errors'] as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php 
                    unset($_SESSION['register_errors']);
                endif; 
                ?>
                
                <form action="../../../backend/action/auth/register_proses.php" method="POST" id="registerForm" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="username" class="form-label required">Username</label>
                                <div class="input-group-custom">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" class="form-control" id="username" name="username" placeholder="Username login" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="nama_lengkap" class="form-label required">Nama Lengkap & Gelar</label>
                        <div class="input-group-custom">
                            <i class="fas fa-address-card input-icon"></i>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="Nama sesuai KTP/Sertifikat" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="alamat" class="form-label">Alamat Lengkap</label>
                        <div class="input-group-custom">
                            <i class="fas fa-map-marker-alt input-icon"></i>
                            <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Alamat tinggal/kantor">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label">Email Perusahaan</label>
                                <div class="input-group-custom">
                                    <i class="fas fa-envelope-open-text input-icon"></i>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="email@company.com">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="no_telp" class="form-label required">No. WhatsApp/HP</label>
                                <div class="input-group-custom">
                                    <i class="fas fa-phone-alt input-icon"></i>
                                    <input type="tel" class="form-control" id="no_telp" name="no_telp" placeholder="08XXXXXXXXXX" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password" class="form-label required">Password</label>
                                <div class="input-group-custom">
                                    <i class="fas fa-key input-icon"></i>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 6 karakter" required minlength="6">
                                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div id="passwordStrength" class="password-strength"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="confirm_password" class="form-label required">Konfirmasi Password</label>
                                <div class="input-group-custom">
                                    <i class="fas fa-check-double input-icon"></i>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Ulangi password" required>
                                    <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="form-group">
                        <label for="divisi" class="form-label">Divisi / Unit Kerja</label>
                        <div class="input-group-custom">
                            <i class="fas fa-shovels input-icon"></i>
                            <select class="form-select form-control" style="padding-left: 45px;" name="divisi">
                                <option value="">-- Pilih Divisi --</option>
                                <option value="operasional">Operasional Lapangan</option>
                                <option value="logistik">Logistik & Inventory</option>
                                <option value="maintenance">Maintenance/Mekanik</option>
                                <option value="project_manager">Project Manager</option>
                            </select>
                        </div>
                    </div> -->
                    
                    <div class="terms-checkbox">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">
                                Saya bersedia mematuhi <strong>SOP K3</strong> dan bertanggung jawab atas unit yang dipinjam.
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-register">
                        <i class="fas fa-truck-monster me-2"></i> Daftar Akun
                    </button>
                    
                    <div class="text-center mt-4">
                        <span class="text-muted">Sudah punya akses?</span> <a href="login.php" class="text-decoration-none fw-bold" style="color: var(--accent-color);">Masuk Logistik</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = input.parentElement.querySelector('.password-toggle i');
            if (input.type === "password") {
                input.type = "text";
                icon.className = "fas fa-eye-slash";
            } else {
                input.type = "password";
                icon.className = "fas fa-eye";
            }
        }

        // Real-time strength check
        document.getElementById('password').addEventListener('input', function() {
            const val = this.value;
            const bar = document.getElementById('passwordStrength');
            bar.className = 'password-strength';
            if(val.length > 0 && val.length < 6) bar.classList.add('strength-weak');
            else if(val.length >= 6 && val.length < 10) bar.classList.add('strength-medium');
            else if(val.length >= 10) bar.classList.add('strength-strong');
        });
    </script>
</body>
</html>