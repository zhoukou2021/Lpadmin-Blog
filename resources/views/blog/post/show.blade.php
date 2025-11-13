@extends('blog.layouts.main')

@section('banner')
  @include('blog.components.banner', ['banner' => $postBanner ?? null, 'cssClass' => 'post-banner'])
@endsection

@section('content')

  <div class="row layout-main">
    <div class="col-lg-9 mb-3">
      <div class="card">
        <article class="post-detail">
          <!-- 文章头部 -->
          <div class="post-header">
            <h1 class="post-title-main">{{ $postTitle }}</h1>
            <div class="post-meta-header">
              @if(!empty($categoryName))
                <a href="{{ blog_url($categorySlug) }}" class="post-category">
                  <i class="bi bi-folder"></i> {{ $categoryName }}
                </a>
              @endif
              <span class="post-meta-item">
                <i class="bi bi-calendar3"></i> {{ $post->published_at ? $post->published_at->format('Y-m-d H:i') : '' }}
              </span>
              <span class="post-meta-item">
                <i class="bi bi-eye"></i> {{ $post->view_count ?? 0 }}
              </span>
              <span class="post-meta-item">
                <i class="bi bi-chat-dots"></i> {{ $post->comments_count ?? 0 }}
              </span>
              <span class="post-meta-item post-like-item" data-post-id="{{ $post->id }}">
                <i class="bi bi-hand-thumbs-up{{ $isLiked ?? false ? '-fill' : '' }}"></i> <span class="likes-count">{{ $post->likes_count ?? 0 }}</span>
              </span>
              <span class="post-meta-item post-favorite-item" data-post-id="{{ $post->id }}">
                <i class="bi bi-star{{ $isFavorited ?? false ? '-fill' : '' }}"></i> <span class="favorites-count">{{ $post->favorites_count ?? 0 }}</span>
              </span>
            </div>
            @if(!empty($postTags) && count($postTags) > 0)
              <div class="post-tags-header">
                @foreach($postTags as $tag)
                  <a href="{{ blog_url($tag['slug']) }}" class="post-tag">#{{ $tag['name'] }}</a>
                @endforeach
              </div>
            @endif
          </div>

          <!-- 文章封面 -->
          @if(!empty($post->cover))
            <div class="post-cover-main">
              <img src="{{ $post->cover }}" alt="{{ $postTitle }}">
            </div>
          @endif

          <!-- 文章内容 -->
          <div class="post-body">
            {!! $postContent !!}
          </div>
        </article>
      </div>

      <!-- 上一篇/下一篇导航 -->
      <div class="post-nav">
        <div class="post-nav-item post-nav-prev">
          @if($prevPost)
            <a href="{{ blog_url($prevPost['slug']) }}" class="post-nav-link">
              <div class="post-nav-icon">
                <i class="bi bi-chevron-left"></i>
              </div>
              <div class="post-nav-content">
                <div class="post-nav-label">{{ __('blog.prev_post') }}</div>
                <div class="post-nav-title">{{ $prevPost['title'] }}</div>
              </div>
            </a>
          @else
            <div class="post-nav-link post-nav-empty">
              <div class="post-nav-icon">
                <i class="bi bi-chevron-left"></i>
              </div>
              <div class="post-nav-content">
                <div class="post-nav-label">{{ __('blog.prev_post') }}</div>
                <div class="post-nav-title muted">{{ __('blog.no_more_posts') }}</div>
              </div>
            </div>
          @endif
        </div>
        <div class="post-nav-item post-nav-next">
          @if($nextPost)
            <a href="{{ blog_url($nextPost['slug']) }}" class="post-nav-link">
              <div class="post-nav-content">
                <div class="post-nav-label">{{ __('blog.next_post') }}</div>
                <div class="post-nav-title">{{ $nextPost['title'] }}</div>
              </div>
              <div class="post-nav-icon">
                <i class="bi bi-chevron-right"></i>
              </div>
            </a>
          @else
            <div class="post-nav-link post-nav-empty">
              <div class="post-nav-content">
                <div class="post-nav-label">{{ __('blog.next_post') }}</div>
                <div class="post-nav-title muted">{{ __('blog.no_more_posts') }}</div>
              </div>
              <div class="post-nav-icon">
                <i class="bi bi-chevron-right"></i>
              </div>
            </div>
          @endif
        </div>
      </div>

      <!-- 相关文章 -->
      @if($relatedPosts->count() > 0)
        <div class="card related-posts-card">
          <div class="card-hd">{{ __('blog.related_posts') }}</div>
          <div class="card-bd">
            <div class="related-posts-list">
              @foreach($relatedPosts as $rp)
                <div class="related-post-item">
                  @if(!empty($rp['cover']))
                    <div class="related-post-cover">
                      <a href="{{ blog_url($rp['slug']) }}"><img src="{{ $rp['cover'] }}" alt="{{ $rp['title'] }}"></a>
                    </div>
                  @endif
                  <div class="related-post-content">
                    <div class="related-post-title"><a href="{{ blog_url($rp['slug']) }}">{{ $rp['title'] }}</a></div>
                    @if(!empty($rp['summary']))
                      <div class="related-post-summary">{{ $rp['summary'] }}</div>
                    @endif
                    <div class="related-post-meta">
                      <span class="post-meta-item">
                        <i class="bi bi-calendar3"></i> {{ $rp['published_at'] ? \Carbon\Carbon::parse($rp['published_at'])->format('Y-m-d') : '' }}
                      </span>
                      <span class="post-meta-item">
                        <i class="bi bi-eye"></i> {{ $rp['view_count'] ?? 0 }}
                      </span>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      @endif

      <!-- 评论区域 -->
      <div class="card comments-card">
        <div class="card-hd">
          <h3>
            <i class="bi bi-chat-dots"></i> 
            {{ __('blog.comments') }} 
            <span class="muted comment-count">({{ $post->comments_count ?? 0 }})</span>
          </h3>
        </div>
        <div class="card-bd">
          <!-- 评论表单 -->
          <div class="comment-form-wrapper">
            @auth('web')
            <form id="commentForm" class="comment-form" action="{{ route('site.comment.store', ['post' => $post->id]) }}" method="POST">
              @csrf
              <input type="hidden" name="parent_id" id="commentParentId" value="0">
              <div class="comment-form-group">
                <textarea name="content" id="commentContent" class="comment-textarea" rows="4" placeholder="{{ __('blog.comment_placeholder') }}" required></textarea>
              </div>
              <div class="comment-form-footer">
                <div class="comment-form-tip muted">
                  <span id="commentReplyTo" style="display:none"><span id="replyToPrefix"></span><span id="replyToName"></span></span>
                  <button type="button" id="cancelReply" class="btn-cancel-reply">{{ __('blog.cancel_reply') }}</button>
                </div>
                <button type="submit" class="btn-submit-comment">{{ __('blog.submit_comment') }}</button>
              </div>
            </form>
            @else
            <div class="comment-login-prompt">
              <p>
                <i class="bi bi-lock"></i>
                {{ __('blog.comment_login_required') ?? '请先登录后再发布评论' }}
              </p>
              <div class="comment-login-buttons">
                <a href="javascript:;" onclick="if(typeof openAuthModal==='function'){openAuthModal('login');return false;}" class="comment-login-btn comment-login-btn-primary">
                  {{ __('blog.login') }}
                </a>
                <a href="javascript:;" onclick="if(typeof openAuthModal==='function'){openAuthModal('register');return false;}" class="comment-login-btn comment-login-btn-secondary">
                  {{ __('blog.register') }}
                </a>
              </div>
            </div>
            @endauth
          </div>

          <!-- 评论列表 -->
          <div class="comments-list">
            @if(count($comments) > 0)
              @foreach($comments as $comment)
                @include('blog.post.comment-item', ['comment' => $comment, 'depth' => 0])
              @endforeach
            @else
              <div class="comments-empty muted">
                <i class="bi bi-chat-dots"></i>
                <div>{{ __('blog.no_comments') }}</div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    @include('blog.components.sidebar')
  </div>
@endsection

@section('head')
  @include('blog.components.banner-styles', ['cssClass' => 'post-banner'])
  <link rel="stylesheet" href="/static/blog/css/post-detail.css" />
  <script src="/static/blog/js/post-detail.js"></script>
<script>
    window.PostDetailConfig = {
      commentStoreUrl: '{{ route("site.comment.store", ["post" => $post->id]) }}',
      likeUrlTemplate: '{{ route("site.post.like", ["post" => ":id"]) }}',
      favoriteUrlTemplate: '{{ route("site.post.favorite", ["post" => ":id"]) }}',
      texts: {
      commentRequired: @json(__('blog.comment_required')),
      commentMinLength: @json(__('blog.comment_min_length')),
      commentMaxLength: @json(__('blog.comment_max_length')),
      submitting: @json(__('blog.submitting')),
      submitComment: @json(__('blog.submit_comment')),
      commentSuccess: @json(__('blog.comment_success')),
      commentFailed: @json(__('blog.comment_failed')),
      commentLoginRequired: @json(__('blog.comment_login_required')),
      commentSensitiveWord: @json(__('blog.comment_sensitive_word')),
      replyTo: @json(__('blog.reply_to'))
      }
    };
</script>
@endsection
