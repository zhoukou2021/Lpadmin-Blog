@extends('blog.layouts.main')

@section('banner')
  @include('blog.components.banner-3d', ['banners' => $banners ?? [], 'bannerId' => 'contactBanner'])
@endsection

@section('content')
  <div class="row layout-main">
    <div class="col-lg-9 mb-3">
      <div class="card">
        <div class="card-hd">
          <h2 class="contact-title" style="margin:0;font-size:24px;font-weight:700;color:var(--primary);line-height:1.5;word-break:break-word">
            {{ __('blog.contact') }}
          </h2>
        </div>
        <div class="card-bd">
          <div class="contact-content" style="padding:20px 0;font-size:15px;line-height:1.8;color:var(--ink)">
            @if(!empty($contactContent))
              {!! $contactContent !!}
            @else
              <div class="muted" style="padding:40px;text-align:center;">{{ __('blog.no_contact_info') ?? '暂无联系方式信息' }}</div>
            @endif
          </div>
        </div>
      </div>
    </div>
    @include('blog.components.sidebar')
  </div>
@endsection

@section('head')
  @include('blog.components.banner-3d-styles')
  @include('blog.components.banner-3d-script', ['banners' => $banners ?? [], 'bannerId' => 'contactBanner'])
  <style>
  /* 联系方式页面样式 */
  .contact-title{margin:0 0 20px;font-size:24px;font-weight:700;color:var(--primary);line-height:1.5;word-break:break-word;border-bottom:1px solid var(--primary);padding-bottom:15px}
  .contact-content{padding:20px 0;font-size:15px;line-height:1.8;color:var(--ink);word-wrap:break-word;white-space:pre-wrap}
  .contact-content p{margin:12px 0}
  .contact-content h1,.contact-content h2,.contact-content h3,.contact-content h4,.contact-content h5,.contact-content h6{margin:20px 0 12px;font-weight:700;color:var(--ink)}
  .contact-content h1{font-size:22px}
  .contact-content h2{font-size:20px}
  .contact-content h3{font-size:18px}
  .contact-content h4{font-size:16px}
  .contact-content ul,.contact-content ol{margin:12px 0;padding-left:24px}
  .contact-content li{margin:6px 0}
  .contact-content a{color:var(--primary);text-decoration:none}
  .contact-content a:hover{text-decoration:underline}

  @media (max-width: 767.98px){
    .contact-title{font-size:20px;margin-bottom:16px;padding-bottom:12px}
    .contact-content{padding:16px 0;font-size:14px;line-height:1.7}
    .contact-content h1{font-size:20px}
    .contact-content h2{font-size:18px}
    .contact-content h3{font-size:16px}
    .contact-content h4{font-size:15px}
  }
  @media (max-width: 575.98px){
    .contact-title{font-size:18px}
    .contact-content{font-size:13px}
  }
</style>
@endsection

