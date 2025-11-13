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

        <div class="member-quick-actions">
          <h3>{{ __('blog.quick_actions') }}</h3>
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
  <link rel="stylesheet" href="/static/blog/css/member.css" />
@endsection

