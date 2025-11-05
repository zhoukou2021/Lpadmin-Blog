@if(isset($categories) && $categories->count() > 0)
  <div class="card" style="margin-bottom:12px">
    <div class="card-hd">{{ __('blog.categories') }}</div>
    <div class="card-bd">
      @foreach($categories as $c)
        <a href="{{ blog_url($c['slug']) }}" class="muted">{{ $c['name'] }}</a>
      @endforeach
    </div>
  </div>
@endif

@if(isset($tags) && $tags->count() > 0)
  <div class="card">
    <div class="card-hd">{{ __('blog.tag_cloud') }}</div>
    <div class="card-bd">
      @foreach($tags as $t)
        <a href="{{ blog_url($t['slug']) }}" class="muted">#{{ $t['name'] }}</a>
      @endforeach
    </div>
  </div>
@endif

