@extends('blog.layouts.main')

@section('banner')
  @include('blog.components.banner', ['banner' => $memberBanner ?? null, 'cssClass' => 'member-banner'])
@endsection

@section('content')
<div class="layout-main row">
  <div class="col-lg-3">
    <!-- 会员中心左侧菜单 -->
    <div class="card" style="margin-bottom:12px">
      <div class="card-hd">{{ __('blog.member_center') }}</div>
      <div class="card-bd" style="padding:0;">
        <div class="member-menu">
          <a href="{{ route('site.member.index') }}" class="member-menu-item {{ request()->routeIs('site.member.index') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i>
            <span>{{ __('blog.overview') }}</span>
          </a>
          <a href="{{ route('site.member.profile') }}" class="member-menu-item {{ request()->routeIs('site.member.profile') ? 'active' : '' }}">
            <i class="bi bi-person"></i>
            <span>{{ __('blog.edit_profile') }}</span>
          </a>
          <a href="{{ route('site.member.favorites') }}" class="member-menu-item {{ request()->routeIs('site.member.favorites') ? 'active' : '' }}">
            <i class="bi bi-star"></i>
            <span>{{ __('blog.my_favorites') }}</span>
          </a>
          <a href="{{ route('site.member.likes') }}" class="member-menu-item {{ request()->routeIs('site.member.likes') ? 'active' : '' }}">
            <i class="bi bi-heart"></i>
            <span>{{ __('blog.my_likes') }}</span>
          </a>
          <a href="{{ route('site.member.comments') }}" class="member-menu-item {{ request()->routeIs('site.member.comments') ? 'active' : '' }}">
            <i class="bi bi-chat-dots"></i>
            <span>{{ __('blog.my_comments') }}</span>
          </a>
        </div>
      </div>
    </div>

    <!-- 侧边栏 -->
    @include('blog.partials.sidebar', ['categories' => $categories ?? [], 'tags' => $tags ?? []])
  </div>

  <div class="col-lg-9">
    <!-- 会员中心主页内容 -->
    <div class="card">
      <div class="card-hd">{{ __('blog.welcome') }}, {{ $user->nickname }}</div>
      <div class="card-bd">
        <div class="member-stats">
          <div class="stat-item">
            <div class="stat-icon" style="background:var(--primary-weak);color:var(--primary);">
              <i class="bi bi-star-fill"></i>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ $favoritesCount ?? 0 }}</div>
              <div class="stat-label">{{ __('blog.favorites') }}</div>
            </div>
            <a href="{{ route('site.member.favorites') }}" class="stat-link">{{ __('blog.view_all') }} <i class="bi bi-arrow-right"></i></a>
          </div>
          <div class="stat-item">
            <div class="stat-icon" style="background:#fff0f0;color:#ff6b6b;">
              <i class="bi bi-heart-fill"></i>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ $likesCount ?? 0 }}</div>
              <div class="stat-label">{{ __('blog.likes') }}</div>
            </div>
            <a href="{{ route('site.member.likes') }}" class="stat-link">{{ __('blog.view_all') }} <i class="bi bi-arrow-right"></i></a>
          </div>
          <div class="stat-item">
            <div class="stat-icon" style="background:#f0f7ff;color:#4dabf7;">
              <i class="bi bi-chat-dots-fill"></i>
            </div>
            <div class="stat-content">
              <div class="stat-value">{{ $commentsCount ?? 0 }}</div>
              <div class="stat-label">{{ __('blog.comments') }}</div>
            </div>
            <a href="{{ route('site.member.comments') }}" class="stat-link">{{ __('blog.view_all') }} <i class="bi bi-arrow-right"></i></a>
          </div>
        </div>

        <div class="member-quick-actions" style="margin-top:24px;padding-top:24px;border-top:1px solid #e6eee6;">
          <h3 style="margin:0 0 16px;font-size:16px;font-weight:600;">{{ __('blog.quick_actions') }}</h3>
          <div class="quick-actions-grid">
            <a href="{{ route('site.member.profile') }}" class="quick-action-item">
              <i class="bi bi-person-circle"></i>
              <span>{{ __('blog.edit_profile') }}</span>
            </a>
            <a href="{{ route('site.member.favorites') }}" class="quick-action-item">
              <i class="bi bi-star"></i>
              <span>{{ __('blog.my_favorites') }}</span>
            </a>
            <a href="{{ route('site.member.likes') }}" class="quick-action-item">
              <i class="bi bi-heart"></i>
              <span>{{ __('blog.my_likes') }}</span>
            </a>
            <a href="{{ route('site.member.comments') }}" class="quick-action-item">
              <i class="bi bi-chat-dots"></i>
              <span>{{ __('blog.my_comments') }}</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('head')
  @include('blog.components.banner-styles', ['cssClass' => 'member-banner'])
<style>
  /* 会员中心菜单样式 */
  .member-menu {
    display: flex;
    flex-direction: column;
  }
  
  .member-menu-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    color: var(--ink);
    text-decoration: none;
    border-bottom: 1px solid #f0f0f0;
    transition: all .2s;
  }
  
  .member-menu-item:last-child {
    border-bottom: none;
  }
  
  .member-menu-item:hover {
    background: var(--primary-weak);
    color: var(--primary);
  }
  
  .member-menu-item.active {
    background: var(--primary-weak);
    color: var(--primary);
    font-weight: 600;
  }
  
  .member-menu-item i {
    font-size: 16px;
    width: 20px;
    text-align: center;
  }

  /* 统计卡片样式 */
  .member-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
  }
  
  .stat-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: #fafafa;
    border-radius: 10px;
    border: 1px solid #e6eee6;
    transition: all .2s;
    position: relative;
  }
  
  .stat-item:hover {
    border-color: var(--primary);
    box-shadow: 0 4px 12px rgba(44, 163, 106, .1);
  }
  
  .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
  }
  
  .stat-content {
    flex: 1;
    min-width: 0;
  }
  
  .stat-value {
    font-size: 24px;
    font-weight: 700;
    color: var(--ink);
    line-height: 1.2;
  }
  
  .stat-label {
    font-size: 13px;
    color: #666;
    margin-top: 4px;
  }
  
  .stat-link {
    font-size: 12px;
    color: var(--primary);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
    transition: color .2s;
  }
  
  .stat-link:hover {
    color: var(--primary);
    text-decoration: underline;
  }

  /* 快捷操作样式 */
  .quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 12px;
  }
  
  .quick-action-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 16px;
    background: #fafafa;
    border: 1px solid #e6eee6;
    border-radius: 10px;
    text-decoration: none;
    color: var(--ink);
    transition: all .2s;
  }
  
  .quick-action-item:hover {
    background: var(--primary-weak);
    border-color: var(--primary);
    color: var(--primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(44, 163, 106, .1);
  }
  
  .quick-action-item i {
    font-size: 32px;
    color: var(--primary);
  }
  
  .quick-action-item:hover i {
    transform: scale(1.1);
  }
  
  .quick-action-item span {
    font-size: 14px;
    font-weight: 500;
  }

  @media (max-width: 991.98px) {
    .member-stats {
      grid-template-columns: 1fr;
    }
    
    .quick-actions-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }
  
  @media (max-width: 575.98px) {
    .stat-item {
      flex-direction: column;
      text-align: center;
    }
    
    .stat-link {
      margin-top: 8px;
    }
    
    .quick-actions-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
@endsection

