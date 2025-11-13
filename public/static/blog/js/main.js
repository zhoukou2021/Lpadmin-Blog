/**
 * 博客主页面 JavaScript
 * 处理语言切换和用户登出功能
 */

(function(){
    'use strict';
    
    // 语言切换功能
    function initLangSwitcher() {
        var langSwitcher = document.getElementById('langSwitcher');
        if (!langSwitcher) return;
        
        langSwitcher.addEventListener('change', function(){
            var to = this.value;
            var currentPath = location.pathname;
            
            // 检查是否定义了全局配置
            if (typeof window.BlogConfig === 'undefined' || !window.BlogConfig.languageSwitchUrl) {
                console.error('BlogConfig.languageSwitchUrl is not defined');
                return;
            }
            
            // 使用AJAX或表单提交切换语言
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = window.BlogConfig.languageSwitchUrl;
            
            // 添加CSRF token
            var csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.content || '';
            form.appendChild(csrfInput);
            
            // 添加语言参数
            var langInput = document.createElement('input');
            langInput.type = 'hidden';
            langInput.name = 'lang';
            langInput.value = to;
            form.appendChild(langInput);
            
            // 添加重定向路径
            var redirectInput = document.createElement('input');
            redirectInput.type = 'hidden';
            redirectInput.name = 'redirect';
            redirectInput.value = currentPath;
            form.appendChild(redirectInput);
            
            document.body.appendChild(form);
            form.submit();
        });
    }
    
    // 登出函数
    function handleLogout() {
        var logoutConfirm = window.BlogConfig?.logoutConfirm || '确定要登出吗？';
        
        if (!confirm(logoutConfirm)) {
            return;
        }
        
        var token = document.querySelector('meta[name="csrf-token"]')?.content || '';
        var logoutUrl = window.BlogConfig?.logoutUrl;
        
        if (!logoutUrl) {
            console.error('BlogConfig.logoutUrl is not defined');
            return;
        }
        
        fetch(logoutUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.code === 0) {
                location.reload();
            } else {
                alert(data.message || '登出失败');
            }
        })
        .catch(function(error) {
            console.error('Logout error:', error);
            location.reload();
        });
    }
    
    // 将 handleLogout 暴露到全局作用域
    window.handleLogout = handleLogout;
    
    // 初始化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLangSwitcher);
    } else {
        initLangSwitcher();
    }
})();

