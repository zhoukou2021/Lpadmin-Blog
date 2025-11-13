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
    <!-- 资料编辑表单 -->
    <div class="card">
      <div class="card-hd">{{ __('blog.edit_profile') }}</div>
      <div class="card-bd">
        <form id="profileForm" class="member-form" onsubmit="return false;">
          @csrf
          <div class="form-group">
            <label>{{ __('blog.username') }}</label>
            <input type="text" value="{{ $user->username }}" disabled>
            <div class="form-help">{{ __('blog.username_cannot_change') }}</div>
          </div>

          <div class="form-group">
            <label>{{ __('blog.nickname') }}</label>
            <input type="text" name="nickname" value="{{ $user->nickname }}">
          </div>

          <div class="form-group">
            <label>{{ __('blog.email') }}</label>
            <input type="email" name="email" value="{{ $user->email }}">
          </div>

          <div class="form-group">
            <label>{{ __('blog.phone') }}</label>
            <input type="text" name="phone" value="{{ $user->phone }}">
          </div>

          <div class="form-group">
            <label>{{ __('blog.gender') }}</label>
            <select name="gender">
              <option value="0" {{ $user->gender == 0 ? 'selected' : '' }}>{{ __('blog.gender_unknown') }}</option>
              <option value="1" {{ $user->gender == 1 ? 'selected' : '' }}>{{ __('blog.gender_male') }}</option>
              <option value="2" {{ $user->gender == 2 ? 'selected' : '' }}>{{ __('blog.gender_female') }}</option>
            </select>
          </div>

          <div class="form-group">
            <label>{{ __('blog.birthday') }}</label>
            <input type="date" name="birthday" value="{{ $user->birthday ? $user->birthday->format('Y-m-d') : '' }}">
          </div>

          <div class="form-group">
            <label>{{ __('blog.avatar') }}</label>
            <div class="avatar-upload-wrapper">
              <!-- 头像预览 -->
              <div class="avatar-preview-wrapper">
                <img id="avatarPreview" class="avatar-preview" src="{{ $user->avatar_url ?: '/static/admin/images/avatar.jpg' }}" alt="Avatar">
              </div>
              <!-- 上传控件 -->
              <div class="avatar-upload-controls">
                <input type="file" id="avatarFileInput" class="avatar-file-input" name="avatar_file" accept="image/*">
                <button type="button" id="avatarUploadBtn" class="avatar-upload-btn">
                  <i class="bi bi-image"></i> 选择图片
                </button>
                <div class="avatar-upload-help">
                  支持 JPG、PNG、GIF 格式，最大 2MB
                </div>
                <!-- 隐藏字段，用于保留原有头像URL（如果用户只修改其他字段） -->
                <input type="hidden" name="avatar" id="avatarUrlInput" value="{{ $user->avatar }}">
              </div>
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn-submit">{{ __('blog.save') }}</button>
            <a href="{{ route('site.member.index') }}" class="btn-cancel">{{ __('blog.cancel') }}</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('head')
  @include('blog.components.banner-styles', ['cssClass' => 'member-banner'])
  <link rel="stylesheet" href="/static/blog/css/member.css" />
  <script src="/static/blog/js/member-profile.js"></script>
  <script>
    window.MemberConfig = {
      updateProfileUrl: '{{ route("site.member.updateProfile") }}',
      profileUrl: '{{ route("site.member.profile") }}',
      texts: {
        saving: @json(__('blog.saving')),
        updateSuccess: @json(__('blog.update_success')),
        updateFailed: @json(__('blog.update_failed'))
      }
    };
  </script>
@endsection

