<!-- 登录/注册弹窗 -->
<div class="auth-modal-overlay" id="authModalOverlay" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
  <div class="auth-modal" id="authModal" style="background:#fff;border-radius:16px;width:90%;max-width:420px;max-height:90vh;overflow-y:auto;box-shadow:0 12px 48px rgba(0,0,0,.2);position:relative;animation:authModalSlideIn .3s ease;">
    <!-- 关闭按钮 -->
    <button type="button" class="auth-modal-close" id="authModalClose" style="position:absolute;top:12px;right:12px;width:32px;height:32px;border:none;background:transparent;color:#999;font-size:20px;cursor:pointer;z-index:10;display:flex;align-items:center;justify-content:center;border-radius:50%;transition:all .2s;">
      <i class="bi bi-x-lg"></i>
    </button>
    
    <!-- 登录表单 -->
    <div class="auth-form auth-form-login" id="authFormLogin" style="padding:32px 24px;">
      <h2 style="margin:0 0 24px;font-size:24px;font-weight:700;color:var(--ink);text-align:center;">{{ __('blog.login') }}</h2>
      
      <form id="loginForm" style="display:flex;flex-direction:column;gap:16px;">
        <div class="form-group">
          <label style="display:block;margin-bottom:6px;font-size:14px;font-weight:600;color:var(--ink);">{{ __('blog.username_or_email') }}</label>
          <input type="text" name="username" required style="width:100%;padding:10px 12px;border:1px solid #e6eee6;border-radius:8px;font-size:14px;box-sizing:border-box;transition:border-color .2s;">
        </div>
        
        <div class="form-group">
          <label style="display:block;margin-bottom:6px;font-size:14px;font-weight:600;color:var(--ink);">{{ __('blog.password') }}</label>
          <input type="password" name="password" required style="width:100%;padding:10px 12px;border:1px solid #e6eee6;border-radius:8px;font-size:14px;box-sizing:border-box;transition:border-color .2s;">
        </div>
        
        <div class="form-group" style="display:flex;align-items:center;justify-content:space-between;">
          <label style="display:flex;align-items:center;gap:6px;font-size:14px;color:#666;cursor:pointer;">
            <input type="checkbox" name="remember" value="1" style="cursor:pointer;">
            <span>{{ __('blog.remember_me') }}</span>
          </label>
        </div>
        
        <button type="submit" class="btn-auth-submit" style="width:100%;padding:12px;background:var(--primary);color:#111;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;transition:all .2s;margin-top:8px;">{{ __('blog.login') }}</button>
        
        <div style="text-align:center;margin-top:16px;font-size:14px;color:#666;">
          {{ __('blog.no_account') }}<a href="javascript:;" class="auth-switch-link" data-switch="register" style="color:var(--primary);text-decoration:none;font-weight:600;">{{ __('blog.register_now') }}</a>
        </div>
      </form>
    </div>
    
    <!-- 注册表单 -->
    <div class="auth-form auth-form-register" id="authFormRegister" style="display:none;padding:32px 24px;">
      <h2 style="margin:0 0 24px;font-size:24px;font-weight:700;color:var(--ink);text-align:center;">{{ __('blog.register') }}</h2>
      
      <form id="registerForm" style="display:flex;flex-direction:column;gap:16px;">
        <div class="form-group">
          <label style="display:block;margin-bottom:6px;font-size:14px;font-weight:600;color:var(--ink);">{{ __('blog.username') }} <span style="color:#999;font-weight:400;">{{ __('blog.at_least_3_chars') }}</span></label>
          <input type="text" name="username" required minlength="3" style="width:100%;padding:10px 12px;border:1px solid #e6eee6;border-radius:8px;font-size:14px;box-sizing:border-box;transition:border-color .2s;">
        </div>
        
        <div class="form-group">
          <label style="display:block;margin-bottom:6px;font-size:14px;font-weight:600;color:var(--ink);">{{ __('blog.nickname') }} <span style="color:#999;font-weight:400;">{{ __('blog.optional') }}</span></label>
          <input type="text" name="nickname" style="width:100%;padding:10px 12px;border:1px solid #e6eee6;border-radius:8px;font-size:14px;box-sizing:border-box;transition:border-color .2s;">
        </div>
        
        <div class="form-group">
          <label style="display:block;margin-bottom:6px;font-size:14px;font-weight:600;color:var(--ink);">{{ __('blog.email') }}</label>
          <input type="email" name="email" required style="width:100%;padding:10px 12px;border:1px solid #e6eee6;border-radius:8px;font-size:14px;box-sizing:border-box;transition:border-color .2s;">
        </div>
        
        <div class="form-group">
          <label style="display:block;margin-bottom:6px;font-size:14px;font-weight:600;color:var(--ink);">{{ __('blog.password') }} <span style="color:#999;font-weight:400;">{{ __('blog.at_least_6_chars') }}</span></label>
          <input type="password" name="password" required minlength="6" style="width:100%;padding:10px 12px;border:1px solid #e6eee6;border-radius:8px;font-size:14px;box-sizing:border-box;transition:border-color .2s;">
        </div>
        
        <div class="form-group">
          <label style="display:block;margin-bottom:6px;font-size:14px;font-weight:600;color:var(--ink);">{{ __('blog.confirm_password') }}</label>
          <input type="password" name="password_confirmation" required minlength="6" style="width:100%;padding:10px 12px;border:1px solid #e6eee6;border-radius:8px;font-size:14px;box-sizing:border-box;transition:border-color .2s;">
        </div>
        
        <button type="submit" class="btn-auth-submit" style="width:100%;padding:12px;background:var(--primary);color:#111;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;transition:all .2s;margin-top:8px;">{{ __('blog.register') }}</button>
        
        <div style="text-align:center;margin-top:16px;font-size:14px;color:#666;">
          {{ __('blog.have_account') }}<a href="javascript:;" class="auth-switch-link" data-switch="login" style="color:var(--primary);text-decoration:none;font-weight:600;">{{ __('blog.login_now') }}</a>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  @keyframes authModalSlideIn {
    from {
      opacity: 0;
      transform: translateY(-20px) scale(0.95);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }
  
  .auth-modal-overlay {
    display: flex !important;
  }
  
  .auth-modal-overlay[style*="display:none"] {
    display: none !important;
  }
  
  .auth-modal-close:hover {
    background: #f5f5f5 !important;
    color: var(--ink) !important;
  }
  
  .form-group input:focus {
    outline: none;
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 3px rgba(44, 163, 106, .1);
  }
  
  .btn-auth-submit:hover {
    background: var(--primary-dark) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(44, 163, 106, .3);
  }
  
  .btn-auth-submit:active {
    transform: translateY(0);
  }
  
  .btn-auth-submit:disabled {
    background: #ccc !important;
    cursor: not-allowed !important;
    transform: none !important;
  }
  
  .auth-switch-link:hover {
    text-decoration: underline !important;
  }
  
  @media (max-width: 575.98px) {
    .auth-modal {
      width: 95% !important;
      border-radius: 12px !important;
    }
    
    .auth-form {
      padding: 24px 20px !important;
    }
    
    .auth-form h2 {
      font-size: 20px !important;
    }
  }
</style>

<script>
  // 认证弹窗管理器
  (function() {
    var AuthModal = {
      overlay: null,
      modal: null,
      loginForm: null,
      registerForm: null,
      
      init: function() {
        this.overlay = document.getElementById('authModalOverlay');
        this.modal = document.getElementById('authModal');
        this.loginForm = document.getElementById('authFormLogin');
        this.registerForm = document.getElementById('authFormRegister');
        
        if (!this.overlay) return;
        
        // 关闭按钮
        var closeBtn = document.getElementById('authModalClose');
        if (closeBtn) {
          closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            AuthModal.close();
          });
        }
        
        // 点击遮罩层关闭
        this.overlay.addEventListener('click', function(e) {
          if (e.target === this.overlay) {
            AuthModal.close();
          }
        }.bind(this));
        
        // 阻止弹窗内部点击事件冒泡
        if (this.modal) {
          this.modal.addEventListener('click', function(e) {
            e.stopPropagation();
          });
        }
        
        // ESC键关闭
        document.addEventListener('keydown', function(e) {
          if (e.key === 'Escape' && AuthModal.isOpen()) {
            AuthModal.close();
          }
        });
        
        // 表单切换
        var switchLinks = document.querySelectorAll('.auth-switch-link');
        switchLinks.forEach(function(link) {
          link.addEventListener('click', function(e) {
            e.preventDefault();
            var target = this.getAttribute('data-switch');
            AuthModal.switchForm(target);
          });
        });
        
        // 登录表单提交
        var loginForm = document.getElementById('loginForm');
        if (loginForm) {
          loginForm.addEventListener('submit', this.handleLogin.bind(this));
        }
        
        // 注册表单提交
        var registerForm = document.getElementById('registerForm');
        if (registerForm) {
          registerForm.addEventListener('submit', this.handleRegister.bind(this));
        }
      },
      
      open: function(type) {
        type = type || 'login';
        if (this.overlay) {
          this.overlay.style.display = 'flex';
          document.body.style.overflow = 'hidden';
          this.switchForm(type);
        }
      },
      
      close: function() {
        if (this.overlay) {
          this.overlay.style.display = 'none';
          document.body.style.overflow = '';
        }
      },
      
      isOpen: function() {
        return this.overlay && this.overlay.style.display !== 'none';
      },
      
      switchForm: function(type) {
        if (!this.loginForm || !this.registerForm) return;
        
        if (type === 'register') {
          this.loginForm.style.display = 'none';
          this.registerForm.style.display = 'block';
        } else {
          this.loginForm.style.display = 'block';
          this.registerForm.style.display = 'none';
        }
      },
      
      handleLogin: function(e) {
        e.preventDefault();
        var form = e.target;
        var submitBtn = form.querySelector('.btn-auth-submit');
        var originalText = submitBtn ? submitBtn.textContent : '';
        
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.textContent = @json(__('blog.logging_in'));
        }
        
        var formData = new FormData(form);
        var token = document.querySelector('meta[name="csrf-token"]')?.content || '';
        
        fetch('{{ route("site.auth.login") }}', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
          },
          body: formData
        })
        .then(function(response) {
          return response.json().then(function(data) {
            if (!response.ok) {
              throw new Error(data.message || '登录失败');
            }
            return data;
          });
        })
        .then(function(data) {
          if (data.code === 0) {
            AuthModal.close();
            AuthModal.updateUserInfo(data.data.user);
            if (typeof window.onAuthSuccess === 'function') {
              window.onAuthSuccess(data.data.user);
            }
            // 刷新页面以更新用户状态
            location.reload();
          } else {
            alert(data.message || '登录失败');
            if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.textContent = originalText;
            }
          }
        })
        .catch(function(error) {
          console.error('Login error:', error);
          alert(error.message || '登录失败，请重试');
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
          }
        });
      },
      
      handleRegister: function(e) {
        e.preventDefault();
        var form = e.target;
        var submitBtn = form.querySelector('.btn-auth-submit');
        var originalText = submitBtn ? submitBtn.textContent : '';
        
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.textContent = @json(__('blog.registering'));
        }
        
        var formData = new FormData(form);
        var token = document.querySelector('meta[name="csrf-token"]')?.content || '';
        
        fetch('{{ route("site.auth.register") }}', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
          },
          body: formData
        })
        .then(function(response) {
          return response.json().then(function(data) {
            if (!response.ok) {
              throw new Error(data.message || '注册失败');
            }
            return data;
          });
        })
        .then(function(data) {
          if (data.code === 0) {
            AuthModal.close();
            AuthModal.updateUserInfo(data.data.user);
            if (typeof window.onAuthSuccess === 'function') {
              window.onAuthSuccess(data.data.user);
            }
            // 刷新页面以更新用户状态
            location.reload();
          } else {
            alert(data.message || '注册失败');
            if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.textContent = originalText;
            }
          }
        })
        .catch(function(error) {
          console.error('Register error:', error);
          alert(error.message || '注册失败，请重试');
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
          }
        });
      },
      
      updateUserInfo: function(user) {
        // 更新用户信息显示（如果需要）
        if (typeof window.updateUserDisplay === 'function') {
          window.updateUserDisplay(user);
        }
      }
    };
    
    // 全局函数：打开登录弹窗
    window.openAuthModal = function(type) {
      AuthModal.open(type);
    };
    
    // 全局函数：关闭弹窗
    window.closeAuthModal = function() {
      AuthModal.close();
    };
    
    // 初始化
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', function() {
        AuthModal.init();
      });
    } else {
      AuthModal.init();
    }
    
    // 将 AuthModal 暴露到全局，以便其他地方使用
    window.AuthModal = AuthModal;
  })();
</script>

