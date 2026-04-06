<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HeavyRent - Solusi Peminjaman Alat Berat Terpercaya</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        :root {
            --primary: #fbbf24;
            --primary-dark: #f59e0b;
            --dark: #0f172a;
            --dark-light: #1e293b;
            --text-light: #cbd5e1;
            --text-dark: #94a3b8;
            --gradient: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        }
        
        body {
            background-color: var(--dark);
            color: #fff;
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--dark-light);
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
            background: var(--dark);
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
            width: 80px;
            height: 80px;
            border: 5px solid var(--dark-light);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Bagian Atas dengan Video Background */
        .top-section {
            position: relative;
            height: 100vh;
            min-height: 700px;
            overflow: hidden;
        }
        
        .video-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        
        #hero-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                135deg,
                rgba(15, 23, 42, 0.95) 0%,
                rgba(15, 23, 42, 0.7) 50%,
                rgba(15, 23, 42, 0.4) 100%
            );
            z-index: 2;
        }
        
        /* Animated Border */
        .animated-border {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 2px solid transparent;
            z-index: 3;
            pointer-events: none;
            animation: borderGlow 3s infinite;
        }
        
        @keyframes borderGlow {
            0%, 100% { border-color: rgba(251, 191, 36, 0.3); box-shadow: 0 0 30px rgba(251, 191, 36, 0.1); }
            50% { border-color: rgba(251, 191, 36, 0.8); box-shadow: 0 0 50px rgba(251, 191, 36, 0.3); }
        }
        
        /* Konten di atas video */
        .top-content {
            position: relative;
            z-index: 4;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header Styles */
        header {
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: all 0.3s;
        }
        
        header.scrolled {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 2px;
            color: var(--primary);
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }
        
        .logo span {
            color: #fff;
        }
        
        .logo::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--gradient);
            transform: translateX(-100%);
            transition: transform 0.3s;
        }
        
        .logo:hover::before {
            transform: translateX(0);
        }
        
        .nav-links {
            display: flex;
            gap: 30px;
        }
        
        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
            position: relative;
            padding: 5px 0;
        }
        
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--gradient);
            transition: width 0.3s;
        }
        
        .nav-links a:hover::after {
            width: 100%;
        }
        
        .nav-links a:hover {
            color: var(--primary);
            transform: translateY(-2px);
        }
        
        /* Dropdown Menu */
        .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-content {
            position: absolute;
            top: 100%;
            left: 0;
            background: var(--dark-light);
            min-width: 200px;
            border-radius: 10px;
            padding: 10px 0;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(251, 191, 36, 0.2);
            z-index: 100;
        }
        
        .dropdown:hover .dropdown-content {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-content a {
            display: block;
            padding: 10px 20px;
            color: var(--text-light);
            transition: all 0.3s;
        }
        
        .dropdown-content a:hover {
            background: rgba(251, 191, 36, 0.1);
            color: var(--primary);
            padding-left: 30px;
        }
        
        .cta-button {
            background: var(--gradient);
            color: var(--dark);
            border: none;
            padding: 12px 28px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .cta-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            z-index: -1;
            transition: opacity 0.3s;
            opacity: 0;
        }
        
        .cta-button:hover::before {
            opacity: 1;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(251, 191, 36, 0.4);
            color: var(--dark);
        }
        
        /* Hero Section */
        .hero {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 40px 0;
        }
        
        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }
        
        .hero-text h1 {
            font-size: 52px;
            line-height: 1.2;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 20px;
            animation: fadeInUp 1s;
        }
        
        .hero-text h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100px;
            height: 3px;
            background: var(--gradient);
        }
        
        .hero-text h1 span {
            color: var(--primary);
            position: relative;
            display: inline-block;
        }
        
        .hero-text h1 span::before {
            content: '';
            position: absolute;
            bottom: 5px;
            left: 0;
            width: 100%;
            height: 8px;
            background: rgba(251, 191, 36, 0.2);
            z-index: -1;
        }
        
        .hero-text p {
            font-size: 18px;
            color: var(--text-light);
            margin-bottom: 30px;
            max-width: 500px;
            animation: fadeInUp 1s 0.2s both;
        }
        
        .hero-buttons {
            display: flex;
            gap: 15px;
            animation: fadeInUp 1s 0.4s both;
        }
        
        .hero-image {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            animation: float 3s ease-in-out infinite;
        }
        
        .hero-image img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .hero-image::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: var(--gradient);
            border-radius: 12px;
            z-index: -1;
            animation: rotate 4s linear infinite;
        }
        
        @keyframes rotate {
            0% { filter: hue-rotate(0deg); }
            100% { filter: hue-rotate(360deg); }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        
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
        
        /* Scroll Indicator */
        .scroll-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 4;
            text-align: center;
            color: #fff;
            cursor: pointer;
            transition: opacity 0.3s;
        }
        
        .mouse {
            width: 30px;
            height: 50px;
            border: 2px solid var(--primary);
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
            background: var(--primary);
            border-radius: 2px;
            animation: scroll 2s infinite;
        }
        
        @keyframes scroll {
            0% { opacity: 1; transform: translateX(-50%) translateY(0); }
            100% { opacity: 0; transform: translateX(-50%) translateY(20px); }
        }
        
        /* Stats Section */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            margin: 80px 0;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px 20px;
            text-align: center;
            transition: all 0.3s;
            border: 1px solid rgba(251, 191, 36, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, transparent, rgba(251, 191, 36, 0.1), transparent);
            transform: translateX(-100%);
            transition: transform 0.5s;
        }
        
        .stat-card:hover::before {
            transform: translateX(100%);
        }
        
        .stat-card:hover {
            transform: translateY(-10px);
            border-color: var(--primary);
            box-shadow: 0 20px 40px rgba(251, 191, 36, 0.2);
        }
        
        .stat-value {
            font-size: 42px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 5px;
            position: relative;
            display: inline-block;
        }
        
        .stat-value::after {
            content: '+';
            position: absolute;
            top: 0;
            right: -20px;
            font-size: 24px;
        }
        
        .stat-label {
            font-size: 14px;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Section Title */
        .section-title {
            text-align: center;
            font-size: 36px;
            margin-bottom: 50px;
            color: #fff;
            position: relative;
            padding-bottom: 20px;
        }
        
        .section-title::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: var(--gradient);
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 20px;
            background: var(--primary);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: translateX(-50%) scale(1); opacity: 1; }
            50% { transform: translateX(-50%) scale(1.5); opacity: 0.5; }
        }
        
        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-bottom: 50px;
        }
        
        .feature-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s;
            border: 1px solid rgba(251, 191, 36, 0.1);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }
        
        .feature-card:hover {
            transform: translateY(-10px) scale(1.02);
            border-color: var(--primary);
            box-shadow: 0 30px 60px rgba(251, 191, 36, 0.2);
        }
        
        .feature-icon {
            font-size: 48px;
            color: var(--primary);
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        
        .feature-icon::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            background: rgba(251, 191, 36, 0.1);
            border-radius: 50%;
            z-index: -1;
            animation: pulseIcon 2s infinite;
        }
        
        @keyframes pulseIcon {
            0%, 100% { transform: translate(-50%, -50%) scale(1); }
            50% { transform: translate(-50%, -50%) scale(1.2); }
        }
        
        .feature-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: #fff;
        }
        
        .feature-card p {
            color: var(--text-light);
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .price-tag {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
        }
        
        .price-tag small {
            font-size: 14px;
            font-weight: 400;
            color: var(--text-light);
        }
        
        /* Advantages Grid */
        .advantages-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            margin: 50px 0;
        }
        
        .advantage-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(5px);
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            border: 1px solid rgba(251, 191, 36, 0.1);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .advantage-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--gradient);
            transform: translateX(-100%);
            transition: transform 0.3s;
        }
        
        .advantage-card:hover::before {
            transform: translateX(0);
        }
        
        .advantage-card:hover {
            transform: translateY(-5px);
            background: rgba(251, 191, 36, 0.05);
            border-color: var(--primary);
        }
        
        .advantage-card i {
            font-size: 40px;
            color: var(--primary);
            margin-bottom: 15px;
            transition: transform 0.3s;
        }
        
        .advantage-card:hover i {
            transform: scale(1.1) rotate(360deg);
        }
        
        .advantage-card h4 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #fff;
        }
        
        .advantage-card p {
            color: var(--text-light);
            font-size: 13px;
        }
        
        /* Testimonials Section */
        .testimonials {
            margin: 100px 0;
            position: relative;
        }
        
        .testimonial-slider {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }
        
        .testimonial-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            border: 1px solid rgba(251, 191, 36, 0.1);
            position: relative;
        }
        
        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 60px;
            color: var(--primary);
            opacity: 0.3;
            font-family: serif;
        }
        
        .testimonial-content {
            margin-bottom: 20px;
            color: var(--text-light);
            font-style: italic;
            line-height: 1.8;
            padding-left: 30px;
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
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--dark);
        }
        
        .author-info h5 {
            color: #fff;
            margin-bottom: 5px;
        }
        
        .author-info p {
            color: var(--text-light);
            font-size: 12px;
        }
        
        /* Gallery Section */
        .gallery {
            margin: 100px 0;
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        
        .gallery-item {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
        }
        
        .gallery-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.5s;
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
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            display: flex;
            align-items: flex-end;
            padding: 20px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }
        
        .gallery-overlay h4 {
            color: #fff;
            transform: translateY(20px);
            transition: transform 0.3s;
        }
        
        .gallery-item:hover .gallery-overlay h4 {
            transform: translateY(0);
        }
        
        /* Form Peminjaman */
        .rental-form {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            margin: 50px 0;
            border: 1px solid rgba(251, 191, 36, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .rental-form::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(251, 191, 36, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            position: relative;
            z-index: 1;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-light);
            font-weight: 600;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(251, 191, 36, 0.2);
            border-radius: 8px;
            color: #fff;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(251, 191, 36, 0.05);
            box-shadow: 0 0 20px rgba(251, 191, 36, 0.2);
        }
        
        .form-group input:hover,
        .form-group select:hover,
        .form-group textarea:hover {
            border-color: var(--primary);
        }
        
        .form-full {
            grid-column: span 2;
        }
        
        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, rgba(251, 191, 36, 0.1) 0%, transparent 100%);
            border-radius: 20px;
            padding: 60px 40px;
            margin: 80px 0;
            text-align: center;
            border: 1px solid rgba(251, 191, 36, 0.2);
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.1"><path d="M20 20 L80 20 L50 80 Z" fill="%23fbbf24"/></svg>') repeat;
            animation: slide 20s linear infinite;
        }
        
        @keyframes slide {
            from { background-position: 0 0; }
            to { background-position: 100px 100px; }
        }
        
        .cta-section h3 {
            font-size: 36px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        
        .cta-section p {
            font-size: 18px;
            color: var(--text-light);
            max-width: 700px;
            margin: 0 auto 30px;
            position: relative;
            z-index: 1;
        }
        
        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            position: relative;
            z-index: 1;
        }
        
        .cta-button-secondary {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
            padding: 12px 28px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .cta-button-secondary::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--gradient);
            z-index: -1;
            transition: transform 0.3s;
            transform: scaleX(0);
            transform-origin: right;
        }
        
        .cta-button-secondary:hover::before {
            transform: scaleX(1);
            transform-origin: left;
        }
        
        .cta-button-secondary:hover {
            color: var(--dark);
            border-color: transparent;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(251, 191, 36, 0.3);
        }
        
        /* Partner Logos */
        .partner-logos {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 50px;
            flex-wrap: wrap;
            margin: 50px 0;
        }
        
        .partner-logos div {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-light);
            padding: 10px 20px;
            border: 1px solid rgba(251, 191, 36, 0.2);
            border-radius: 10px;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(5px);
        }
        
        .partner-logos div:hover {
            color: var(--primary);
            border-color: var(--primary);
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 10px 20px rgba(251, 191, 36, 0.2);
        }
        
        /* Footer */
        footer {
            background: linear-gradient(to top, var(--dark), var(--dark-light));
            padding: 60px 0 30px;
            text-align: center;
            position: relative;
            margin-top: 80px;
        }
        
        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--gradient);
        }
        
        .footer-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
        }
        
        .footer-logo {
            font-size: 40px;
            font-weight: 800;
            letter-spacing: 3px;
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .footer-logo span {
            color: #fff;
        }
        
        .footer-text {
            color: var(--text-light);
            max-width: 600px;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        
        .footer-contact {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            justify-content: center;
            margin: 20px 0;
        }
        
        .footer-contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-light);
            transition: color 0.3s;
        }
        
        .footer-contact-item:hover {
            color: var(--primary);
        }
        
        .footer-contact-item i {
            color: var(--primary);
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin: 20px 0;
        }
        
        .social-links a {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(251, 191, 36, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            transition: all 0.3s;
        }
        
        .social-links a:hover {
            background: var(--gradient);
            color: var(--dark);
            transform: translateY(-5px) rotate(360deg);
        }
        
        .copyright {
            color: var(--text-dark);
            font-size: 14px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid rgba(251, 191, 36, 0.1);
        }
        
        /* Back to Top Button */
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
            color: var(--dark);
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
            z-index: 99;
            border: none;
        }
        
        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
        }
        
        .back-to-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(251, 191, 36, 0.4);
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
            color: #fff;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .floating-contact a.whatsapp {
            background: #25D366;
        }
        
        .floating-contact a.phone {
            background: var(--primary);
            color: var(--dark);
        }
        
        .floating-contact a:hover {
            transform: scale(1.1) translateY(-5px);
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .hero-text h1::after {
                left: 50%;
                transform: translateX(-50%);
            }
            
            .hero-text p {
                margin: 0 auto 30px;
            }
            
            .hero-buttons {
                justify-content: center;
            }
            
            .stats {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .advantages-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .testimonial-slider {
                grid-template-columns: 1fr;
            }
            
            .gallery-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .nav-links {
                position: fixed;