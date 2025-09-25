@extends('home.layout')

@section('title', 'LPadmin - 现代化后台管理系统')
@section('description', 'LPadmin是基于Laravel 10+和PearAdminLayui构建的现代化后台管理系统，提供完整的RBAC权限管理、用户管理、系统配置等核心功能')

@section('content')
<!-- Quick Navigation -->
<nav class="quick-nav" id="quickNav">
    <div class="container">
        <div class="quick-nav-content">
            <div class="quick-nav-brand">
                <img src="{{ asset('logo.png') }}" alt="LPadmin Logo" class="nav-logo">
                <span>LPadmin</span>
            </div>
            <ul class="quick-nav-menu">
                <li><a href="#home" class="nav-link active">首页</a></li>
                <li><a href="#core-features" class="nav-link">核心功能</a></li>
                <li><a href="#features" class="nav-link">系统优势</a></li>
                <li><a href="#tech-info" class="nav-link">技术栈</a></li>
                <li><a href="#documentation" class="nav-link">开发文档</a></li>
            </ul>
            <!-- 移动端菜单按钮 -->
            <button class="mobile-menu-toggle d-md-none" id="mobileMenuToggle">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>
        </div>
        <!-- 移动端菜单 -->
        <div class="mobile-menu" id="mobileMenu">
            <div class="mobile-menu-content">
                <a href="#home" class="mobile-nav-link active">
                    <i class="fas fa-home me-2"></i>首页
                </a>
                <a href="#core-features" class="mobile-nav-link">
                    <i class="fas fa-cogs me-2"></i>核心功能
                </a>
                <a href="#features" class="mobile-nav-link">
                    <i class="fas fa-star me-2"></i>系统优势
                </a>
                <a href="#tech-info" class="mobile-nav-link">
                    <i class="fas fa-layer-group me-2"></i>技术栈
                </a>
                <a href="#documentation" class="mobile-nav-link">
                    <i class="fas fa-book me-2"></i>开发文档
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section id="home" class="hero-section">
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content" data-aos="fade-right">
                <h1 class="hero-title">{{ $systemInfo['name'] }}</h1>
                <p class="hero-subtitle">{{ $systemInfo['description'] }}</p>
                <div class="hero-buttons" style="margin-bottom:10px;">
                    <a href="{{ lpadmin_url_prefix() }}/login" class="btn-hero btn-primary-hero">
                        <i class="fas fa-sign-in-alt me-2"></i>立即体验
                    </a>
                    <a href="#features" class="btn-hero btn-outline-hero">
                        <i class="fas fa-info-circle me-2"></i>了解更多
                    </a>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="text-center">
                    <div class="admin-screenshot">
                        <img src="{{ asset('static/images/admin.png') }}" alt="LPadmin 后台截图" class="img-fluid screenshot-img">
                        <div class="screenshot-overlay">
                            <div class="play-button">
                                <i class="fas fa-play"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Core Features Section -->
