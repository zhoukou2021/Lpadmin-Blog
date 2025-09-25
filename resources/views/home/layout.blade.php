<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'LPadmin - 基于Laravel 10+和PearAdminLayui构建的现代化后台管理系统')</title>
    <meta name="description" content="@yield('description', 'LPadmin是基于Laravel 10+和PearAdminLayui构建的现代化后台管理系统，提供完整的RBAC权限管理功能')">
    <meta name="keywords" content="@yield('keywords', 'LPadmin,Laravel,后台管理系统,RBAC,权限管理,PearAdminLayui')">
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('static/images/favicon.ico') }}" type="image/x-icon">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --text-color: #2c3e50;
            --light-bg: #f8f9fa;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            overflow-x: hidden;
            padding-top: 70px; /* 为固定导航留出空间 */
            width: 100vw; /* 强制body使用视口宽度 */
            max-width: 100vw; /* 防止body超出视口 */
        }

        /* 确保所有主要容器不会超出视口宽度 */
        html {
            overflow-x: hidden;
            width: 100vw;
            max-width: 100vw;
        }

        /* Quick Navigation Styles */
        .quick-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.1);
            z-index: 1000;
            transition: all 0.3s ease;
            width: 100vw; /* 强制使用视口宽度 */
            max-width: 100vw; /* 防止超出视口 */
            /* 移除 overflow-x: hidden，避免遮挡下拉菜单 */
        }

        .quick-nav .container {
            max-width: 100%;
            width: 100%;
            padding-left: 15px;
            padding-right: 15px;
            margin: 0 auto;
        }

        .quick-nav.scrolled {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }

        .quick-nav-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 0;
            max-width: 100%; /* 确保不超出容器 */
            width: 100%;
        }

        .quick-nav-brand {
            display: flex;
            align-items: center;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .nav-logo {
            width: 32px;
            height: 32px;
            margin-right: 0.5rem;
            border-radius: 6px;
            object-fit: contain;
        }

        .quick-nav-menu {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 2rem;
        }

        .quick-nav-menu .nav-link {
            color: var(--text-color);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            transition: all 0.3s ease;
            position: relative;
        }

        .quick-nav-menu .nav-link:hover,
        .quick-nav-menu .nav-link.active {
            background: var(--gradient-primary);
            color: white;
            transform: translateY(-2px);
        }

        .quick-nav-actions {
            display: flex;
            align-items: center;
        }

        .btn-nav-login {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1.5rem;
            background: var(--gradient-secondary);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-nav-login:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* 移动端菜单按钮 */
        .mobile-menu-toggle {
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 40px;
            height: 40px;
            background: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            flex-shrink: 0; /* 防止按钮被压缩 */
        }

        .hamburger-line {
            width: 20px;
            height: 2px;
            background: var(--text-color);
            margin: 2px 0;
            transition: all 0.3s ease;
            border-radius: 1px;
        }

        .mobile-menu-toggle.active .hamburger-line:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .mobile-menu-toggle.active .hamburger-line:nth-child(2) {
            opacity: 0;
        }

        .mobile-menu-toggle.active .hamburger-line:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }

        /* 移动端菜单 */
        .mobile-menu {
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transform: translateY(-100%);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1001; /* 提高z-index，确保显示在导航栏上方 */
            width: 100vw; /* 确保菜单宽度正确 */
            max-width: 100vw;
        }

        .mobile-menu.active {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
        }

        .mobile-menu-content {
            padding: 1rem 0;
        }

        .mobile-nav-link {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: var(--text-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .mobile-nav-link:hover,
        .mobile-nav-link.active {
            background: var(--light-bg);
            color: var(--primary-color);
            border-left-color: var(--secondary-color);
        }

        .mobile-nav-link i {
            width: 20px;
            color: var(--secondary-color);
        }

        /* 响应式导航 */
        @media (max-width: 768px) {
            .quick-nav-menu {
                display: none;
            }

            .mobile-menu-toggle {
                display: flex;
                margin-left: auto;
                flex-shrink: 0; /* 防止按钮被压缩 */
            }

            .quick-nav {
                width: 100vw;
                max-width: 100vw;
            }

            .quick-nav .container {
                max-width: 100%;
                padding-left: 10px; /* 减少移动端内边距 */
                padding-right: 10px;
            }

            .quick-nav-content {
                justify-content: space-between;
                padding: 0.75rem 0;
                width: 100%;
                max-width: 100%;
                min-width: 0; /* 允许flex项目收缩 */
            }

            body {
                padding-top: 60px;
                overflow-x: hidden; /* 确保body不会水平滚动 */
            }

            /* 移动端菜单位置调整 */
            .mobile-menu {
                top: 60px; /* 匹配移动端导航栏高度 */
            }

            .nav-logo {
                width: 28px;
                height: 28px;
            }

            .quick-nav-brand {
                font-size: 1.1rem;
                flex-shrink: 0; /* 防止品牌区域被压缩 */
                min-width: 0; /* 允许内容收缩 */
                overflow: hidden; /* 防止内容溢出 */
            }

            .btn-nav-login {
                padding: 0.4rem 1rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .quick-nav-brand span {
                display: none;
            }

            .btn-nav-login span {
                display: none;
            }

            .btn-nav-login {
                width: 40px;
                height: 40px;
                padding: 0;
                border-radius: 50%;
                justify-content: center;
            }

            /* 超小屏幕时进一步优化 */
            .quick-nav .container {
                padding-left: 8px; /* 进一步减少超小屏幕内边距 */
                padding-right: 8px;
            }

            .quick-nav-content {
                padding: 0.5rem 0;
                gap: 0.5rem; /* 添加元素间距 */
            }

            .nav-logo {
                width: 24px;
                height: 24px;
            }

            .mobile-menu-toggle {
                width: 36px;
                height: 36px;
            }

            .hamburger-line {
                width: 18px;
            }

            /* 超小屏幕按钮进一步缩小 */
            .btn-hero, .btn-outline-hero {
                padding: 8px 16px; /* 进一步缩小按钮 */
                font-size: 0.8rem; /* 更小的字体 */
                border-radius: 25px; /* 调整圆角 */
            }

            .hero-actions {
                gap: 0.3rem; /* 减少按钮间距 */
            }
        }

        .hero-section {
            background: var(--gradient-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            width: 100vw; /* 强制使用视口宽度 */
            max-width: 100vw; /* 防止超出视口 */
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.1)" points="0,1000 1000,0 1000,1000"/></svg>');
            background-size: cover;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero-subtitle {
            font-size: 1.3rem;
            color: rgba(255,255,255,0.9);
            margin-bottom: 2rem;
            font-weight: 300;
        }

        .btn-hero {
            padding: 15px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }

        .btn-primary-hero {
            background: white;
            color: var(--primary-color);
            border: 2px solid white;
        }

        .btn-primary-hero:hover {
            background: transparent;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        .btn-outline-hero {
            background: transparent;
            color: white;
            border: 2px solid white;
            margin-left: 1rem;
        }

        .btn-outline-hero:hover {
            background: white;
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        .section {
            padding: 80px 0;
            width: 100vw; /* 强制使用视口宽度 */
            max-width: 100vw; /* 防止超出视口 */
            overflow-x: hidden; /* 隐藏水平溢出 */
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: var(--gradient-secondary);
            border-radius: 2px;
        }

        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            height: 100%;
            border: none;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: white;
        }

        .admin-screenshot {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .admin-screenshot:hover {
            transform: scale(1.05);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .screenshot-img {
            width: 100%;
            height: auto;
            border-radius: 15px;
        }

        .screenshot-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .admin-screenshot:hover .screenshot-overlay {
            opacity: 1;
        }

        .play-button {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--primary-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .play-button:hover {
            background: white;
            transform: scale(1.1);
        }

        .core-feature-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            height: 100%;
            border: none;
        }

        .core-feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .feature-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .feature-icon-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
            color: white;
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 0;
        }

        .feature-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .feature-list {
            margin-top: 1rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .tech-system-info {
            background: white;
        }

        .info-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            height: 100%;
            border: none;
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .info-card-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 1.5rem;
            text-align: center;
            position: relative;
        }

        .info-card-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 3px;
            background: var(--gradient-secondary);
            border-radius: 2px;
        }

        .tech-item-compact {
            background: var(--light-bg);
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            transition: all 0.3s ease;
            margin-bottom: 0.5rem;
        }

        .tech-item-compact:hover {
            background: var(--gradient-primary);
            color: white;
            transform: scale(1.02);
        }

        .tech-item-compact strong {
            display: block;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .tech-item-compact .text-muted {
            font-size: 0.8rem;
        }

        .tech-item-compact:hover .text-muted {
            color: rgba(255,255,255,0.9) !important;
        }

        /* Documentation Section Styles */
        .documentation {
            background: var(--light-bg);
        }

        .doc-card-home {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            height: 100%;
            border: none;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .doc-card-home:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        .doc-card-home::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--gradient-secondary);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .doc-card-home:hover::before {
            opacity: 1;
        }

        .doc-card-content {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
            flex: 1;
        }

        .doc-icon-wrapper {
            width: 48px;
            height: 48px;
            background: var(--gradient-primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .doc-card-home:hover .doc-icon-wrapper {
            background: var(--gradient-secondary);
            transform: scale(1.05);
        }

        .doc-icon-home {
            font-size: 1.25rem;
            color: white;
        }

        .doc-info {
            flex: 1;
            min-width: 0;
        }

        .doc-title-home {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .doc-description-home {
            color: #666;
            font-size: 0.85rem;
            line-height: 1.4;
            margin-bottom: 0;
        }

        .doc-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }

        .doc-type {
            background: var(--light-bg);
            color: var(--text-color);
            padding: 0.2rem 0.6rem;
            border-radius: 15px;
            font-weight: 500;
            font-size: 0.75rem;
        }

        .btn-doc-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: var(--gradient-primary);
            color: white;
            text-decoration: none;
            border-radius: 50%;
            font-size: 0.8rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-doc-icon:hover {
            background: var(--gradient-secondary);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }

        .footer {
            background: var(--primary-color);
            color: white;
            padding: 3rem 0 2rem;
        }

        .footer a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: white;
        }

        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .shape {
            position: absolute;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            /* 移动端按钮样式调整 */
            .btn-hero, .btn-outline-hero {
                padding: 10px 20px; /* 缩小按钮尺寸 */
                font-size: 0.9rem; /* 缩小字体 */
                margin: 0; /* 重置margin */
            }

            .btn-outline-hero {
                margin-left: 0.5rem; /* 按钮间距 */
            }

            /* 确保按钮在一行显示 */
            .hero-actions {
                display: flex;
                flex-wrap: nowrap; /* 不换行 */
                gap: 0.5rem; /* 按钮间距 */
                justify-content: flex-start; /* 左对齐 */
            }
            
            .section-title {
                font-size: 2rem;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    @yield('content')
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    
    <script>
        // 初始化AOS动画
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
        
        // 平滑滚动
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>
