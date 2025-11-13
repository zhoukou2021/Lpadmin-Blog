/**
 * 会员中心个人资料编辑功能
 */
(function() {
  'use strict';
  
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
          if (avatarUrlInput) {
            avatarUrlInput.value = '';
          }
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
        submitBtn.textContent = window.MemberConfig && window.MemberConfig.texts && window.MemberConfig.texts.saving 
          ? window.MemberConfig.texts.saving 
          : '保存中...';
      }

      var formData = new FormData(form);
      var token = document.querySelector('meta[name="csrf-token"]')?.content || '';
      var updateUrl = window.MemberConfig && window.MemberConfig.updateProfileUrl 
        ? window.MemberConfig.updateProfileUrl 
        : form.action || '';

      if (!updateUrl) {
        console.error('Update profile URL is not defined');
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = originalText;
        }
        return;
      }

      fetch(updateUrl, {
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
            var errorMsg = data.message || (window.MemberConfig && window.MemberConfig.texts && window.MemberConfig.texts.updateFailed) || '更新失败';
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
          var successMsg = window.MemberConfig && window.MemberConfig.texts && window.MemberConfig.texts.updateSuccess 
            ? window.MemberConfig.texts.updateSuccess 
            : '更新成功';
          alert(successMsg);
          // 延迟一下再刷新，确保用户看到提示
          setTimeout(function() {
            var profileUrl = window.MemberConfig && window.MemberConfig.profileUrl 
              ? window.MemberConfig.profileUrl 
              : window.location.href;
            window.location.href = profileUrl;
          }, 500);
        } else {
          var failMsg = data.message || (window.MemberConfig && window.MemberConfig.texts && window.MemberConfig.texts.updateFailed) || '更新失败';
          alert(failMsg);
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
          }
        }
      })
      .catch(function(error) {
        console.error('Update error:', error);
        var failMsg = error.message || (window.MemberConfig && window.MemberConfig.texts && window.MemberConfig.texts.updateFailed) || '更新失败';
        alert(failMsg);
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = originalText;
        }
      });
    }
  });
})();

