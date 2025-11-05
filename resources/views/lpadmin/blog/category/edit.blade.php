<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>编辑分类</title>
    <link rel="stylesheet" href="/static/admin/component/pear/css/pear.css" />
    <link rel="stylesheet" href="/static/admin/css/reset.css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="pear-container">

<div class="layui-card">
    <div class="layui-card-body">
        <form class="layui-form" lay-filter="cat-form">

            <div class="layui-form-item">
                <label class="layui-form-label">父级分类</label>
                <div class="layui-input-block">
                    <select name="parent_id">
                        <option value="0">顶级分类</option>
                        @foreach($parents as $p)
                            <option value="{{ $p->id }}" {{ $category->parent_id==$p->id?'selected':'' }}>{{ $p->display_name ?? ('#'.$p->id) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">Slug</label>
                <div class="layui-input-block">
                    <input type="text" name="slug" class="layui-input" value="{{ $category->slug ?? '' }}" placeholder="留空则自动生成 category_{id}">
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
                            <label class="layui-form-label">{{ $langLabels[$l] ?? $l }} 名称</label>
                            <div class="layui-input-block">
                                <input type="text" name="name[{{ $l }}]" class="layui-input" value="{{ $category->name[$l] ?? '' }}" {{ $i===0 ? 'required' : '' }}>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">描述</label>
                            <div class="layui-input-block">
                                <textarea name="description[{{ $l }}]" class="layui-textarea">{{ $category->description[$l] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">排序</label>
                <div class="layui-input-block">
                    <input type="number" name="sort" value="{{ $category->sort }}" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">是否显示</label>
                <div class="layui-input-block">
                    <input type="hidden" name="visible" value="0">
                    <input type="checkbox" name="visible" value="1" title="显示" {{ $category->visible?'checked':'' }}>
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
    let form = layui.form; let $ = layui.$;
    form.on('submit(save)', function(data){
        $.ajax({
            url: "{{ route('lpadmin.blog.category.update', $category->id) }}",
            method: 'POST',
            data: Object.assign({}, data.field, {_method: 'PUT'}),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(res){
                if (res.code === 0 || res.code === 200) {
                    layer.msg(res.message || '保存成功', {icon:1});
                    if (parent.refreshTable) parent.refreshTable();
                    if (parent.layer) parent.layer.close(parent.layer.getFrameIndex(window.name));
                } else { layer.msg(res.message || '保存失败', {icon:2}); }
            },
            error: function(){ layer.msg('网络异常', {icon:2}); }
        });
        return false;
    });
});
</script>

</body>
</html>


