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
        <form id="profileForm" onsubmit="return false;" style="max-width:600px;">
          @csrf
          <div class="form-group" style="margin-bottom:20px;">
            <label style="display:block;margin-bottom:8px;font-weight:600;color:var(--ink);">{{ __('blog.username') }}</label>
            <input type="text" value="{{ $user->username }}" disabled style="width:100%;padding:10px 12px;border:1px solid #e6eee6;border-radius:8px;background:#f5f5f5;color:#999;box-sizing:border-box;">
            <div style="font-size:12px;color:#999;margin-top:4px;">{{ __('blog.username_cannot_change') }}</div>
          </div>

          <div class="form-group" style="margin-bottom:20px;">
            <label style="display:block;margin-bottom:8px;font-weight:600;color:var(--ink);">{{ __('blog.nickname') }}</label>
            <input type="text" name="nickname" value="{{ $user->nickname }}" style="width:100%;padding:10px 12px;border:1px solid #e6eee6;border-radius:8px;font-size:14px;box-sizing:border-box;transition:border-color .2s;">
          </div>

          <div class="form-group" style="margin-bottom:20px;">
            <label style="display:block;margin-bottom:8px;font-weight:600;color:var(--ink);">{{ __('blog.email') }}</label>
            <input type="email" name="email" value="{{ $user->email }}" style="width:100%;padding:10px 12px;border:1px solid #e6eee6;border-radius:8px;font-size:14px;box-sizing:border-box;transition:border-color .2s;">
          </div>

          <div class="form-group" style="margin-bottom:20px;">
            <label style="display:block;margin-bottom:8px;font-weight:600;color:var(--ink);">{{ __('blog.phone') }}</label>
            <input type="text" name="phone" value="{{ $user->phone }}" style="width:100%;padding:10px 12px;border:1px solid #e6eee6;border-radius:8px;font-size:14px;box-sizing:border-box;transition:border-color .2s;">
          </div>

          <div class="form-group" style="margin-bottom:20px;">
            <label style="display:block;margin-bottom:8px;font-weight:600;color:var(--ink);">{{ __('blog.gender') }}</label>
            <select name="gender" style="width:100%;padding:10px 12px;border:1px solid #e6eee6;border-radius:8px;font-size:14px;box-sizing:border-box;transition:border-color .2s;background:#fff;">
              <option value="0" {{ $user->gender == 0 ? 'selected' : '' }}>{{ __('blog.gender_unknown') }}</option>
              <option value="1" {{ $user->gender == 1 ? 'selected' : '' }}>{{ __('blog.gender_male') }}</option>
              <option value="2" {{ $user->gender == 2 ? 'selected' : '' }}>{{ __('blog.gender_female') }}</option>
            </select>
          </div>

          <div class="form-group" style="margin-bottom:20px;">
            <label style="display:block;margin-bottom:8px;font-weight:600;color:var(--ink);">{{ __('blog.birthday') }}</label>
            <input type="date" name="birthday" value="{{ $user->birthday ? $user->birthday->format('Y-m-d') : '' }}" style="width:100%;padding:10px 12px;border:1px solid #e6eee6;border-radius:8px;font-size:14px;box-sizing:border-box;transition:border-color .2s;">
          </div>

          <div class="form-group" style="margin-bottom:20px;">
            <label style="display:block;margin-bottom:8px;font-weight:600;color:var(--ink);">{{ __('blog.avatar') }}</label>
            <div style="display:flex;gap:16px;align-items:flex-start;">
              <!-- 头像预览 -->
              <div class="avatar-preview-wrapper" style="flex-shrink:0;">
                <img id="avatarPreview" src="{{ $user->avatar_url ?: '/static/admin/images/avatar.jpg' }}" alt="Avatar" style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:2px solid #e6eee6;background:#f5f5f5;">
              </div>
              <!-- 上传控件 -->
              <div style="flex:1;">
                <input type="file" id="avatarFileInput" name="avatar_file" accept="image/*" style="display:none;">
                <button type="button" id="avatarUploadBtn" style="padding:8px 16px;background:var(--primary-weak);color:var(--primary);border:1px solid var(--primary);border-radius:6px;font-size:14px;cursor:pointer;transition:all .2s;">
                  <i class="bi bi-image"></i> 选择图片
                </button>
                <div style="font-size:12px;color:#999;margin-top:8px;">
                  支持 JPG、PNG、GIF 格式，最大 2MB
                </div>
                <!-- 隐藏字段，用于保留原有头像URL（如果用户只修改其他字段） -->
                <input type="hidden" name="avatar" id="avatarUrlInput" value="{{ $user->avatar }}">
              </div>
            </div>
          </div>

          <div class="form-actions" style="margin-top:24px;">
            <button type="submit" class="btn-submit" style="padding:10px 24px;background:var(--primary);color:#111;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;transition:all .2s;">{{ __('blog.save') }}</button>
            <a href="{{ route('site.member.index') }}" style="padding:10px 24px;margin-left:12px;background:#f5f5f5;color:var(--ink);border:1px solid #e6eee6;border-radius:8px;font-size:14px;font-weight:500;text-decoration:none;display:inline-block;transition:all .2s;">{{ __('blog.cancel') }}</a>
          </div>
        </form>
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

  /* 表单样式 */
  .form-group input:focus,
  .form-group select:focus {
    outline: none;
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 3px rgba(44, 163, 106, .1);
  }

  .btn-submit:hover {
    background: var(--primary-dark) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(44, 163, 106, .3);
  }

  .btn-submit:disabled {
    background: #ccc !important;
    cursor: not-allowed !important;
    transform: none !important;
  }

  #avatarUploadBtn:hover {
    background: var(--primary) !important;
    color: #fff !important;
  }

  #avatarPreview {
    transition: all .2s;
  }

  #avatarPreview:hover {
    opacity: 0.8;
  }
