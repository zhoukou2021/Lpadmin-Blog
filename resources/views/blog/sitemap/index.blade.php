@extends('blog.layouts.main')

@section('content')
  @php
    $seo = $seo ?? ['title' => __('blog.sitemap_title')];
  @endphp

  <div class="row">
    <div class="col-lg-12 mb-3">
      <div class="card">
        <div class="card-hd">
          <h1 style="margin: 0; font-size: 20px; font-weight: 700;">
            <i class="bi bi-diagram-3"></i> {{ __('blog.sitemap_title') }}
          </h1>
        </div>
        <div class="card-bd">
          <!-- 快速导航 -->
          <div class="sitemap-nav" style="margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid var(--primary-weak);">
            <a href="#categories" class="sitemap-nav-link" style="margin-right: 16px; color: var(--primary); text-decoration: none; font-weight: 600;">
              <i class="bi bi-folder"></i> {{ __('blog.category_list') }}
            </a>
            <a href="#tags" class="sitemap-nav-link" style="color: var(--primary); text-decoration: none; font-weight: 600;">
              <i class="bi bi-tags"></i> {{ __('blog.tag_list') }}
            </a>
            <a href="#posts" class="sitemap-nav-link" style="margin-right: 16px; color: var(--primary); text-decoration: none; font-weight: 600;">
              <i class="bi bi-file-text"></i> {{ __('blog.post_list') }}
            </a>
          </div>

          <!-- 分类列表 -->
          <section id="categories" style="margin-bottom: 32px;">
            <h2 style="font-size: 18px; font-weight: 700; margin-bottom: 16px; color: var(--ink); border-left: 4px solid var(--primary); padding-left: 12px;">
              <i class="bi bi-folder"></i> {{ __('blog.category_list') }} ({{ count($categories) }})
            </h2>
            <div class="sitemap-list" style="display: flex; flex-wrap: wrap; gap: 12px;">
              @forelse($categories as $category)
                <a href="{{ $category['url'] }}" class="sitemap-tag" style="display: inline-block; padding: 8px 16px; background: var(--primary-weak); color: var(--primary); border-radius: 8px; text-decoration: none; transition: all 0.2s; font-size: 14px;">
                  <i class="bi bi-folder"></i> {{ $category['name'] }}
                </a>
              @empty
                <div class="muted" style="width: 100%; text-align: center; padding: 20px;">{{ __('blog.no_categories') }}</div>
              @endforelse
            </div>
          </section>

          <!-- 标签列表 -->
          <section id="tags" style="margin-bottom: 32px;">
            <h2 style="font-size: 18px; font-weight: 700; margin-bottom: 16px; color: var(--ink); border-left: 4px solid var(--primary); padding-left: 12px;">
              <i class="bi bi-tags"></i> {{ __('blog.tag_list') }} ({{ count($tags) }})
            </h2>
            <div class="sitemap-list" style="display: flex; flex-wrap: wrap; gap: 8px;">
              @forelse($tags as $tag)
                <a href="{{ $tag['url'] }}" class="sitemap-tag" style="display: inline-block; padding: 6px 12px; background: var(--primary-weak); color: var(--primary); border-radius: 6px; text-decoration: none; transition: all 0.2s; font-size: 13px;">
                  <i class="bi bi-tag"></i> {{ $tag['name'] }}
                </a>
              @empty
                <div class="muted" style="width: 100%; text-align: center; padding: 20px;">{{ __('blog.no_tags') }}</div>
              @endforelse
            </div>
          </section>
          <!-- 文章列表 -->
          <section id="posts" style="margin-bottom: 32px; overflow: hidden; width: 100%;">
            <h2 style="font-size: 18px; font-weight: 700; margin-bottom: 16px; color: var(--ink); border-left: 4px solid var(--primary); padding-left: 12px;">
              <i class="bi bi-file-text"></i> {{ __('blog.post_list') }} ({{ count($posts) }})
            </h2>
            <div class="sitemap-posts-grid">
              @forelse($posts as $post)
                <div class="sitemap-post-item">
                  <a href="{{ $post['url'] }}" class="sitemap-post-link">
                    {{ $post['title'] }}
                  </a>
                </div>
              @empty
                <div class="muted" style="text-align: center; padding: 20px; grid-column: 1 / -1;">{{ __('blog.no_posts_list') }}</div>
              @endforelse
            </div>
          </section>

          <!-- 返回顶部按钮 -->
          <div style="text-align: center; margin-top: 32px; padding-top: 24px; border-top: 1px solid #e6eee6;">
            <a href="#top" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: var(--primary-weak); color: var(--primary); border-radius: 8px; text-decoration: none; font-size: 14px;">
              <i class="bi bi-arrow-up"></i> {{ __('blog.back_to_top') }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    .sitemap-nav-link:hover {
      color: var(--primary) !important;
      text-decoration: underline !important;
    }
    .sitemap-item:hover {
      background: #fafdfb;
    }
    .sitemap-item a:hover {
      color: var(--primary) !important;
    }
    .sitemap-tag:hover {
      background: #d4f0e4 !important;
      transform: translateY(-2px);
      box-shadow: 0 2px 8px rgba(44, 163, 106, 0.15);
    }
    .sitemap-item {
      padding: 12px 0;
      border-bottom: 1px dashed #e6eee6;
      display: flex;
      align-items: flex-start;
      gap: 12px;
    }
    /* 文章列表三列布局（PC端） */
    .sitemap-posts-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 16px;
      width: 100%;
      max-width: 100%;
      box-sizing: border-box;
      overflow: hidden;
    }
    .sitemap-post-item {
      padding: 12px;
      border: 1px solid #e6eee6;
      border-radius: 8px;
      background: #fff;
      transition: all 0.2s;
      min-width: 0;
      width: 100%;
      box-sizing: border-box;
      overflow: hidden;
    }
    .sitemap-post-item:hover {
      background: var(--primary-weak);
      border-color: var(--primary);
      transform: translateY(-2px);
      box-shadow: 0 2px 8px rgba(44, 163, 106, 0.15);
    }
    .sitemap-post-link {
      color: var(--ink);
      font-weight: 600;
      font-size: 14px;
      text-decoration: none;
      display: block;
      width: 100%;
      max-width: 100%;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      line-height: 1.5;
      box-sizing: border-box;
    }
    .sitemap-post-link:hover {
      color: var(--primary);
    }
    /* 平板端两列布局 */
    @media (max-width: 991.98px) {
      .sitemap-posts-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
      }
    }
    /* 移动端单列布局 */
    @media (max-width: 575.98px) {
      .sitemap-nav {
        display: flex;
        flex-direction: column;
        gap: 8px;
      }
      .sitemap-nav-link {
        margin-right: 0 !important;
      }
      .sitemap-posts-grid {
        grid-template-columns: 1fr;
        gap: 12px;
      }
    }
  </style>
@endsection

