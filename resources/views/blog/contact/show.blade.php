@extends('blog.layouts.main')

@section('banner')
  @include('blog.components.banner-3d', ['banners' => $banners ?? [], 'bannerId' => 'contactBanner'])
@endsection

@section('content')
  <div class="row layout-main">
    <div class="col-lg-9 mb-3">
      <div class="card">
        <div class="card-hd">
          <h2 class="contact-title">
            {{ __('blog.contact') }}
          </h2>
        </div>
        <div class="card-bd">
          <div class="contact-content">
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
  <link rel="stylesheet" href="/static/blog/css/contact.css" />
@endsection

