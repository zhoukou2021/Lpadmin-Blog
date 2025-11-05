<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>编辑顶部菜单</title>
    <link rel="stylesheet" href="/static/admin/component/pear/css/pear.css" />
    <link rel="stylesheet" href="/static/admin/css/reset.css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="pear-container">

<div class="layui-card">
  <div class="layui-card-body">
    <form class="layui-form" lay-filter="nav-form">
      <div class="layui-form-item">
        <label class="layui-form-label">父级导航</label>
        <div class="layui-input-block">
          <select name="parent_id">
            <option value="0">顶级导航</option>
            @foreach($parents as $p)
              <option value="{{ $p->id }}" {{ ($nav->parent_id ?? 0) == $p->id ? 'selected' : '' }}>{{ $p->display_name ?? ('#'.$p->id) }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="layui-form-item">
        <label class="layui-form-label">链接</label>
        <div class="layui-input-block">
          <input type="text" name="url" class="layui-input" value="{{ $nav->url ?? '' }}" placeholder="如 /cn 或 /cn/posts" required>
        </div>
      </div>

      @php $langData = enabled_langs(); $langs = $langData['codes']; $langLabels = $langData['labels']; @endphp
      <div class="layui-tab" lay-filter="lang-tabs">
        <ul class="layui-tab-title">
          @foreach($langs as $i => $l)
            <li class="{{ $i===0 ? 'layui-this' : '' }}">{{ $langLabels[$l] ?? $l }}</li>
          @endforeach
        </ul>
        <div class="layui-tab-content">
          @foreach($langs as $i => $l)
          <div class="layui-tab-item {{ $i===0 ? 'layui-show' : '' }}">
            <div class="layui-form-item">
              <label class="layui-form-label">{{ $langLabels[$l] ?? $l }} 标题</label>
              <div class="layui-input-block">
                <input type="text" name="title[{{ $l }}]" class="layui-input" value="{{ $nav->title[$l] ?? '' }}" {{ $i===0 ? 'required' : '' }}>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>

      

      @include('lpadmin.components.icon-picker', [
          'name' => 'icon',
          'value' => $nav->icon,
          'label' => '图标',
          'placeholder' => '请选择图标',
          'id' => 'blog-nav-icon'
      ])

      <div class="layui-form-item">
        <label class="layui-form-label">排序</label>
        <div class="layui-input-block">
          <input type="number" name="sort" value="{{ $nav->sort }}" class="layui-input">
        </div>
      </div>

      <div class="layui-form-item">
        <label class="layui-form-label">是否显示</label>
        <div class="layui-input-block">
          <input type="hidden" name="visible" value="0">
          <input type="checkbox" name="visible" value="1" title="显示" {{ $nav->visible?'checked':'' }}>
        </div>
      </div>

      <div class="layui-form-item">
        <div class="layui-input-block">
          <button type="submit" class="pear-btn pear-btn-primary" lay-submit lay-filter="save">保存</button>
          <button type="reset" class="pear-btn">重置</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="/static/admin/component/layui/layui.js?v=2.8.12"></script>
<script src="/static/admin/component/pear/pear.js"></script>
<script>
layui.use(['form'], function(){
  const form = layui.form; const $ = layui.$;
  form.on('submit(save)', function(data){
    $.ajax({
      url: "{{ route('lpadmin.blog.nav.update', $nav->id) }}",
      method: 'POST', data: Object.assign({}, data.field, {_method:'PUT'}),
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      success: function(res){
        if (res.code===0||res.code===200) {
          layer.msg(res.message||'保存成功',{icon:1});
          if (parent.refreshTable) parent.refreshTable();
          if (parent.layer) parent.layer.close(parent.layer.getFrameIndex(window.name));
        } else { layer.msg(res.message||'保存失败',{icon:2}); }
      }, error:function(){ layer.msg('网络异常',{icon:2}); }
    });
    return false;
  });
});
</script>

</body>
</html>


