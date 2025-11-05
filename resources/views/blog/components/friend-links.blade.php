{{-- 
  友情链接组件
  参数:
    $friendLinks: 友情链接数组列表（由 get_blog_friend_links() 辅助函数返回），每个元素包含 ['link' => '', 'title' => '']
--}}
@if(isset($friendLinks) && is_array($friendLinks) && !empty($friendLinks))
    <div class="friend-links">
      @foreach($friendLinks as $fl)
        @if(!empty($fl['link']) && !empty($fl['title']))
          <a href="{{ $fl['link'] }}" target="_blank" rel="noopener noreferrer">{{ $fl['title'] }}</a>
        @endif
      @endforeach
    </div>
@endif

