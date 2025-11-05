{{-- 
  3D 百叶窗焦点图脚本组件
  参数:
    $banners: 广告数据数组
    $bannerId: 可选，用于生成唯一的ID，默认为 'banner'
    $autoPlay: 可选，是否自动播放，默认为 true
    $autoPlayInterval: 可选，自动播放间隔（毫秒），默认为 5000
--}}
@php
  $bannerId = $bannerId ?? 'banner';
  $stageId = $bannerId . 'Stage';
  $prevId = $bannerId . 'Prev';
  $nextId = $bannerId . 'Next';
  $dotsId = $bannerId . 'Dots';
  $autoPlay = $autoPlay ?? true;
  $autoPlayInterval = $autoPlayInterval ?? 5000;
@endphp
@if(!empty($banners) && count($banners) > 0)
<script>
  // 3D 百叶窗 Banner（纯原生实现）
  (function(){
    var data = @json($banners ?? []);
    var stage, dots, prevBtn, nextBtn;
    var w, h, slices = 12, current = 0, busy = false, autoTimer = null;
    
    function renderDots(){
      if (!dots || !data.length) return;
      dots.innerHTML = '';
      for (var i = 0; i < data.length; i++) {
        var d = document.createElement('span');
        d.className = 'nav-dot' + (i === current ? ' active' : '');
        d.addEventListener('click', function(idx) {
          return function() { go(idx); };
        }(i));
        dots.appendChild(d);
      }
    }
    
    function draw(index){
      if (!stage || !data.length || !data[index]) return;
      stage.innerHTML = '';
      var item = data[index];
      var sw = Math.ceil(w / slices);
      for (var i = 0; i < slices; i++) {
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
        stage.appendChild(s);
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
          // 根据屏幕宽度动态设置字体大小
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
          // 根据屏幕宽度动态设置字体大小
          contentEl.style.fontSize = isMobile ? '12px' : '18px';
          contentEl.style.fontWeight = '400';
          contentEl.style.color = '#fff';
          contentEl.style.textShadow = '0 2px 6px rgba(0,0,0,0.3)';
          contentEl.style.textAlign = 'center';
          contentEl.style.lineHeight = '1.6';
          contentEl.style.maxWidth = '800px';
          textWrapper.appendChild(contentEl);
        }
        
        // 将文本包装容器添加到文本层
        textLayer.appendChild(textWrapper);
        stage.appendChild(textLayer);
      }
    }
    
    function go(next){
      if (busy || !data.length) return;
      if (typeof next === 'number') {
        if (next === current) return;
        current = (next + data.length) % data.length;
      } else {
        current = (current + 1) % data.length;
      }
      busy = true;
      renderDots();
      var sw = Math.ceil((stage.clientWidth || w) / slices);
      var children = Array.prototype.slice.call(stage.children);
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
        draw(current);
        var news = Array.prototype.slice.call(stage.children);
        news.forEach(function(el, idx) {
          el.style.transform = 'perspective(800px) rotateY(90deg)';
          el.style.opacity = '0';
          setTimeout(function() {
            el.style.transition = 'transform .6s ease, opacity .6s ease';
            el.style.transform = 'perspective(800px) rotateY(0deg)';
            el.style.opacity = '1';
          }, idx * 40);
        });
        busy = false;
      }, 40 * slices + 400);
    }
    
    function init(){
      stage = document.getElementById('{{ $stageId }}');
      dots = document.getElementById('{{ $dotsId }}');
      prevBtn = document.getElementById('{{ $prevId }}');
      nextBtn = document.getElementById('{{ $nextId }}');
      var banner3d = stage ? stage.closest('.banner-3d') : null;
      if (!stage || !data.length) return;
      w = stage.clientWidth || document.body.clientWidth || window.innerWidth;
      h = stage.clientHeight;
      draw(0);
      renderDots();
      
      // 绑定左右切换按钮事件
      if (prevBtn) {
        prevBtn.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          e.stopImmediatePropagation();
          if (busy || !data.length) return false;
          var nextIndex = (current - 1 + data.length) % data.length;
          go(nextIndex);
          return false;
        }, true);
      }
      if (nextBtn) {
        nextBtn.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          e.stopImmediatePropagation();
          if (busy || !data.length) return false;
          var nextIndex = (current + 1) % data.length;
          go(nextIndex);
          return false;
        }, true);
      }
      
      // 自动播放
      @if($autoPlay)
      if (autoTimer) clearInterval(autoTimer);
      autoTimer = setInterval(function() { go(); }, {{ $autoPlayInterval }});
      @endif
    }
    
    function handleResize(){
      if (!stage || !data.length) return;
      w = stage.clientWidth || document.body.clientWidth || window.innerWidth;
      // 重新绘制以更新字体大小
      draw(current);
    }
    
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', init);
    } else {
      setTimeout(init, 100);
    }
    window.addEventListener('resize', handleResize);
  })();
</script>
@endif

