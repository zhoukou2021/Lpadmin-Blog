/**
 * 3D 百叶窗焦点图脚本
 * 使用方式：
 *   1. 在页面中引入此文件
 *   2. 调用 Banner3D.init(config) 初始化
 *   config 格式：
 *     {
 *       stageId: 'bannerStage',      // stage元素ID
 *       dotsId: 'bannerDots',          // 导航点容器ID
 *       prevId: 'bannerPrev',          // 上一张按钮ID
 *       nextId: 'bannerNext',          // 下一张按钮ID
 *       banners: [],                   // 广告数据数组
 *       autoPlay: true,                 // 是否自动播放
 *       autoPlayInterval: 5000          // 自动播放间隔（毫秒）
 *     }
 */
(function() {
  'use strict';
  
  var Banner3D = {
    instances: {},
    
    init: function(config) {
      if (!config || !config.stageId || !config.banners || !config.banners.length) {
        return;
      }
      
      var instanceId = config.stageId;
      if (this.instances[instanceId]) {
        // 如果已存在实例，先清理
        this.destroy(instanceId);
      }
      
      var instance = {
        stage: document.getElementById(config.stageId),
        dots: document.getElementById(config.dotsId),
        prevBtn: document.getElementById(config.prevId),
        nextBtn: document.getElementById(config.nextId),
        data: config.banners || [],
        autoPlay: config.autoPlay !== false,
        autoPlayInterval: config.autoPlayInterval || 5000,
        w: 0,
        h: 0,
        slices: 12,
        current: 0,
        busy: false,
        autoTimer: null
      };
      
      if (!instance.stage || !instance.data.length) {
        return;
      }
      
      this.instances[instanceId] = instance;
      this._initInstance(instanceId);
    },
    
    _initInstance: function(instanceId) {
      var inst = this.instances[instanceId];
      if (!inst) return;
      
      inst.w = inst.stage.clientWidth || document.body.clientWidth || window.innerWidth;
      inst.h = inst.stage.clientHeight;
      this._draw(instanceId, 0);
      this._renderDots(instanceId);
      
      // 绑定左右切换按钮事件
      if (inst.prevBtn) {
        inst.prevBtn.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          e.stopImmediatePropagation();
          if (inst.busy || !inst.data.length) return false;
          var nextIndex = (inst.current - 1 + inst.data.length) % inst.data.length;
          Banner3D._go(instanceId, nextIndex);
          return false;
        }, true);
      }
      
      if (inst.nextBtn) {
        inst.nextBtn.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          e.stopImmediatePropagation();
          if (inst.busy || !inst.data.length) return false;
          var nextIndex = (inst.current + 1) % inst.data.length;
          Banner3D._go(instanceId, nextIndex);
          return false;
        }, true);
      }
      
      // 自动播放
      if (inst.autoPlay) {
        if (inst.autoTimer) clearInterval(inst.autoTimer);
        inst.autoTimer = setInterval(function() {
          Banner3D._go(instanceId);
        }, inst.autoPlayInterval);
      }
      
      // 窗口大小变化处理
      var resizeTimer;
      window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
          Banner3D._handleResize(instanceId);
        }, 100);
      });
    },
    
    _renderDots: function(instanceId) {
      var inst = this.instances[instanceId];
      if (!inst || !inst.dots || !inst.data.length) return;
      
      inst.dots.innerHTML = '';
      for (var i = 0; i < inst.data.length; i++) {
        var d = document.createElement('span');
        d.className = 'nav-dot' + (i === inst.current ? ' active' : '');
        d.addEventListener('click', function(idx) {
          return function() {
            Banner3D._go(instanceId, idx);
          };
        }(i));
        inst.dots.appendChild(d);
      }
    },
    
    _draw: function(instanceId, index) {
      var inst = this.instances[instanceId];
      if (!inst || !inst.stage || !inst.data.length || !inst.data[index]) return;
      
      inst.stage.innerHTML = '';
      var item = inst.data[index];
      var sw = Math.ceil(inst.w / inst.slices);
      
      for (var i = 0; i < inst.slices; i++) {
        var s = document.createElement('div');
        s.className = 'slice';
        s.style.left = (i * sw) + 'px';
        s.style.width = sw + 'px';
        s.style.background = 'url(' + (item.img || '') + ') center/cover no-repeat';
        s.style.backgroundPosition = (-(i * sw)) + 'px center';
        if (item.link) {
          s.style.cursor = 'pointer';
          s.addEventListener('click', function() {
            window.location.href = item.link;
          });
        }
        inst.stage.appendChild(s);
      }
      
      // 添加标题和内容文本层
      if (item.title || item.content) {
        var textLayer = document.createElement('div');
        textLayer.className = 'banner-text-layer';
        textLayer.style.position = 'absolute';
        textLayer.style.left = '0';
        textLayer.style.top = '0';
        textLayer.style.width = '100%';
        textLayer.style.height = '100%';
        textLayer.style.display = 'flex';
        textLayer.style.flexDirection = 'column';
        textLayer.style.justifyContent = 'center';
        textLayer.style.alignItems = 'center';
        textLayer.style.padding = '40px';
        textLayer.style.zIndex = '2';
        textLayer.style.pointerEvents = 'none';
        
        // 创建文本包装容器（带背景）
        var textWrapper = document.createElement('div');
        textWrapper.className = 'banner-text-wrapper';
        
        if (item.link) {
          textLayer.style.cursor = 'pointer';
          textLayer.style.pointerEvents = 'auto';
          textWrapper.style.cursor = 'pointer';
          textLayer.addEventListener('click', function() {
            window.location.href = item.link;
          });
        }
        
        if (item.title) {
          var titleEl = document.createElement('h2');
          titleEl.className = 'banner-title';
          
          // 移动端截断标题文字
          var titleText = item.title;
          var isMobile = window.innerWidth <= 768;
          if (isMobile) {
            var maxTitleLength = window.innerWidth <= 480 ? 30 : 40;
            if (titleText.length > maxTitleLength) {
              titleText = titleText.substring(0, maxTitleLength) + '...';
            }
          }
          
          titleEl.textContent = titleText;
          titleEl.style.margin = '0 0 16px 0';
          titleEl.style.fontSize = isMobile ? '18px' : '36px';
          titleEl.style.fontWeight = '700';
          titleEl.style.color = '#fff';
          titleEl.style.textShadow = '0 2px 8px rgba(0,0,0,0.3)';
          titleEl.style.textAlign = 'center';
          titleEl.style.lineHeight = '1.3';
          textWrapper.appendChild(titleEl);
        }
        
        if (item.content) {
          var contentEl = document.createElement('div');
          contentEl.className = 'banner-content';
          
          // 移动端截断内容文字
          var contentText = item.content;
          var isMobile = window.innerWidth <= 768;
          if (isMobile) {
            var maxContentLength = window.innerWidth <= 480 ? 50 : 80;
            if (contentText.length > maxContentLength) {
              contentText = contentText.substring(0, maxContentLength) + '...';
            }
          }
          
          contentEl.textContent = contentText;
          contentEl.style.margin = '0';
          contentEl.style.fontSize = isMobile ? '12px' : '18px';
          contentEl.style.fontWeight = '400';
          contentEl.style.color = '#fff';
          contentEl.style.textShadow = '0 2px 6px rgba(0,0,0,0.3)';
          contentEl.style.textAlign = 'center';
          contentEl.style.lineHeight = '1.6';
          contentEl.style.maxWidth = '800px';
          textWrapper.appendChild(contentEl);
        }
        
        textLayer.appendChild(textWrapper);
        inst.stage.appendChild(textLayer);
      }
    },
    
    _go: function(instanceId, next) {
      var inst = this.instances[instanceId];
      if (!inst || inst.busy || !inst.data.length) return;
      
      if (typeof next === 'number') {
        if (next === inst.current) return;
        inst.current = (next + inst.data.length) % inst.data.length;
      } else {
        inst.current = (inst.current + 1) % inst.data.length;
      }
      
      inst.busy = true;
      this._renderDots(instanceId);
      var sw = Math.ceil((inst.stage.clientWidth || inst.w) / inst.slices);
      var children = Array.prototype.slice.call(inst.stage.children);
      
      children.forEach(function(el, idx) {
        el.style.transform = 'perspective(800px) rotateY(0deg)';
        el.style.opacity = '1';
        setTimeout(function() {
          el.style.transition = 'transform .6s ease, opacity .6s ease';
          el.style.transform = 'perspective(800px) rotateY(-90deg)';
          el.style.opacity = '0';
        }, idx * 40);
      });
      
      setTimeout(function() {
        Banner3D._draw(instanceId, inst.current);
        var news = Array.prototype.slice.call(inst.stage.children);
        news.forEach(function(el, idx) {
          el.style.transform = 'perspective(800px) rotateY(90deg)';
          el.style.opacity = '0';
          setTimeout(function() {
            el.style.transition = 'transform .6s ease, opacity .6s ease';
            el.style.transform = 'perspective(800px) rotateY(0deg)';
            el.style.opacity = '1';
          }, idx * 40);
        });
        inst.busy = false;
      }, 40 * inst.slices + 400);
    },
    
    _handleResize: function(instanceId) {
      var inst = this.instances[instanceId];
      if (!inst || !inst.stage || !inst.data.length) return;
      inst.w = inst.stage.clientWidth || document.body.clientWidth || window.innerWidth;
      this._draw(instanceId, inst.current);
    },
    
    destroy: function(instanceId) {
      var inst = this.instances[instanceId];
      if (!inst) return;
      
      if (inst.autoTimer) {
        clearInterval(inst.autoTimer);
        inst.autoTimer = null;
      }
      
      delete this.instances[instanceId];
    }
  };
  
  // 暴露到全局
  window.Banner3D = Banner3D;
})();

