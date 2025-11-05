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
<style>
    /* 滚动容器样式 */
    .sidebar-scroll-container {
        position: relative;
        overflow: hidden;
    }
    .sidebar-scroll-wrapper {
        display: flex;
        flex-direction: row;
        overflow-x: auto;
        overflow-y: hidden;
        scroll-behavior: smooth;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE and Edge */
    }
    .sidebar-scroll-wrapper::-webkit-scrollbar {
        display: none; /* Chrome, Safari, Opera */
    }
    /* 每屏样式：占100%宽度，包含5条垂直排列的文章 */
    .sidebar-screen {
        flex: 0 0 100%;
        width: 100%;
        min-width: 100%;
        scroll-snap-align: start;
        display: flex;
        flex-direction: column;
    }
    /* 导航按钮 */
    .sidebar-scroll-nav {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #e6eee6;
    }
    .sidebar-scroll-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border: 1px solid #e6eee6;
        border-radius: 4px;
        background: #fff;
        color: var(--ink);
        cursor: pointer;
        transition: all 0.2s;
        font-size: 14px;
    }
    .sidebar-scroll-btn:hover {
        background: var(--primary-weak);
        color: var(--primary);
        border-color: var(--primary);
    }
    .sidebar-scroll-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .sidebar-scroll-btn:disabled:hover {
        background: #fff;
        color: var(--ink);
        border-color: #e6eee6;
    }
    /* 覆盖主布局中的 .post-card 样式，确保侧边栏使用块级布局 */
    .sidebar-post-item.post-card {
        display: block !important;  /* 覆盖主布局的 flex 布局 */
        flex-direction: initial !important;
        gap: 0 !important;
    }
    .sidebar-post-item {
        padding: 8px 0;
        border-bottom: 1px dashed #e6eee6;
        box-sizing: border-box;
    }
    .sidebar-screen .sidebar-post-item:last-child {
        border-bottom: none;
    }
    .sidebar-post-title {
        display: -webkit-box;        /* 使其为 flexbox（用于 line-clamp） */
        -webkit-box-orient: vertical; /* 垂直排列 */
        overflow: hidden;           /* 隐藏超出内容 */
        -webkit-line-clamp: 2;     /* 限制为两行 */
        line-clamp: 2;             /* 标准属性，提高兼容性 */
        max-height: 3em;           /* 根据行高计算最大高度 (例如，每行1.5em * 2行) */
        margin-bottom: 6px;        /* 标题和统计数据之间的间距 */
        width: 100%;               /* 确保占满宽度 */
    }
    .sidebar-post-title a {
        color: var(--ink);
        font-weight: normal;
        font-size: 14px;
        line-height: 1.5;
    }
    .sidebar-post-title a:hover {
        color: var(--primary);
    }
    .sidebar-post-meta {
        display: flex !important;    /* 强制使用 flex 布局 */
        flex-direction: row;         /* 横向排列 */
        flex-wrap: wrap;             /* 允许换行 */
        gap: 8px;                    /* 项目间距 */
        align-items: center;         /* 垂直居中 */
        font-size: 12px;
        color: #597a6a;
        margin-top: 4px;             /* 与标题的间距 */
    }
    .sidebar-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        white-space: nowrap;
        flex-shrink: 0;              /* 防止收缩 */
    }
    .sidebar-meta-item i {
        font-size: 12px;
    }
    .sidebar-meta-item.post-like-item:hover,
    .sidebar-meta-item.post-favorite-item:hover {
        color: var(--primary);
    }
</style>
<script>
(function() {
    // 初始化所有滚动容器
    document.addEventListener('DOMContentLoaded', function() {
        const containers = document.querySelectorAll('.sidebar-scroll-container');
        
        containers.forEach(function(container) {
            const wrapper = container.querySelector('.sidebar-scroll-wrapper');
            const prevBtn = container.querySelector('.sidebar-scroll-prev');
            const nextBtn = container.querySelector('.sidebar-scroll-next');
            
            if (!wrapper || !prevBtn || !nextBtn) return;
            
            // 计算每屏宽度（100%）
            const screenWidth = wrapper.offsetWidth;
            
            // 更新按钮状态
            function updateButtons() {
                const scrollLeft = wrapper.scrollLeft;
                const maxScroll = wrapper.scrollWidth - wrapper.offsetWidth;
                
                prevBtn.disabled = scrollLeft <= 0;
                nextBtn.disabled = scrollLeft >= maxScroll - 1; // 减1避免浮点数误差
            }
            
            // 上一屏
            prevBtn.addEventListener('click', function() {
                wrapper.scrollBy({
                    left: -screenWidth,
                    behavior: 'smooth'
                });
            });
            
            // 下一屏
            nextBtn.addEventListener('click', function() {
                wrapper.scrollBy({
                    left: screenWidth,
                    behavior: 'smooth'
                });
            });
            
            // 监听滚动事件
            wrapper.addEventListener('scroll', updateButtons);
            
            // 初始化按钮状态
            updateButtons();
            
            // 监听窗口大小变化
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    updateButtons();
                }, 100);
            });
        });
    });
})();
</script>