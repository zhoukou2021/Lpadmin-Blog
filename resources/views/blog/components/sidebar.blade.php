<div class="col-lg-3">
    <div class="card" style="margin-bottom:12px">
    <div class="card-hd">{{ __('blog.categories') }}</div>
    <div class="card-bd">
        @foreach(($categories ?? []) as $c)
        <a href="{{ blog_url($c['slug']) }}" class="muted" >{{ $c['name'] }}</a>
        @endforeach
    </div>
    </div>
    <div class="card" style="margin-bottom:12px">
    <div class="card-hd">{{ __('blog.tag_cloud') }}</div>
    <div class="card-bd">
        @foreach(($tags ?? []) as $t)
        <a href="{{ blog_url($t['slug']) }}" class="muted" >#{{ $t['name'] }}</a>
        @endforeach
    </div>
    </div>
    <div class="card" style="margin-bottom:12px">
    <div class="card-hd">{{ __('blog.hot_comments') }}</div>
    <div class="card-bd sidebar-scroll-container">
        <div class="sidebar-scroll-wrapper">
            @php
                $commentsChunks = collect($hotComments ?? [])->chunk(5);
            @endphp
            @foreach($commentsChunks as $chunk)
            <div class="sidebar-screen">
                @foreach($chunk as $p)
                <div class="post-card sidebar-post-item">
                    <div class="post-title sidebar-post-title"><a href="{{ blog_url($p['slug']) }}">{{ $p['title'] }}</a></div>
                    <div class="sidebar-post-meta">
                        <span class="sidebar-meta-item"><i class="bi bi-eye"></i> {{ $p['view_count'] ?? 0 }}</span>
                        <span class="sidebar-meta-item"><i class="bi bi-chat-dots"></i> {{ $p['comments_count'] ?? 0 }}</span>
                        <span class="sidebar-meta-item post-like-item" data-post-id="{{ $p['id'] }}" style="cursor:pointer;user-select:none;"><i class="bi bi-hand-thumbs-up"></i> <span class="likes-count">{{ $p['likes_count'] ?? 0 }}</span></span>
                        <span class="sidebar-meta-item post-favorite-item" data-post-id="{{ $p['id'] }}" style="cursor:pointer;user-select:none;"><i class="bi bi-star"></i> <span class="favorites-count">{{ $p['favorites_count'] ?? 0 }}</span></span>
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
        <div class="sidebar-scroll-nav">
            <button class="sidebar-scroll-btn sidebar-scroll-prev" aria-label="上一屏"><i class="bi bi-chevron-left"></i></button>
            <button class="sidebar-scroll-btn sidebar-scroll-next" aria-label="下一屏"><i class="bi bi-chevron-right"></i></button>
        </div>
    </div>
    </div>
    <div class="card" style="margin-bottom:12px">
    <div class="card-hd">{{ __('blog.hot_views') }}</div>
    <div class="card-bd sidebar-scroll-container">
        <div class="sidebar-scroll-wrapper">
            @php
                $viewsChunks = collect($hotViews ?? [])->chunk(5);
            @endphp
            @foreach($viewsChunks as $chunk)
            <div class="sidebar-screen">
                @foreach($chunk as $p)
                <div class="post-card sidebar-post-item">
                    <div class="post-title sidebar-post-title"><a href="{{ blog_url($p['slug']) }}">{{ $p['title'] }}</a></div>
                    <div class="sidebar-post-meta">
                        <span class="sidebar-meta-item"><i class="bi bi-eye"></i> {{ $p['view_count'] ?? 0 }}</span>
                        <span class="sidebar-meta-item"><i class="bi bi-chat-dots"></i> {{ $p['comments_count'] ?? 0 }}</span>
                        <span class="sidebar-meta-item post-like-item" data-post-id="{{ $p['id'] }}" style="cursor:pointer;user-select:none;"><i class="bi bi-hand-thumbs-up"></i> <span class="likes-count">{{ $p['likes_count'] ?? 0 }}</span></span>
                        <span class="sidebar-meta-item post-favorite-item" data-post-id="{{ $p['id'] }}" style="cursor:pointer;user-select:none;"><i class="bi bi-star"></i> <span class="favorites-count">{{ $p['favorites_count'] ?? 0 }}</span></span>
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
        <div class="sidebar-scroll-nav">
            <button class="sidebar-scroll-btn sidebar-scroll-prev" aria-label="上一屏"><i class="bi bi-chevron-left"></i></button>
            <button class="sidebar-scroll-btn sidebar-scroll-next" aria-label="下一屏"><i class="bi bi-chevron-right"></i></button>
        </div>
    </div>
    </div>
    <div class="card">
    <div class="card-hd">{{ __('blog.hot_favorites') }}</div>
    <div class="card-bd sidebar-scroll-container">
        <div class="sidebar-scroll-wrapper">
            @php
                $favoritesChunks = collect($hotFavorites ?? [])->chunk(5);
            @endphp
            @foreach($favoritesChunks as $chunk)
            <div class="sidebar-screen">
                @foreach($chunk as $p)
                <div class="post-card sidebar-post-item">
                    <div class="post-title sidebar-post-title"><a href="{{ blog_url($p['slug']) }}">{{ $p['title'] }}</a></div>
                    <div class="sidebar-post-meta">
                        <span class="sidebar-meta-item"><i class="bi bi-eye"></i> {{ $p['view_count'] ?? 0 }}</span>
                        <span class="sidebar-meta-item"><i class="bi bi-chat-dots"></i> {{ $p['comments_count'] ?? 0 }}</span>
                        <span class="sidebar-meta-item post-like-item" data-post-id="{{ $p['id'] }}" style="cursor:pointer;user-select:none;"><i class="bi bi-hand-thumbs-up"></i> <span class="likes-count">{{ $p['likes_count'] ?? 0 }}</span></span>
                        <span class="sidebar-meta-item post-favorite-item" data-post-id="{{ $p['id'] }}" style="cursor:pointer;user-select:none;"><i class="bi bi-star"></i> <span class="favorites-count">{{ $p['favorites_count'] ?? 0 }}</span></span>
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
        <div class="sidebar-scroll-nav">
            <button class="sidebar-scroll-btn sidebar-scroll-prev" aria-label="上一屏"><i class="bi bi-chevron-left"></i></button>
            <button class="sidebar-scroll-btn sidebar-scroll-next" aria-label="下一屏"><i class="bi bi-chevron-right"></i></button>
        </div>
    </div>
    </div>
</div>
<link rel="stylesheet" href="/static/blog/css/sidebar.css" />
<script src="/static/blog/js/sidebar.js"></script>