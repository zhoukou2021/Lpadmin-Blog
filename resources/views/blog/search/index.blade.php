@extends('blog.layouts.main')

@section('content')
  <div class="row layout-main">
    <div class="col-lg-9 mb-3">
      <div class="card search-card">
        <div class="card-hd">
          <div class="search-header">
            <h1 class="search-title">{{ __('blog.search_results') }}</h1>
            @if($keyword !== '')
              <p class="search-summary">
                <span class="search-highlight">“{{ e($keyword) }}”</span>
                {{ __('blog.search_total', ['count' => $searchCount]) }}
              </p>
            @else
              <p class="search-summary">{{ __('blog.search_empty_query') }}</p>
            @endif
          </div>
        </div>
        <div class="card-bd">
          @if($keyword === '')
            <div class="search-empty">
              <i class="bi bi-search"></i>
              <p>{{ __('blog.search_empty_query') }}</p>
            </div>
          @else
            @if($searchCount > 0)
              <div class="posts-container view-list">
                @foreach($posts as $post)
                  <div class="post-card">
                    @if(!empty($post['cover']))
                      <div class="post-cover">
                        <a href="{{ blog_url($post['slug']) }}"><img src="{{ $post['cover'] }}" alt="{{ $post['title'] }}"></a>
                      </div>
                    @endif
                    <div class="post-content">
                      <div class="post-title"><a href="{{ blog_url($post['slug']) }}">{{ $post['title'] }}</a></div>
                      @if(!empty($post['summary']))
                        <div class="post-summary">{{ $post['summary'] }}</div>
                      @endif
                      <div class="post-meta">
                        @if(!empty($post['category_name']))
                          <a href="{{ blog_url($post['category_slug']) }}" class="post-category">
                            <i class="bi bi-folder"></i> {{ $post['category_name'] }}
                          </a>
                        @endif
                        <span class="post-meta-item">
                          <i class="bi bi-calendar3"></i> {{ $post['published_at'] ? \Carbon\Carbon::parse($post['published_at'])->format('Y-m-d') : '' }}
                        </span>
                        <span class="post-meta-item">
                          <i class="bi bi-eye"></i> {{ $post['view_count'] ?? 0 }}
                        </span>
                        <span class="post-meta-item">
                          <i class="bi bi-chat-dots"></i> {{ $post['comments_count'] ?? 0 }}
                        </span>
                      </div>
                      @if(!empty($post['tags']) && count($post['tags']) > 0)
                        <div class="post-tags">
                          @foreach($post['tags'] as $tag)
                            <a href="{{ blog_url($tag['slug']) }}" class="post-tag">#{{ $tag['name'] }}</a>
                          @endforeach
                        </div>
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
              @if($postsPaginator && $postsPaginator->hasPages())
                <div class="search-pagination">
                  {{ $postsPaginator->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
              @endif
            @else
              <div class="search-empty">
                <i class="bi bi-search"></i>
                <p>{{ __('blog.search_no_results', ['keyword' => $keyword]) }}</p>
                <p class="search-tip">{{ __('blog.search_try_different') }}</p>
              </div>
            @endif
          @endif
        </div>
      </div>
    </div>

    @include('blog.components.sidebar')
  </div>
@endsection

@section('head')
  <link rel="stylesheet" href="/static/blog/css/search.css" />
@endsection