</style>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('profileForm');
    if (!form) return;

    // 头像上传功能
    var avatarFileInput = document.getElementById('avatarFileInput');
    var avatarUploadBtn = document.getElementById('avatarUploadBtn');
    var avatarPreview = document.getElementById('avatarPreview');
    var avatarUrlInput = document.getElementById('avatarUrlInput');

    if (avatarUploadBtn && avatarFileInput) {
      avatarUploadBtn.addEventListener('click', function() {
        avatarFileInput.click();
      });

      avatarFileInput.addEventListener('change', function(e) {
        var file = e.target.files[0];
        if (!file) return;

        // 验证文件类型
        if (!file.type.match('image.*')) {
          alert('请选择图片文件');
          return;
        }

        // 验证文件大小（2MB）
        if (file.size > 2 * 1024 * 1024) {
          alert('图片大小不能超过2MB');
          return;
        }

        // 预览图片
        var reader = new FileReader();
        reader.onload = function(e) {
          avatarPreview.src = e.target.result;
          // 清空隐藏的avatar URL，使用上传的文件
          avatarUrlInput.value = '';
        };
        reader.readAsDataURL(file);
      });
    }

    // 移除表单的默认提交行为
    form.onsubmit = function(e) {
      e.preventDefault();
      e.stopPropagation();
      return false;
    };

    // 为提交按钮添加点击事件
    var submitBtn = form.querySelector('.btn-submit');
    if (submitBtn) {
      submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        handleFormSubmit();
        return false;
      });
    }

    // 处理表单提交
    function handleFormSubmit() {
      var submitBtn = form.querySelector('.btn-submit');
      var originalText = submitBtn ? submitBtn.textContent : '';

      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = '{{ __("blog.saving") }}...';
      }

      var formData = new FormData(form);
      var token = document.querySelector('meta[name="csrf-token"]')?.content || '';

      fetch('{{ route("site.member.updateProfile") }}', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': token,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData
      })
      .then(function(response) {
        // 确保响应是JSON格式
        var contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
          throw new Error('服务器返回的不是JSON格式');
        }
        return response.json().then(function(data) {
          if (!response.ok) {
            // 处理验证错误
            var errorMsg = data.message || '{{ __("blog.update_failed") }}';
            if (data.errors) {
              var errorMessages = [];
              for (var field in data.errors) {
                if (data.errors.hasOwnProperty(field)) {
                  errorMessages.push(data.errors[field].join(', '));
                }
              }
              if (errorMessages.length > 0) {
                errorMsg = errorMessages.join('\n');
              }
            }
            throw new Error(errorMsg);
          }
          return data;
        });
      })
      .then(function(data) {
        if (data.code === 0) {
          alert('{{ __("blog.update_success") }}');
          // 延迟一下再刷新，确保用户看到提示
          setTimeout(function() {
            window.location.href = '{{ route("site.member.profile") }}';
          }, 500);
        } else {
          alert(data.message || '{{ __("blog.update_failed") }}');
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
          }
        }
      })
      .catch(function(error) {
        console.error('Update error:', error);
        alert(error.message || '{{ __("blog.update_failed") }}');
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = originalText;
        }
      });
    }
  });
</script>
@endsection

