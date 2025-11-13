<!DOCTYPE html>
<html lang="{{ $lang ?? app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ (isset($seo) && isset($seo['title']) ? $seo['title'] : __('blog.blog')) }}</title>
    <meta name="keywords" content="{{ (isset($seo) && isset($seo['keywords']) ? $seo['keywords'] : __('blog.keywords')) }}">
    @if(isset($seo) && !empty($seo['description']))
        <meta name="description" content="{{ $seo['description'] }}">
    @endif
    @if(isset($seo) && !empty($seo['canonical']))
        <link rel="canonical" href="{{ $seo['canonical'] }}"/>
    @endif
    <link rel="stylesheet" href="/static/admin/css/reset.css" />
    <!-- Bootstrap for simplified layout -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- 博客主样式 -->
    <link rel="stylesheet" href="/static/blog/css/main.css" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('head')
  </head>
  <body>
    <div class="site-wrap">
        <header class="full-bleed">
            <nav class="navbar navbar-expand-lg">
                <div class="container">
                    <a class="navbar-brand" href="/">
                        @php
                            // 获取网站名称（支持多语言）
                            if (!isset($siteName)) {
                                $currentLang = $lang ?? 'cn';
                                $siteName = \App\Helpers\ConfigHelper::getI18n('site_name', $currentLang, 'Blog');
                            }
                        @endphp
                        <span class="brand-word">{{ $siteName }}</span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarContent">
                        <ul class="navbar-nav me-auto">
                            @php($topItems = ($navs ?? collect())->where('parent_id', 0))
                            @foreach($topItems as $item)
                                @php($title = is_array($item->title)?($item->title[$lang] ?? (array_values($item->title)[0]??'')):$item->title)
                                @php($url = $item->url ?? '')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ $url ?: '#' }}">{{ $title }}</a>
                                    @php($children = ($navs ?? collect())->where('parent_id', $item->id))
                                    @if($children->count())
                                        <div class="submenu">
                                            @foreach($children as $child)
                                                @php($ct = is_array($child->title)?($child->title[$lang] ?? (array_values($child->title)[0]??'')):$child->title)
                                                @php($cu = $child->url ?? '')
                                                <a href="{{ $cu ?: '#' }}">{{ $ct }}</a>
                                            @endforeach
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        <div class="navbar-extra">
                            <form class="navbar-search" action="{{ route('site.search') }}" method="get">
                                <label for="navbarSearchInput" class="visually-hidden">{{ __('blog.search') }}</label>
                                <div class="search-input-group">
                                    <input id="navbarSearchInput" type="search" name="q" value="{{ request('q', '') }}" placeholder="{{ __('blog.search_placeholder') }}" aria-label="{{ __('blog.search') }}">
                                    <button type="submit" aria-label="{{ __('blog.search') }}">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </form>
                            <div class="lang-login">
                                <form method="get" onsubmit="return false;">
                                    @php($codes = ($langData['codes'] ?? ['cn','en','tw']))
                                    @php($labels = ($langData['labels'] ?? []))
                                    @php($defaultLabels = ['cn' => '简体中文', 'en' => 'English', 'tw' => '繁體中文'])
                                    <select id="langSwitcher" class="form-select form-select-sm">
                                        @foreach($codes as $code)
                                            <option value="{{ $code }}" {{ ($lang??'cn')===$code?'selected':'' }}>{{ $labels[$code] ?? ($defaultLabels[$code] ?? $code) }}</option>
                                        @endforeach
                                    </select>
                                </form>
                                @auth('web')
                                    <div class="user-info">
                                        <a href="{{ route('site.member.profile')}}" class="user-avatar-link">
                                            <img src="{{ auth('web')->user()->avatar_url }}" alt="{{ auth('web')->user()->nickname }}">
                                            <span>{{ auth('web')->user()->nickname }}</span>
                                        </a>
                                        <a href="javascript:;" onclick="handleLogout()" class="logout-link">{{ __('blog.logout') }}</a>
                                    </div>
                                @else
                                    <a href="javascript:;" onclick="openAuthModal('login')" class="login-link">{{ __('blog.login') }}</a>
                                    <a href="javascript:;" onclick="openAuthModal('register')" class="register-link">{{ __('blog.register') }}</a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </header>
        @yield('banner')
        <div class="main container py-3">
            @yield('content')
        </div>
        <footer class="footer full-bleed">
            <div class="footer-inner container">
            @include('blog.components.friend-links', ['friendLinks' => $friendLinks ?? null, 'lang' => $lang ?? null])
            <div class="muted footer-right">
                Power By <a href="https://gitee.com/xw54/lpadmin" target="_blank">LPadmin</a>  
                <a href="{{ route('site.sitemap') }}" target="_blank">{{ __('blog.sitemap') }}</a>
            </div>
            <div class="muted footer-right">{!! ($copyright ?? ('&copy; '.date('Y'))) !!}</div>
            </div>
        </footer>
    </div>
    
    <!-- 包含认证弹窗组件 -->
    @include('blog.components.auth-modal')
    
    <!-- 博客配置（供 JavaScript 使用） -->
    <script>
        window.BlogConfig = {
            languageSwitchUrl: '{{ route("site.language.switch") }}',
            logoutUrl: '{{ route("site.auth.logout") }}',
            logoutConfirm: '{{ __("blog.logout_confirm") ?? "确定要登出吗？" }}'
        };
    </script>
    <!-- 博客主脚本 -->
    <script src="/static/blog/js/main.js"></script>
  </body>
</html>


