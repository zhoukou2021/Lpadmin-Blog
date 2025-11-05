{{-- 
  3D 百叶窗焦点图组件
  参数:
    $banners: 广告数据数组，格式: [['img' => '', 'link' => '', 'title' => '', 'content' => ''], ...]
    $bannerId: 可选，用于生成唯一的ID（避免多个焦点图在同一页面时ID冲突），默认为 'banner'
--}}
@php
  $bannerId = $bannerId ?? 'banner';
  $stageId = $bannerId . 'Stage';
  $prevId = $bannerId . 'Prev';
  $nextId = $bannerId . 'Next';
  $dotsId = $bannerId . 'Dots';
@endphp
@if(!empty($banners) && count($banners) > 0)
  <div class="banner full-bleed">
      <div class="container-fluid px-0">
          <div class="banner-3d">
          <div class="stage" id="{{ $stageId }}"></div>
          <div class="arrow arrow-left" id="{{ $prevId }}">
            <i class="bi bi-chevron-left"></i>
          </div>
          <div class="arrow arrow-right" id="{{ $nextId }}">
            <i class="bi bi-chevron-right"></i>
          </div>
          <div id="{{ $dotsId }}" class="banner-dots"></div>
          </div>
      </div>
  </div>
@endif

