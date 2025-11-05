<div class="comment-item" data-comment-id="{{ $comment['id'] }}">
  <div class="comment-avatar">
    @if(!empty($comment['avatar']))
      <img src="{{ $comment['avatar'] }}" alt="{{ $comment['nickname'] }}">
    @else
      <div class="comment-avatar-default">
        <i class="bi bi-person-circle"></i>
      </div>
    @endif
  </div>
  <div class="comment-content">
    <div class="comment-header">
      <div class="comment-author">{{ $comment['nickname'] }}</div>
      <div class="comment-time muted">{{ $comment['created_at'] }}</div>
    </div>
    <div class="comment-text">{{ $comment['content'] }}</div>
    <div class="comment-actions">
      <button type="button" class="btn-reply-comment" data-comment-id="{{ $comment['id'] }}" data-comment-name="{{ $comment['nickname'] }}">
        <i class="bi bi-reply"></i> {{ __('blog.reply') }}
      </button>
    </div>
    @if(!empty($comment['children']) && count($comment['children']) > 0)
      <div class="comment-children">
        @foreach($comment['children'] as $child)
          @include('blog.post.comment-item', ['comment' => $child, 'depth' => $depth + 1])
        @endforeach
      </div>
    @endif
  </div>
</div>

