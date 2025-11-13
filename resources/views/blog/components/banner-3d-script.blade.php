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
<script src="/static/blog/js/banner-3d.js"></script>
<script>
  // 初始化 3D 百叶窗焦点图
  (function() {
    function initBanner3D() {
      if (typeof window.Banner3D === 'undefined') {
        // 如果脚本还未加载，等待一下
        setTimeout(initBanner3D, 50);
        return;
      }
      
      window.Banner3D.init({
        stageId: '{{ $stageId }}',
        dotsId: '{{ $dotsId }}',
        prevId: '{{ $prevId }}',
        nextId: '{{ $nextId }}',
        banners: @json($banners ?? []),
        autoPlay: {{ $autoPlay ? 'true' : 'false' }},
        autoPlayInterval: {{ $autoPlayInterval }}
      });
    }
    
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initBanner3D);
    } else {
      setTimeout(initBanner3D, 100);
    }
  })();
</script>
@endif

