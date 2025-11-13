<!-- 登录/注册弹窗 -->
<link rel="stylesheet" href="/static/blog/css/auth-modal.css" />
<div class="auth-modal-overlay" id="authModalOverlay" style="display:none;">
  <div class="auth-modal" id="authModal">
    <!-- 关闭按钮 -->
    <button type="button" class="auth-modal-close" id="authModalClose">
      <i class="bi bi-x-lg"></i>
    </button>
    
    <!-- 登录表单 -->
    <div class="auth-form auth-form-login" id="authFormLogin">
      <h2>{{ __('blog.login') }}</h2>
      
      <form id="loginForm" action="{{ route('site.auth.login') }}" method="POST">
        @csrf
        <div class="form-group">
          <label>{{ __('blog.username_or_email') }}</label>
          <input type="text" name="username" required>
        </div>
        
        <div class="form-group">
          <label>{{ __('blog.password') }}</label>
          <input type="password" name="password" required>
        </div>
        
        <div class="form-group" style="display:flex;align-items:center;justify-content:space-between;">
          <label style="display:flex;align-items:center;gap:6px;color:#666;cursor:pointer;">
            <input type="checkbox" name="remember" value="1">
            <span>{{ __('blog.remember_me') }}</span>
          </label>
        </div>
        
        <button type="submit" class="btn-auth-submit">{{ __('blog.login') }}</button>
        
        <div style="text-align:center;margin-top:16px;font-size:14px;color:#666;">
          {{ __('blog.no_account') }}<a href="javascript:;" class="auth-switch-link" data-switch="register">{{ __('blog.register_now') }}</a>
        </div>
      </form>
    </div>
    
    <!-- 注册表单 -->
    <div class="auth-form auth-form-register" id="authFormRegister" style="display:none;">
      <h2>{{ __('blog.register') }}</h2>
      
      <form id="registerForm" action="{{ route('site.auth.register') }}" method="POST">
        @csrf
        <div class="form-group">
          <label>{{ __('blog.username') }} <span style="color:#999;font-weight:400;">{{ __('blog.at_least_3_chars') }}</span></label>
          <input type="text" name="username" required minlength="3">
        </div>
        
        <div class="form-group">
          <label>{{ __('blog.nickname') }} <span style="color:#999;font-weight:400;">{{ __('blog.optional') }}</span></label>
          <input type="text" name="nickname">
        </div>
        
        <div class="form-group">
          <label>{{ __('blog.email') }}</label>
          <input type="email" name="email" required>
        </div>
        
        <div class="form-group">
          <label>{{ __('blog.password') }} <span style="color:#999;font-weight:400;">{{ __('blog.at_least_6_chars') }}</span></label>
          <input type="password" name="password" required minlength="6">
        </div>
        
        <div class="form-group">
          <label>{{ __('blog.confirm_password') }}</label>
          <input type="password" name="password_confirmation" required minlength="6">
        </div>
        
        <button type="submit" class="btn-auth-submit">{{ __('blog.register') }}</button>
        
        <div style="text-align:center;margin-top:16px;font-size:14px;color:#666;">
          {{ __('blog.have_account') }}<a href="javascript:;" class="auth-switch-link" data-switch="login">{{ __('blog.login_now') }}</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="/static/blog/js/auth-modal.js"></script>
<script>
  // 配置认证弹窗
  window.AuthConfig = {
    loginUrl: '{{ route("site.auth.login") }}',
    registerUrl: '{{ route("site.auth.register") }}',
    texts: {
      logging_in: @json(__('blog.logging_in')),
      registering: @json(__('blog.registering'))
    }
  };
</script>

