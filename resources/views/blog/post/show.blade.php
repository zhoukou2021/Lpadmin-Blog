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
              <span class="post-meta-item post-like-item" data-post-id="{{ $post->id }}" style="cursor:pointer;user-select:none;">
                <i class="bi bi-hand-thumbs-up{{ $isLiked ?? false ? '-fill' : '' }}"></i> <span class="likes-count">{{ $post->likes_count ?? 0 }}</span>
              </span>
              <span class="post-meta-item post-favorite-item" data-post-id="{{ $post->id }}" style="cursor:pointer;user-select:none;">
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
        <div class="card" style="margin-top:20px">
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
      <div class="card" style="margin-top:20px">
        <div class="card-hd">
          <h3 style="margin:0;font-size:18px;font-weight:700">
            <i class="bi bi-chat-dots"></i> 
            {{ __('blog.comments') }} 
            <span class="muted" style="font-size:14px;font-weight:400">({{ $post->comments_count ?? 0 }})</span>
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
                <div class="comment-form-tip muted" style="font-size:12px;color:#999">
                  <span id="commentReplyTo" style="display:none;margin-right:8px"><span id="replyToPrefix"></span><span id="replyToName"></span></span>
                  <button type="button" id="cancelReply" class="btn-cancel-reply" style="display:none;background:none;border:none;color:var(--primary);cursor:pointer;font-size:12px">{{ __('blog.cancel_reply') }}</button>
                </div>
                <button type="submit" class="btn-submit-comment">{{ __('blog.submit_comment') }}</button>
              </div>
            </form>
            @else
            <div class="comment-login-prompt" style="padding:30px;text-align:center;background:var(--primary-weak);border-radius:8px;margin-bottom:20px">
              <p style="margin:0 0 16px;color:var(--ink);font-size:15px">
                <i class="bi bi-lock" style="margin-right:6px;color:var(--primary)"></i>
                {{ __('blog.comment_login_required') ?? '请先登录后再发布评论' }}
              </p>
              <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap">
                <a href="javascript:;" onclick="if(typeof openAuthModal==='function'){openAuthModal('login');return false;}" style="padding:8px 20px;background:var(--primary);color:#fff;border-radius:6px;text-decoration:none;font-size:14px;display:inline-block;transition:all .2s">
                  {{ __('blog.login') }}
                </a>
                <a href="javascript:;" onclick="if(typeof openAuthModal==='function'){openAuthModal('register');return false;}" style="padding:8px 20px;background:#fff;color:var(--primary);border:1px solid var(--primary);border-radius:6px;text-decoration:none;font-size:14px;display:inline-block;transition:all .2s">
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
              <div class="comments-empty muted" style="padding:40px;text-align:center;color:#999">
                <i class="bi bi-chat-dots" style="font-size:32px;margin-bottom:12px;display:block;opacity:0.5"></i>
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
<style>

  /* 文章详情样式 */
  .post-detail{padding:20px 18px}
  .post-header{border-bottom:1px solid var(--primary);padding-bottom:15px;margin-bottom:20px}
  .post-title-main{margin:0 0 12px;font-size:24px;font-weight:700;color:var(--primary);line-height:1.5;word-break:break-word}
  .post-meta-header{display:flex;flex-wrap:wrap;gap:12px;align-items:center;margin-bottom:12px;font-size:13px}
  .post-tags-header{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px}
  .post-cover-main{margin:0 0 20px;border-radius:10px;overflow:hidden;background:#f0f0f0}
  .post-cover-main img{width:100%;height:auto;display:block}
  .post-body{font-size:15px;line-height:1.8;color:var(--ink)}
  .post-body p{margin:12px 0}
  .post-body h1,.post-body h2,.post-body h3,.post-body h4,.post-body h5,.post-body h6{margin:20px 0 12px;font-weight:700;color:var(--ink)}
  .post-body h1{font-size:22px}
  .post-body h2{font-size:20px}
  .post-body h3{font-size:18px}
  .post-body h4{font-size:16px}
  .post-body img{max-width:100%;height:auto;border-radius:8px;margin:16px 0;display:block}
  .post-body ul,.post-body ol{margin:12px 0;padding-left:24px}
  .post-body li{margin:6px 0}
  .post-body blockquote{border-left:4px solid var(--primary);padding-left:16px;margin:16px 0;color:#666;font-style:italic}
  .post-body code{background:var(--primary-weak);padding:2px 6px;border-radius:4px;font-size:14px;color:var(--primary);font-family:monospace}
  .post-body pre{background:#f5f5f5;padding:12px;border-radius:8px;overflow-x:auto;margin:16px 0}
  .post-body pre code{background:none;padding:0;color:var(--ink)}
  .post-body a{color:var(--primary);text-decoration:none}
  .post-body a:hover{text-decoration:underline}
  .post-body table{width:100%;border-collapse:collapse;margin:16px 0}
  .post-body th,.post-body td{border:1px solid #e6eee6;padding:8px 12px;text-align:left}
  .post-body th{background:var(--primary-weak);font-weight:700}

  @media (max-width: 767.98px){
    .post-detail{padding:16px 14px}
    .post-title-main{font-size:20px;margin-bottom:10px}
    .post-meta-header{font-size:12px;gap:10px;margin-bottom:10px}
    .post-header{padding-bottom:12px;margin-bottom:16px}
    .post-body{font-size:14px;line-height:1.7}
    .post-body h1{font-size:20px}
    .post-body h2{font-size:18px}
    .post-body h3{font-size:16px}
    .post-body h4{font-size:15px}
  }
  @media (max-width: 575.98px){
    .post-detail{padding:12px 10px}
    .post-title-main{font-size:18px}
    .post-meta-header{font-size:11px;gap:8px}
    .post-body{font-size:13px}
  }

  /* 上一篇/下一篇导航 */
  .post-nav{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:20px}
  .post-nav-item{background:#fff;border:1px solid #e6eee6;border-radius:10px;overflow:hidden;transition:all .2s}
  .post-nav-item:hover{border-color:var(--primary);box-shadow:0 4px 12px rgba(44,163,106,.1)}
  .post-nav-link{display:flex;align-items:center;gap:12px;padding:16px;text-decoration:none;color:var(--ink);height:100%}
  .post-nav-link.post-nav-empty{color:#999;cursor:not-allowed}
  .post-nav-prev .post-nav-link{flex-direction:row}
  .post-nav-next .post-nav-link{flex-direction:row-reverse;text-align:right}
  .post-nav-icon{flex-shrink:0;width:40px;height:40px;border-radius:50%;background:var(--primary-weak);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:20px;transition:all .2s}
  .post-nav-link:hover .post-nav-icon{background:var(--primary);color:#fff;transform:scale(1.1)}
  .post-nav-content{flex:1;min-width:0}
  .post-nav-label{font-size:12px;color:#999;margin-bottom:4px}
  .post-nav-title{font-size:14px;font-weight:600;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;color:var(--ink)}
  .post-nav-link:hover .post-nav-title{color:var(--primary)}
  .post-nav-empty .post-nav-title{color:#999;font-weight:400}
  
  @media (max-width: 767.98px){
    .post-nav{grid-template-columns:1fr;gap:12px;margin-top:16px}
    .post-nav-link{padding:12px}
    .post-nav-icon{width:36px;height:36px;font-size:18px}
    .post-nav-title{font-size:13px}
    .post-nav-next .post-nav-link{flex-direction:row;text-align:left}
  }

  /* 相关文章样式 */
  .related-posts-list{display:flex;flex-direction:column;gap:12px}
  .related-post-item{display:flex;gap:12px;padding:12px 0;border-bottom:1px dashed #e6eee6}
  .related-post-item:last-child{border-bottom:none}
  .related-post-cover{flex-shrink:0;width:100px;height:70px;border-radius:8px;overflow:hidden;background:#f0f0f0}
  .related-post-cover img{width:100%;height:100%;object-fit:cover}
  .related-post-content{flex:1;min-width:0}
  .related-post-title{margin:0 0 6px;font-size:15px;line-height:1.4}
  .related-post-title a{color:var(--ink);font-weight:600}
  .related-post-title a:hover{color:var(--primary)}
  .related-post-summary{font-size:13px;color:#666;margin:4px 0;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
  .related-post-meta{display:flex;gap:12px;margin-top:6px;font-size:12px}
  
  @media (max-width: 767.98px){
    .related-post-item{flex-direction:column;gap:8px;padding:10px 0}
    .related-post-cover{width:100%;height:160px}
    .related-post-title{font-size:14px}
    .related-post-summary{font-size:12px}
    .related-post-meta{font-size:11px;gap:10px}
  }

  /* 卡片头部布局调整 */
  .card-hd{padding:16px 18px}
  @media (max-width: 767.98px){
    .card-hd{padding:12px 14px}
  }
  
  /* 移动端卡片内容优化 */
  @media (max-width: 767.98px){
    .card-bd{padding:10px 12px}
    .post-title{font-size:15px;margin-bottom:6px}
    .post-summary{font-size:12px;margin:4px 0;line-height:1.5}
    .post-meta{font-size:11px;gap:8px;margin-top:6px;flex-wrap:wrap}
    .post-meta-item i{font-size:12px}
    .post-tags{margin-top:4px;gap:3px}
    .post-tag{font-size:10px;padding:2px 6px}
  }
  
  /* 侧边栏移动端优化 */
  @media (max-width: 991.98px){
    .col-lg-3{margin-top:16px}
    .col-lg-3 .card{margin-bottom:12px}
    .col-lg-3 .card-hd{font-size:15px;padding:10px 12px}
    .col-lg-3 .card-bd{padding:10px 12px}
    .col-lg-3 .post-card{padding:8px 0}
    .col-lg-3 .post-title{font-size:13px;margin-bottom:4px}
    .col-lg-3 .muted{font-size:12px;margin:0 4px 4px 0}
  }
  
  /* 主布局移动端优化 */
  @media (max-width: 767.98px){
    .layout-main .col-lg-9{margin-bottom:0}
    .layout-main .card{border-radius:10px;margin-bottom:12px}
    .main.container{padding-left:12px;padding-right:12px}
  }

  /* 评论区域样式 */
  .comment-form-wrapper{margin-bottom:30px;padding-bottom:20px;border-bottom:1px solid #e6eee6}
  .comment-form-group{margin-bottom:12px}
  .comment-textarea{width:100%;padding:12px;border:1px solid #e6eee6;border-radius:8px;font-size:14px;line-height:1.6;resize:vertical;font-family:inherit;box-sizing:border-box;transition:border-color .2s}
  .comment-textarea:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(44,163,106,.1)}
  .comment-form-footer{display:flex;justify-content:space-between;align-items:center}
  .comment-form-tip{display:flex;align-items:center}
  .btn-submit-comment{padding:8px 20px;background:var(--primary);color:#fff;border:none;border-radius:6px;font-size:14px;cursor:pointer;transition:all .2s;font-weight:500}
  .btn-submit-comment:hover{background:var(--primary-dark);transform:translateY(-1px);box-shadow:0 4px 8px rgba(44,163,106,.2)}
  .btn-submit-comment:active{transform:translateY(0)}
  .btn-submit-comment:disabled{background:#ccc;cursor:not-allowed;transform:none}

  /* 评论列表样式 */
  .comments-list{margin-top:20px}
  .comment-item{display:flex;gap:12px;padding:16px 0;border-bottom:1px solid #f0f0f0}
  .comment-item:last-child{border-bottom:none}
  .comment-avatar{flex-shrink:0;width:40px;height:40px;border-radius:50%;overflow:hidden;background:#f0f0f0}
  .comment-avatar img{width:100%;height:100%;object-fit:cover}
  .comment-avatar-default{width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#999;font-size:32px}
  .comment-content{flex:1;min-width:0}
  .comment-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px}
  .comment-author{font-weight:600;color:var(--ink);font-size:14px}
  .comment-time{font-size:12px;color:#999}
  .comment-text{font-size:14px;line-height:1.6;color:var(--ink);word-wrap:break-word;white-space:pre-wrap;margin-bottom:8px}
  .comment-actions{margin-top:8px}
  .btn-reply-comment{background:none;border:none;color:var(--primary);font-size:12px;cursor:pointer;padding:4px 8px;border-radius:4px;transition:all .2s;display:inline-flex;align-items:center;gap:4px}
  .btn-reply-comment:hover{background:var(--primary-weak);color:var(--primary)}
  .btn-reply-comment i{font-size:14px}
  .comment-children{margin-top:16px;padding-left:20px;border-left:2px solid #e6eee6}
  .comment-children .comment-item{padding:12px 0}
  .comment-children .comment-avatar{width:32px;height:32px}
  .comment-children .comment-author{font-size:13px}
  .comment-children .comment-text{font-size:13px}
  .comments-empty{padding:40px 20px;text-align:center}

  @media (max-width: 767.98px){
    .comment-form-wrapper{margin-bottom:20px;padding-bottom:16px}
    .comment-textarea{padding:10px;font-size:14px}
    .comment-form-footer{flex-direction:column;align-items:stretch;gap:8px}
    .comment-form-tip{flex-wrap:wrap}
    .btn-submit-comment{width:100%;padding:10px 20px}
    .comment-item{padding:12px 0;gap:10px}
    .comment-avatar{width:36px;height:36px}
    .comment-author{font-size:13px}
    .comment-time{font-size:11px}
    .comment-text{font-size:13px;line-height:1.5}
    .comment-children{padding-left:12px}
    .comment-children .comment-avatar{width:28px;height:28px}
  .comment-children .comment-text{font-size:12px}
  .comments-empty{padding:30px 15px}
  }
  
  /* 点赞按钮样式 */
  .post-like-item{transition:all .2s;display:inline-flex;align-items:center;gap:4px}
  .post-like-item:hover{color:var(--primary);transform:scale(1.1)}
  .post-like-item i{font-size:14px;transition:transform .2s}
  .post-like-item:hover i{transform:scale(1.2)}
  .post-like-item[data-loading="true"]{opacity:0.6;pointer-events:none}
  
  /* 收藏按钮样式 */
  .post-favorite-item{transition:all .2s;display:inline-flex;align-items:center;gap:4px}
  .post-favorite-item:hover{color:#ffc107;transform:scale(1.1)}
  .post-favorite-item i{font-size:14px;transition:transform .2s}
  .post-favorite-item:hover i{transform:scale(1.2)}
  .post-favorite-item[data-loading="true"]{opacity:0.6;pointer-events:none}
</style>
<script>
  (function(){
    // 翻译文本
    var translations = {
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
    };

    function initCommentForm(){
      var commentForm = document.getElementById('commentForm');
      if (!commentForm) {
        console.warn('评论表单未找到');
        return;
      }
      
      var commentContent = document.getElementById('commentContent');
      var commentParentId = document.getElementById('commentParentId');
      var commentReplyTo = document.getElementById('commentReplyTo');
      var replyToName = document.getElementById('replyToName');
      var replyToPrefix = document.getElementById('replyToPrefix');
      var cancelReply = document.getElementById('cancelReply');
      var submitBtn = commentForm.querySelector('button[type="submit"]');

      // 初始化回复按钮
      function initReplyButtons(){
        var replyButtons = document.querySelectorAll('.btn-reply-comment');
        replyButtons.forEach(function(btn){
          btn.addEventListener('click', function(e){
            e.preventDefault();
            var commentId = this.getAttribute('data-comment-id');
            var commentName = this.getAttribute('data-comment-name');
            if (commentParentId) commentParentId.value = commentId;
            if (replyToName) replyToName.textContent = commentName;
            if (replyToPrefix && translations.replyTo) {
              // 替换 :name 为实际名称
              var replyText = translations.replyTo.replace(':name', commentName);
              replyToPrefix.textContent = replyText;
            }
            if (commentReplyTo) commentReplyTo.style.display = 'inline';
            if (cancelReply) cancelReply.style.display = 'inline';
            if (commentContent) commentContent.focus();
            // 滚动到评论表单
            commentForm.scrollIntoView({behavior: 'smooth', block: 'nearest'});
          });
        });
      }

      // 取消回复
      if (cancelReply) {
        cancelReply.addEventListener('click', function(e){
          e.preventDefault();
          if (commentParentId) commentParentId.value = '0';
          if (commentReplyTo) commentReplyTo.style.display = 'none';
          cancelReply.style.display = 'none';
          if (commentContent) commentContent.value = '';
        });
      }

      // 提交评论
      commentForm.addEventListener('submit', function(e){
        e.preventDefault();
        e.stopPropagation();
        
        // 检查是否已登录（如果 auth 可用）
        @guest('web')
        if (typeof openAuthModal === 'function') {
          openAuthModal('login');
          return false;
        }
        @endguest
        
        if (!commentContent || !commentContent.value.trim()) {
          alert(translations.commentRequired);
          return false;
        }

        if (commentContent.value.length < 3) {
          alert(translations.commentMinLength);
          return false;
        }

        if (commentContent.value.length > 2000) {
          alert(translations.commentMaxLength);
          return false;
        }

        // 禁用提交按钮
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.textContent = translations.submitting;
        }

        // 获取提交URL（从表单action属性或使用route helper）
        var url = commentForm.getAttribute('action') || "{{ route('site.comment.store', ['post' => $post->id]) }}";
        var formData = new FormData(commentForm);
        var token = document.querySelector('meta[name="csrf-token"]')?.content || '';
        
        // 确保使用POST方法
        if (!token) {
          // 如果meta标签中没有token，从表单中获取
          var csrfInput = commentForm.querySelector('input[name="_token"]');
          token = csrfInput ? csrfInput.value : '';
        }

        fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
          },
          body: formData
        })
        .then(function(response) {
          if (!response.ok) {
            return response.json().then(function(data) {
              var error = new Error(data.message || translations.commentFailed);
              error.processed = true; // 标记为已处理
              
              // 如果是401未授权错误，提示登录
              if (response.status === 401) {
                if (typeof openAuthModal === 'function') {
                  openAuthModal('login');
                }
                // 使用后端返回的翻译消息，如果没有则使用前端翻译
                alert(data.message || translations.commentLoginRequired || '请先登录后再发布评论');
                return Promise.reject(error);
              }
              // 如果是403禁止访问错误（敏感词检测）
              if (response.status === 403) {
                // 使用后端返回的翻译消息（包含敏感词列表）
                alert(data.message || translations.commentSensitiveWord || '评论内容包含敏感词，无法提交');
                return Promise.reject(error);
              }
              // 其他错误
              alert(data.message || translations.commentFailed);
              return Promise.reject(error);
            });
          }
          return response.json();
        })
        .then(function(data) {
          if (data.code === 0) {
            // 成功，刷新页面
            alert(translations.commentSuccess);
            location.reload();
          } else {
            alert(data.message || translations.commentFailed);
            if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.textContent = translations.submitComment;
            }
          }
        })
        .catch(function(error) {
          console.error('Error:', error);
          // 如果错误已经在then中处理过（显示了alert），则不再重复显示
          // 401和403错误已经在then中处理并显示了alert，这里只处理其他未预期的错误
          if (error.message && !error.processed) {
            alert(error.message || translations.commentFailed);
          }
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = translations.submitComment;
          }
        });
        
        return false;
      });

      // 初始化回复按钮
      initReplyButtons();
    }

    // 确保DOM加载完成后执行
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initCommentForm);
    } else {
      initCommentForm();
    }
  })();
  
  // 点赞功能
  (function(){
    function initLikeButtons(){
      var likeButtons = document.querySelectorAll('.post-like-item');
      if (!likeButtons.length) return;
      
      likeButtons.forEach(function(btn){
        btn.addEventListener('click', function(e){
          e.preventDefault();
          e.stopPropagation();
          
          var postId = this.getAttribute('data-post-id');
          if (!postId) return;
          
          var icon = this.querySelector('i');
          var countSpan = this.querySelector('.likes-count');
          var originalClass = icon ? icon.className : '';
          
          // 禁用按钮，防止重复点击
          if (this.dataset.loading === 'true') return;
          this.dataset.loading = 'true';
          this.style.opacity = '0.6';
          this.style.pointerEvents = 'none';
          
          var url = '{{ route("site.post.like", ["post" => ":id"]) }}'.replace(':id', postId);
          var token = document.querySelector('meta[name="csrf-token"]')?.content || '';
          
          fetch(url, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': token,
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
          })
          .then(function(response){
            if (!response.ok) {
              return response.json().then(function(data) {
                throw new Error(data.message || '操作失败');
              });
            }
            return response.json();
          })
          .then(function(data){
            if (data.code === 0) {
              // 更新图标
              if (icon) {
                if (data.liked) {
                  icon.className = originalClass.replace('bi-hand-thumbs-up', 'bi-hand-thumbs-up-fill');
                } else {
                  icon.className = originalClass.replace('bi-hand-thumbs-up-fill', 'bi-hand-thumbs-up');
                }
              }
              // 更新数量
              if (countSpan) {
                countSpan.textContent = data.likes_count || 0;
              }
            } else {
              alert(data.message || '操作失败');
            }
          })
          .catch(function(error){
            console.error('Error:', error);
            alert(error.message || '操作失败，请重试');
          })
          .finally(function(){
            // 恢复按钮
            btn.dataset.loading = 'false';
            btn.style.opacity = '1';
            btn.style.pointerEvents = 'auto';
          });
        });
      });
    }
    
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initLikeButtons);
    } else {
      setTimeout(initLikeButtons, 50);
    }
  })();
  
  // 收藏功能
  (function(){
    function initFavoriteButtons(){
      var favoriteButtons = document.querySelectorAll('.post-favorite-item');
      if (!favoriteButtons.length) return;
      
      favoriteButtons.forEach(function(btn){
        btn.addEventListener('click', function(e){
          e.preventDefault();
          e.stopPropagation();
          
          var postId = this.getAttribute('data-post-id');
          if (!postId) return;
          
          var icon = this.querySelector('i');
          var countSpan = this.querySelector('.favorites-count');
          var originalClass = icon ? icon.className : '';
          
          // 禁用按钮，防止重复点击
          if (this.dataset.loading === 'true') return;
          this.dataset.loading = 'true';
          this.style.opacity = '0.6';
          this.style.pointerEvents = 'none';
          
          var url = '{{ route("site.post.favorite", ["post" => ":id"]) }}'.replace(':id', postId);
          var token = document.querySelector('meta[name="csrf-token"]')?.content || '';
          
          fetch(url, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': token,
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
          })
          .then(function(response){
            if (!response.ok) {
              return response.json().then(function(data) {
                throw new Error(data.message || '操作失败');
              });
            }
            return response.json();
          })
          .then(function(data){
            if (data.code === 0) {
              // 更新图标
              if (icon) {
                if (data.favorited) {
                  icon.className = originalClass.replace('bi-star', 'bi-star-fill');
                } else {
                  icon.className = originalClass.replace('bi-star-fill', 'bi-star');
                }
              }
              // 更新数量
              if (countSpan) {
                countSpan.textContent = data.favorites_count || 0;
              }
            } else {
              alert(data.message || '操作失败');
            }
          })
          .catch(function(error){
            console.error('Error:', error);
            alert(error.message || '操作失败，请重试');
          })
          .finally(function(){
            // 恢复按钮
            btn.dataset.loading = 'false';
            btn.style.opacity = '1';
            btn.style.pointerEvents = 'auto';
          });
        });
      });
    }
    
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initFavoriteButtons);
    } else {
      setTimeout(initFavoriteButtons, 50);
    }
  })();
</script>
@endsection