<section id="core-features" class="section">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">后台核心功能</h2>
        <div class="row">
            @foreach($coreFeatures as $feature)
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="core-feature-card">
                    <div class="feature-header">
                        <div class="feature-icon-large bg-{{ $feature['color'] }}">
                            <i class="{{ $feature['icon'] }}"></i>
                        </div>
                        <h4 class="feature-title">{{ $feature['title'] }}</h4>
                    </div>
                    <p class="feature-description">{{ $feature['description'] }}</p>
                    <div class="feature-list">
                        @foreach($feature['features'] as $item)
                        <div class="feature-item">
                            <i class="fas fa-check text-{{ $feature['color'] }} me-2"></i>
                            <span>{{ $item }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- System Advantages Section -->
<section id="features" class="section" style="background: var(--light-bg);">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">系统优势</h2>
        <div class="row">
            @foreach($systemInfo['features'] as $title => $description)
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="feature-card">
                    <div class="feature-icon">
                        @switch($title)
                            @case('现代化架构')
                                <i class="fas fa-rocket"></i>
                                @break
                            @case('美观界面')
                                <i class="fas fa-palette"></i>
                                @break
                            @case('权限系统')
                                <i class="fas fa-shield-alt"></i>
                                @break
                            @case('响应式设计')
                                <i class="fas fa-mobile-alt"></i>
                                @break
                            @case('高度可配置')
                                <i class="fas fa-cogs"></i>
                                @break
                            @case('安全可靠')
                                <i class="fas fa-lock"></i>
                                @break
                            @case('易于扩展')
                                <i class="fas fa-puzzle-piece"></i>
                                @break
                            @case('开源免费')
                                <i class="fas fa-heart"></i>
                                @break
                            @default
                                <i class="fas fa-star"></i>
                        @endswitch
                    </div>
                    <h4 class="mb-3">{{ $title }}</h4>
                    <p class="text-muted">{{ $description }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Tech Stack & System Info Section -->
<section id="tech-info" class="tech-system-info section" style="background: white;">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">技术栈 & 系统信息</h2>
        <div class="row">
            <!-- 技术栈 -->
            <div class="col-lg-6 mb-4" data-aos="fade-right">
                <div class="info-card">
                    <h4 class="info-card-title">
                        <i class="fas fa-layer-group me-2"></i>技术栈
                    </h4>
                    <div class="row">
                        @foreach($systemInfo['tech_stack'] as $tech => $version)
                        <div class="col-md-6 mb-3">
                            <div class="tech-item-compact" style="text-align: left;">
                                <strong>{{ $tech }}<small style="font-size: 12px;font-weight: lighter;padding-left: 10px;">{{ $version }}</small></strong>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- 系统信息 -->
            <div class="col-lg-6 mb-4" data-aos="fade-left">
                <div class="info-card">
                    <h4 class="info-card-title">
                        <i class="fas fa-info-circle me-2"></i>系统信息
                    </h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>系统名称：</strong>
                            <div class="text-muted">{{ $systemInfo['name'] }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>系统版本：</strong>
                            <div class="text-muted">{{ $systemInfo['version'] }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Laravel版本：</strong>
                            <div class="text-muted">{{ $systemInfo['laravel_version'] }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>PHP版本：</strong>
                            <div class="text-muted">{{ $systemInfo['php_version'] }}</div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ lpadmin_url_prefix() }}/login" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>进入管理后台
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Documentation Section -->
<section id="documentation" class="documentation section" style="background: var(--light-bg);">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">开发文档</h2>
        <p class="text-center text-muted mb-5" data-aos="fade-up" data-aos-delay="100">
            完整的开发文档，帮助您快速上手和深入了解系统
        </p>
        <div class="row">
            @foreach($documentationList as $doc)
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="doc-card-home">
                    <div class="doc-card-content">
                        <div class="doc-icon-wrapper">
                            <i class="{{ $doc['icon'] }} doc-icon-home"></i>
                        </div>
                        <div class="doc-info">
                            <h5 class="doc-title-home">{{ $doc['title'] }}</h5>
                            <p class="doc-description-home">{{ $doc['description'] }}</p>
                        </div>
                    </div>
                    <div class="doc-footer">
                        <span class="doc-type">{{ $doc['type'] }}</span>
                        <a href="javascript:void(0)" onclick="openDocumentation('{{ $doc['file'] }}')" class="btn-doc-icon">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>



<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h5>关于 LPadmin</h5>
                <p>LPadmin是一个现代化的后台管理系统，基于Laravel10+PearAdminLayui框架开发，提供完整的权限管理和系统配置功能。</p>
            </div>
            <div class="col-lg-4 mb-4">
                <h5>快速链接</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ lpadmin_url_prefix() }}/login"><i class="fas fa-sign-in-alt me-2"></i>管理后台</a></li>
                    <li><a href="#features"><i class="fas fa-star me-2"></i>功能特性</a></li>
                    <li><a href="https://laravel.com" target="_blank"><i class="fas fa-external-link-alt me-2"></i>Laravel官网</a></li>
                    <li><a href="https://gitee.com/xw54/lpadmin" target="_blank"><i class="fab fa-github me-2"></i>Gitee</a></li>
                </ul>
            </div>
            <div class="col-lg-4 mb-4">
                <h5>联系方式</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-envelope me-2"></i>{{ $systemInfo['contact']['email'] }}</li>
                    <li><i class="fas fa-phone me-2"></i>{{ $systemInfo['contact']['phone'] }}</li>
                    <li><i class="fas fa-map-marker-alt me-2"></i>{{ $systemInfo['contact']['address'] }}</li>
                    <li class="mt-3">
                        <a href="{{ $systemInfo['social']['github'] }}" class="me-3" target="_blank"><i class="fab fa-github fa-lg"></i></a>
                        <a title="{{ $systemInfo['social']['qq'] }}" class="me-3" style="cursor: pointer;"><i class="fab fa-qq fa-lg"></i></a>
                        <a title="{{ $systemInfo['social']['wechat'] }}" class="me-3" style="cursor: pointer;"><i class="fab fa-weixin fa-lg"></i></a>
                    </li>
                </ul>
            </div>
        </div>
        <hr class="my-4" style="border-color: rgba(255,255,255,0.2);">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0">&copy; {{ date('Y') }} {{ $systemInfo['copyright'] }}. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0">Powered by LPadmin Team</p>
            </div>
        </div>
    </div>
</footer>

@push('scripts')
<script>
// 打开文档
function openDocumentation(docPath) {
    // 构建文档URL
    const docUrl = '{{ lpadmin_url_prefix() }}/doc/view?file=' + encodeURIComponent(docPath);

    // 在新窗口中打开文档
    window.open(docUrl, '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');
}

// 快捷导航功能
document.addEventListener('DOMContentLoaded', function() {
    const quickNav = document.getElementById('quickNav');
    const navLinks = document.querySelectorAll('.quick-nav-menu .nav-link');
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');

    // 移动端菜单切换
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            mobileMenu.classList.toggle('active');
        });

        // 点击移动端导航链接时关闭菜单
        mobileNavLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenuToggle.classList.remove('active');
                mobileMenu.classList.remove('active');
            });
        });

        // 点击页面其他地方关闭移动端菜单
        document.addEventListener('click', function(e) {
            if (!mobileMenuToggle.contains(e.target) && !mobileMenu.contains(e.target)) {
                mobileMenuToggle.classList.remove('active');
                mobileMenu.classList.remove('active');
            }
        });
    }

    // 滚动时添加背景效果
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            quickNav.classList.add('scrolled');
        } else {
            quickNav.classList.remove('scrolled');
        }

        // 更新活跃导航项
        updateActiveNavLink();
    });

    // 平滑滚动到目标section（桌面端）
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            scrollToSection(this.getAttribute('href'));
        });
    });

    // 平滑滚动到目标section（移动端）
    mobileNavLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            scrollToSection(this.getAttribute('href'));
        });
    });

    // 滚动到指定section
    function scrollToSection(targetId) {
        const targetSection = document.querySelector(targetId);
        if (targetSection) {
            const offsetTop = targetSection.offsetTop - 80; // 考虑导航栏高度
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
    }

    // 更新活跃导航项
    function updateActiveNavLink() {
        const sections = document.querySelectorAll('section[id]');
        const scrollPos = window.scrollY + 100;

        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute('id');

            if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                // 更新桌面端导航
                navLinks.forEach(link => link.classList.remove('active'));
                const activeLink = document.querySelector(`.nav-link[href="#${sectionId}"]`);
                if (activeLink) {
                    activeLink.classList.add('active');
                }

                // 更新移动端导航
                mobileNavLinks.forEach(link => link.classList.remove('active'));
                const activeMobileLink = document.querySelector(`.mobile-nav-link[href="#${sectionId}"]`);
                if (activeMobileLink) {
                    activeMobileLink.classList.add('active');
                }
            }
        });
    }

    // 添加文档卡片点击效果
    const docCards = document.querySelectorAll('.doc-card-home');

    docCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // 如果点击的不是按钮，则触发按钮点击
            if (!e.target.closest('.btn-doc-icon')) {
                const btn = this.querySelector('.btn-doc-icon');
                if (btn) {
                    btn.click();
                }
            }
        });

        // 添加鼠标悬停效果
        card.addEventListener('mouseenter', function() {
            this.style.cursor = 'pointer';
        });
    });
});
</script>
@endpush

@endsection
