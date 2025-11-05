{{-- 
  Banner 广告组件
  参数:
    $banner: 广告数据数组 ['img' => '', 'link' => '', 'title' => '']，如果为null则组件不输出任何内容
    $cssClass: CSS类名（如: 'member-banner', 'category-banner'等）
--}}
@if(isset($banner) && $banner)
    <div class="banner full-bleed">
        <div class="container-fluid px-0">
            <div class="{{ $cssClass ?? 'banner-container' }}">
              @if(!empty($banner['link']))
                <a href="{{ $banner['link'] }}" class="banner-link" target="_blank" rel="noopener noreferrer">
                  <img src="{{ $banner['img'] }}" alt="{{ $banner['title'] ?? '' }}" class="banner-image">
                </a>
              @else
                <img src="{{ $banner['img'] }}" alt="{{ $banner['title'] ?? '' }}" class="banner-image">
              @endif
            </div>
        </div>
    </div>
@endif
