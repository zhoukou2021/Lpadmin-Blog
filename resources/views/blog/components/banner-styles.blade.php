{{-- 
  Banner 样式组件（输出完整的style标签和CSS代码）
  参数:
    $cssClass: CSS类名（如: 'member-banner', 'category-banner'等）
--}}
@php
  $bannerClass = $cssClass ?? 'banner-container';
  $isMemberBanner = ($bannerClass === 'member-banner');
@endphp
<style>
  .{{ $bannerClass }} {
    position: relative;
    overflow: hidden;
    height: 300px;
  }
  .{{ $bannerClass }} .banner-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .{{ $bannerClass }} .banner-link {
    display: block;
    width: 100%;
    height: 100%;
  }
 
  @media (max-width: 767.98px) {
    /* 移动端banner容器全宽显示 */
    .banner.full-bleed {
      margin-left: calc(-1 * (100vw - 100%) / 2);
      margin-right: calc(-1 * (100vw - 100%) / 2);
      width: 100vw;
      max-width: 100vw;
      max-height: 100px;
    }
    .banner.full-bleed .container-fluid {
      padding-left: 0 !important;
      padding-right: 0 !important;
      margin-left: 0;
      margin-right: 0;
    }
    .{{ $bannerClass }} {
      height: 180px;
      border-radius: 0;
      margin-left: 0;
      margin-right: 0;
      width: 100%;
      max-width: 100%;
      max-height: 100px;
    }
  }
</style>

