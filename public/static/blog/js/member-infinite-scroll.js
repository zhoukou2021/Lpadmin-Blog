/**
 * 会员中心无限滚动加载功能
 * 支持评论、收藏、点赞页面的无限滚动
 * 
 * 使用方式：
 *   1. 在页面中引入此文件
 *   2. 调用 MemberInfiniteScroll.init(config) 初始化
 *   config 格式：
 *     {
 *       loadMoreUrl: '/member/comments/load-more',  // 加载更多数据的URL
 *       hasMore: true,                               // 是否还有更多数据
 *       renderItem: function(item) { return html; }, // 渲染单个项目的函数
 *       containerSelector: '.comments-list',         // 容器选择器
 *       loaderId: 'infiniteScrollLoader',            // 加载器元素ID
 *       endId: 'infiniteScrollEnd'                   // 结束提示元素ID
 *     }
 */
(function() {
  'use strict';
  
  var MemberInfiniteScroll = {
    instances: {},
    
    init: function(config) {
      if (!config || !config.loadMoreUrl) {
        console.error('MemberInfiniteScroll: loadMoreUrl is required');
        return;
      }
      
      var instanceId = config.containerSelector || 'default';
      if (this.instances[instanceId]) {
        // 如果已存在实例，先清理
        this.destroy(instanceId);
      }
      
      var instance = {
        currentPage: 1,
        isLoading: false,
        hasMore: config.hasMore !== false,
        loadMoreUrl: config.loadMoreUrl,
        renderItem: config.renderItem || function(item) { return ''; },
        container: document.querySelector(config.containerSelector || '.posts-container'),
        loader: document.getElementById(config.loaderId || 'infiniteScrollLoader'),
        end: document.getElementById(config.endId || 'infiniteScrollEnd'),
        threshold: config.threshold || 200
      };
      
      if (!instance.container) {
        console.error('MemberInfiniteScroll: container not found');
        return;
      }
      
      this.instances[instanceId] = instance;
      this._initInstance(instanceId);
    },
    
    _initInstance: function(instanceId) {
      var inst = this.instances[instanceId];
      if (!inst || !inst.hasMore) return;
      
      var ticking = false;
      
      function checkScroll() {
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        var windowHeight = window.innerHeight || document.documentElement.clientHeight;
        var documentHeight = document.documentElement.scrollHeight;
        
        if (scrollTop + windowHeight >= documentHeight - inst.threshold) {
          MemberInfiniteScroll._loadMore(instanceId);
        }
        ticking = false;
      }
      
      window.addEventListener('scroll', function() {
        if (!ticking) {
          window.requestAnimationFrame(checkScroll);
          ticking = true;
        }
      }, { passive: true });
    },
    
    _loadMore: function(instanceId) {
      var inst = this.instances[instanceId];
      if (!inst || inst.isLoading || !inst.hasMore) return;
      
      inst.isLoading = true;
      if (inst.loader) inst.loader.style.display = 'block';
      if (inst.end) inst.end.style.display = 'none';
      
      inst.currentPage++;
      var url = inst.loadMoreUrl + '?page=' + inst.currentPage;
      
      fetch(url, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(function(response) {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(function(data) {
        if (data.code === 0 && data.data && data.data.length > 0) {
          if (inst.container) {
            data.data.forEach(function(item) {
              var html = inst.renderItem(item);
              if (html) {
                inst.container.insertAdjacentHTML('beforeend', html);
              }
            });
          }
          inst.hasMore = data.has_more || false;
          if (!inst.hasMore && inst.end) {
            inst.end.style.display = 'block';
          }
        } else {
          inst.hasMore = false;
          if (inst.end) inst.end.style.display = 'block';
        }
      })
      .catch(function(error) {
        console.error('Error loading more data:', error);
        inst.hasMore = false;
        if (inst.end) inst.end.style.display = 'block';
      })
      .finally(function() {
        inst.isLoading = false;
        if (inst.loader) inst.loader.style.display = 'none';
      });
    },
    
    destroy: function(instanceId) {
      delete this.instances[instanceId];
    }
  };
  
  // 暴露到全局
  window.MemberInfiniteScroll = MemberInfiniteScroll;
})();

