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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        :root{ --primary:#2ca36a; --primary-weak:#e6f7f0; --ink:#0f2b1e; }
        body{font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:var(--ink);margin:0;background:#fafdfb;overflow-x:hidden}
        a{text-decoration:none;color:var(--ink)}
        /* 使用 Bootstrap 的 .container 控制中间主体宽度，勿覆盖其样式 */
        header{padding:0;border-bottom:1px solid #e6eee6;background:linear-gradient(0deg, #f7fff9, #ffffff)}
        .navbar{min-height:56px}
        .navbar-brand{font-weight:800;padding:0}
        .navbar-brand a{text-decoration:none;display:inline-flex;flex-wrap:wrap}
        .brand-word{display:inline-block;margin:0 -0.5px}
        .brand-word:nth-child(odd){color:var(--primary)}
        .brand-word:nth-child(even){color:#4a9b6d}
        .navbar-nav{flex-direction:row;gap:6px}
        .navbar-nav .nav-link{padding:10px 12px;border-radius:8px;transition:background .2s;color:var(--ink)}
        .navbar-nav .nav-link:hover{background:var(--primary-weak)}
        .navbar-nav .nav-item{position:relative}
        .navbar-nav .submenu{display:none;position:absolute;top:100%;left:0;background:#fff;border:1px solid #e6eee6;border-radius:10px;min-width:180px;box-shadow:0 8px 28px rgba(18,38,32,.08);z-index:1000;padding:4px 0;margin-top:0}
        /* 添加一个连接区域，确保鼠标移动时不会离开触发区域 */
        .navbar-nav .nav-item:hover>.submenu::before{content:'';position:absolute;top:-8px;left:0;right:0;height:8px;background:transparent}
        .navbar-nav .nav-item:hover>.submenu,
        .navbar-nav .submenu:hover{display:block}
        .navbar-nav .submenu a{display:block;padding:8px 16px;color:var(--ink);border-radius:4px;margin:2px 8px}
        .navbar-nav .submenu a:hover{background:var(--primary-weak)}
        .navbar-toggler{border:1px solid #e6eee6;padding:4px 8px;background:#fff}
        .navbar-toggler-icon{display:inline-block;width:1.5em;height:1.5em;vertical-align:middle;background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2833, 37, 41, 0.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");background-repeat:no-repeat;background-position:center;background-size:100%}
        .lang-login{display:flex;gap:8px;align-items:center;margin-left:auto}
        .lang-login select{
            padding:6px 12px;
            border:1px solid #e6eee6;
            border-radius:6px;
            background:#fff;
            font-size:14px;
            color:var(--ink);
            cursor:pointer;
            transition:all .2s;
            min-width:60px;
            appearance:none;
            background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat:no-repeat;
            background-position:right 8px center;
            background-size:12px;
            padding-right:32px;
        }
        .lang-login select:hover{
            border-color:var(--primary);
            background-color:var(--primary-weak);
        }
        .lang-login select:focus{
            outline:none;
            border-color:var(--primary);
            box-shadow:0 0 0 3px rgba(44, 163, 106, .1);
        }
        .lang-login a:hover{background:var(--primary-weak)}
        .user-info{display:flex;align-items:center;gap:8px}
        .user-avatar-link:hover{background:var(--primary-weak)}
        @media (max-width: 991.98px){
          .navbar-nav{flex-direction:column;gap:0;padding:8px 0}
          .navbar-nav .nav-link{padding:8px 16px;border-radius:0}
          .navbar-nav .nav-item:hover>.submenu{display:none}
          .navbar-nav .submenu{position:static;display:block;box-shadow:none;border:none;border-left:2px solid var(--primary-weak);margin-left:16px}
          .lang-login{margin-left:0;margin-top:8px;padding:8px 16px;border-top:1px solid #e6eee6}
        }


        /* 主体布局由Bootstrap grid系统控制 */
        @media (min-width: 992px){
          .layout-main > div:first-child{max-width:66.666667%}
        }
        .card{border:1px solid #e6eee6;border-radius:14px;background:#fff;box-shadow:0 6px 24px rgba(18,38,32,.04)}
        .card .card-hd{padding:12px 14px;font-weight:700}
        .card .card-bd{padding:12px}
        .tabs{display:flex;border-bottom:1px solid #e6eee6;flex-wrap:wrap}
        .tabs a{padding:12px 14px;display:block;border-radius:8px 8px 0 0;white-space:nowrap}
        .tabs a.active{background:var(--primary-weak);color:var(--primary);box-shadow:inset 0 -2px 0 var(--primary)}
        @media (max-width: 575.98px){
          .tabs a{padding:10px 12px;font-size:14px}
        }
        .post-card{padding:12px 0;border-bottom:1px dashed #e6eee6;display:flex;gap:12px}
        .post-card:last-child{border-bottom:none}
        .post-cover{flex-shrink:0;width:120px;height:100%;border-radius:8px;overflow:hidden;background:#f0f0f0}
        .post-cover img{width:100%;height:100%;object-fit:cover}
        .post-content{flex:1;min-width:0}
        .post-title{font-size:16px;margin:0 0 8px;line-height:1.4}
        .post-title a{color:var(--ink);font-weight:600}
        .post-title a:hover{color:var(--primary)}
        .post-summary{font-size:13px;color:#666;margin:6px 0;line-height:1.6;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
        .post-meta{display:flex;flex-wrap:wrap;gap:12px;align-items:center;margin-top:8px;font-size:12px}
        .post-meta-item{color:#597a6a;display:flex;align-items:center;gap:4px}
        .post-meta-item i{font-size:13px}
        .post-tags{display:flex;flex-wrap:wrap;gap:4px;margin-top:6px}
        .post-tag{display:inline-block;padding:2px 8px;background:var(--primary-weak);color:var(--primary);border-radius:4px;font-size:11px;text-decoration:none;transition:background .2s}
        .post-tag:hover{background:#d4f0e4;color:var(--primary)}
        .post-category{display:inline-flex;align-items:center;gap:4px;color:var(--primary);font-size:12px;text-decoration:none;transition:color .2s}
        .post-category i{font-size:13px}
        .post-category:hover{color:#1e8050}
        @media (max-width: 575.98px){
          .post-card{flex-direction:column}
          .post-cover{width:100%;height:180px}
          .footer-right{float: none;}
        }
        .muted{
            margin: 0 6px 6px 0;
            display: inline-block;
            color: var(--primary);
            text-decoration: none;
            transition: color .2s;
        }
        .footer{padding:20px 0;border-top:1px solid #e6eee6;margin-top:20px;background:linear-gradient(180deg, #ffffff, #f7fff9)}
        .footer-inner{margin:0 auto;padding:0 16px}
        .friend-links{
            border-bottom: 1px dashed var(--primary);
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .friend-links a{margin-right:10px;color:var(--primary)}
        .footer-right{margin-top:8px;float: right;font-size: 12px;}
        /* 进入/切换动效 */
        .fade-in{animation:fadeIn .4s ease both}
        @keyframes fadeIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}
    </style>
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
                                <div class="user-info" style="display:flex;align-items:center;gap:8px;">
                                    <a href="{{ route('site.member.profile')}} " class="user-avatar-link" style="display:flex;align-items:center;gap:6px;text-decoration:none;color:var(--ink);padding:4px 8px;border-radius:6px;transition:background .2s;">
                                        <img src="{{ auth('web')->user()->avatar_url }}" alt="{{ auth('web')->user()->nickname }}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;">
                                        <span style="font-size:14px;">{{ auth('web')->user()->nickname }}</span>
                                    </a>
                                    <a href="javascript:;" onclick="handleLogout()" style="font-size:14px;color:#999;text-decoration:none;padding:4px 8px;border-radius:6px;transition:background .2s;">{{ __('blog.logout') }}</a>
                                </div>
                            @else
                                <a href="javascript:;" onclick="openAuthModal('login')" style="font-size:14px;color:var(--ink);text-decoration:none;padding:4px 8px;border-radius:6px;transition:background .2s;">{{ __('blog.login') }}</a>
                                <a href="javascript:;" onclick="openAuthModal('register')" style="font-size:14px;color:var(--primary);text-decoration:none;padding:4px 8px;border-radius:6px;transition:background .2s;font-weight:600;">{{ __('blog.register') }}</a>
                            @endauth
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
    
    <script>
        (function(){
            var s=document.getElementById('langSwitcher'); if(!s) return;
            s.addEventListener('change', function(){
                var to=this.value;
                var currentPath = location.pathname;
                
                // 使用AJAX或表单提交切换语言
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("site.language.switch") }}';
                
                // 添加CSRF token
                var csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.content || '';
                form.appendChild(csrfInput);
                
                // 添加语言参数
                var langInput = document.createElement('input');
                langInput.type = 'hidden';
                langInput.name = 'lang';
                langInput.value = to;
                form.appendChild(langInput);
                
                // 添加重定向路径
                var redirectInput = document.createElement('input');
                redirectInput.type = 'hidden';
                redirectInput.name = 'redirect';
                redirectInput.value = currentPath;
                form.appendChild(redirectInput);
                
                document.body.appendChild(form);
                form.submit();
            });
        })();
        
        // 登出函数
        function handleLogout() {
            if (!confirm('{{ __("blog.logout_confirm") ?? "确定要登出吗？" }}')) {
                return;
            }
            
            var token = document.querySelector('meta[name="csrf-token"]')?.content || '';
            fetch('{{ route("site.auth.logout") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.code === 0) {
                    location.reload();
                } else {
                    alert(data.message || '登出失败');
                }
            })
            .catch(function(error) {
                console.error('Logout error:', error);
                location.reload();
            });
        }
    </script>
  </body>
</html>


