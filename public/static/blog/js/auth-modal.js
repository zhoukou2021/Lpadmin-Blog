/**
 * 认证弹窗管理器
 */
(function() {
  'use strict';
  
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
        submitBtn.textContent = this.getText('logging_in') || '登录中...';
      }
      
      var formData = new FormData(form);
      var token = document.querySelector('meta[name="csrf-token"]')?.content || '';
      var loginUrl = this.getLoginUrl();
      
      if (!loginUrl) {
        console.error('Login URL is not defined');
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = originalText;
        }
        return;
      }
      
      fetch(loginUrl, {
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
        submitBtn.textContent = this.getText('registering') || '注册中...';
      }
      
      var formData = new FormData(form);
      var token = document.querySelector('meta[name="csrf-token"]')?.content || '';
      var registerUrl = this.getRegisterUrl();
      
      if (!registerUrl) {
        console.error('Register URL is not defined');
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = originalText;
        }
        return;
      }
      
      fetch(registerUrl, {
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
    },
    
    getLoginUrl: function() {
      // 从全局配置或 data 属性获取
      if (window.AuthConfig && window.AuthConfig.loginUrl) {
        return window.AuthConfig.loginUrl;
      }
      // 尝试从表单的 action 属性获取
      var loginForm = document.getElementById('loginForm');
      if (loginForm && loginForm.action) {
        return loginForm.action;
      }
      return null;
    },
    
    getRegisterUrl: function() {
      // 从全局配置或 data 属性获取
      if (window.AuthConfig && window.AuthConfig.registerUrl) {
        return window.AuthConfig.registerUrl;
      }
      // 尝试从表单的 action 属性获取
      var registerForm = document.getElementById('registerForm');
      if (registerForm && registerForm.action) {
        return registerForm.action;
      }
      return null;
    },
    
    getText: function(key) {
      // 从全局配置获取文本
      if (window.AuthConfig && window.AuthConfig.texts && window.AuthConfig.texts[key]) {
        return window.AuthConfig.texts[key];
      }
      return null;
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

