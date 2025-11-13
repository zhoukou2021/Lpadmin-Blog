/**
 * 文章详情页功能
 * 包含：评论表单、点赞、收藏功能
 */
(function() {
  'use strict';
  
  // 评论表单功能
  function initCommentForm() {
    var commentForm = document.getElementById('commentForm');
    if (!commentForm) {
      console.warn('评论表单未找到');
      return;
    }
    
    var commentContent = document.getElementById('commentContent');
    var commentParentId = document.getElementById('commentParentId');
    var commentReplyTo = document.getElementById('commentReplyTo');
    var replyToName = document.getElementById('replyToName');
    var replyToPrefix = document.getElementById('replyToPrefix');
    var cancelReply = document.getElementById('cancelReply');
    var submitBtn = commentForm.querySelector('button[type="submit"]');
    
    var config = window.PostDetailConfig || {};
    var translations = config.texts || {};

    // 初始化回复按钮
    function initReplyButtons() {
      var replyButtons = document.querySelectorAll('.btn-reply-comment');
      replyButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          var commentId = this.getAttribute('data-comment-id');
          var commentName = this.getAttribute('data-comment-name');
          if (commentParentId) commentParentId.value = commentId;
          if (replyToName) replyToName.textContent = commentName;
          if (replyToPrefix && translations.replyTo) {
            // 替换 :name 为实际名称
            var replyText = translations.replyTo.replace(':name', commentName);
            replyToPrefix.textContent = replyText;
          }
          if (commentReplyTo) commentReplyTo.style.display = 'inline';
          if (cancelReply) cancelReply.style.display = 'inline';
          if (commentContent) commentContent.focus();
          // 滚动到评论表单
          commentForm.scrollIntoView({behavior: 'smooth', block: 'nearest'});
        });
      });
    }

    // 取消回复
    if (cancelReply) {
      cancelReply.addEventListener('click', function(e) {
        e.preventDefault();
        if (commentParentId) commentParentId.value = '0';
        if (commentReplyTo) commentReplyTo.style.display = 'none';
        cancelReply.style.display = 'none';
        if (commentContent) commentContent.value = '';
      });
    }

    // 提交评论
    commentForm.addEventListener('submit', function(e) {
      e.preventDefault();
      e.stopPropagation();
      
      if (!commentContent || !commentContent.value.trim()) {
        alert(translations.commentRequired || '评论内容不能为空');
        return false;
      }

      if (commentContent.value.length < 3) {
        alert(translations.commentMinLength || '评论内容至少需要3个字符');
        return false;
      }

      if (commentContent.value.length > 2000) {
        alert(translations.commentMaxLength || '评论内容不能超过2000个字符');
        return false;
      }

      // 禁用提交按钮
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = translations.submitting || '提交中...';
      }

      // 获取提交URL
      var url = commentForm.getAttribute('action') || (config.commentStoreUrl || '');
      var formData = new FormData(commentForm);
      var token = document.querySelector('meta[name="csrf-token"]')?.content || '';
      
      // 确保使用POST方法
      if (!token) {
        // 如果meta标签中没有token，从表单中获取
        var csrfInput = commentForm.querySelector('input[name="_token"]');
        token = csrfInput ? csrfInput.value : '';
      }

      fetch(url, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': token,
          'Accept': 'application/json',
        },
        body: formData
      })
      .then(function(response) {
        if (!response.ok) {
          return response.json().then(function(data) {
            var error = new Error(data.message || translations.commentFailed || '评论失败');
            error.processed = true; // 标记为已处理
            
            // 如果是401未授权错误，提示登录
            if (response.status === 401) {
              if (typeof openAuthModal === 'function') {
                openAuthModal('login');
              }
              alert(data.message || translations.commentLoginRequired || '请先登录后再发布评论');
              return Promise.reject(error);
            }
            // 如果是403禁止访问错误（敏感词检测）
            if (response.status === 403) {
              alert(data.message || translations.commentSensitiveWord || '评论内容包含敏感词，无法提交');
              return Promise.reject(error);
            }
            // 其他错误
            alert(data.message || translations.commentFailed || '评论失败');
            return Promise.reject(error);
          });
        }
        return response.json();
      })
      .then(function(data) {
        if (data.code === 0) {
          // 成功，刷新页面
          alert(translations.commentSuccess || '评论成功');
          location.reload();
        } else {
          alert(data.message || translations.commentFailed || '评论失败');
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = translations.submitComment || '提交评论';
          }
        }
      })
      .catch(function(error) {
        console.error('Error:', error);
        // 如果错误已经在then中处理过（显示了alert），则不再重复显示
        if (error.message && !error.processed) {
          alert(error.message || translations.commentFailed || '评论失败');
        }
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = translations.submitComment || '提交评论';
        }
      });
      
      return false;
    });

    // 初始化回复按钮
    initReplyButtons();
  }

  // 点赞功能
  function initLikeButtons() {
    var likeButtons = document.querySelectorAll('.post-like-item');
    if (!likeButtons.length) return;
    
    var config = window.PostDetailConfig || {};
    var likeUrlTemplate = config.likeUrlTemplate || '';
    
    likeButtons.forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var postId = this.getAttribute('data-post-id');
        if (!postId) return;
        
        var icon = this.querySelector('i');
        var countSpan = this.querySelector('.likes-count');
        var originalClass = icon ? icon.className : '';
        
        // 禁用按钮，防止重复点击
        if (this.dataset.loading === 'true') return;
        this.dataset.loading = 'true';
        this.style.opacity = '0.6';
        this.style.pointerEvents = 'none';
        
        var url = likeUrlTemplate.replace(':id', postId);
        var token = document.querySelector('meta[name="csrf-token"]')?.content || '';
        
        fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({})
        })
        .then(function(response) {
          if (!response.ok) {
            return response.json().then(function(data) {
              throw new Error(data.message || '操作失败');
            });
          }
          return response.json();
        })
        .then(function(data) {
          if (data.code === 0) {
            // 更新图标
            if (icon) {
              if (data.liked) {
                icon.className = originalClass.replace('bi-hand-thumbs-up', 'bi-hand-thumbs-up-fill');
              } else {
                icon.className = originalClass.replace('bi-hand-thumbs-up-fill', 'bi-hand-thumbs-up');
              }
            }
            // 更新数量
            if (countSpan) {
              countSpan.textContent = data.likes_count || 0;
            }
          } else {
            alert(data.message || '操作失败');
          }
        })
        .catch(function(error) {
          console.error('Error:', error);
          alert(error.message || '操作失败，请重试');
        })
        .finally(function() {
          // 恢复按钮
          btn.dataset.loading = 'false';
          btn.style.opacity = '1';
          btn.style.pointerEvents = 'auto';
        });
      });
    });
  }
  
  // 收藏功能
  function initFavoriteButtons() {
    var favoriteButtons = document.querySelectorAll('.post-favorite-item');
    if (!favoriteButtons.length) return;
    
    var config = window.PostDetailConfig || {};
    var favoriteUrlTemplate = config.favoriteUrlTemplate || '';
    
    favoriteButtons.forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var postId = this.getAttribute('data-post-id');
        if (!postId) return;
        
        var icon = this.querySelector('i');
        var countSpan = this.querySelector('.favorites-count');
        var originalClass = icon ? icon.className : '';
        
        // 禁用按钮，防止重复点击
        if (this.dataset.loading === 'true') return;
        this.dataset.loading = 'true';
        this.style.opacity = '0.6';
        this.style.pointerEvents = 'none';
        
        var url = favoriteUrlTemplate.replace(':id', postId);
        var token = document.querySelector('meta[name="csrf-token"]')?.content || '';
        
        fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({})
        })
        .then(function(response) {
          if (!response.ok) {
            return response.json().then(function(data) {
              throw new Error(data.message || '操作失败');
            });
          }
          return response.json();
        })
        .then(function(data) {
          if (data.code === 0) {
            // 更新图标
            if (icon) {
              if (data.favorited) {
                icon.className = originalClass.replace('bi-star', 'bi-star-fill');
              } else {
                icon.className = originalClass.replace('bi-star-fill', 'bi-star');
              }
            }
            // 更新数量
            if (countSpan) {
              countSpan.textContent = data.favorites_count || 0;
            }
          } else {
            alert(data.message || '操作失败');
          }
        })
        .catch(function(error) {
          console.error('Error:', error);
          alert(error.message || '操作失败，请重试');
        })
        .finally(function() {
          // 恢复按钮
          btn.dataset.loading = 'false';
          btn.style.opacity = '1';
          btn.style.pointerEvents = 'auto';
        });
      });
    });
  }
  
  // 初始化所有功能
  function init() {
    initCommentForm();
    initLikeButtons();
    initFavoriteButtons();
  }
  
  // 确保DOM加载完成后执行
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();

