<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>广告管理</title>
    <link rel="stylesheet" href="/static/admin/component/pear/css/pear.css" />
    <link rel="stylesheet" href="/static/admin/css/reset.css" />
    <link rel="stylesheet" href="/static/admin/css/table-common.css" />
</head>
<body class="pear-container">

<div class="layui-card">
    <div class="layui-card-body">
        <form class="layui-form" action="">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">类型</label>
                    <div class="layui-input-inline">
                        <select name="type">
                            <option value="">全部</option>
                            @foreach(\App\Models\Blog\Ad::getTypeOptions() as $type => $label)
                                <option value="{{ $type }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">显示</label>
                    <div class="layui-input-inline">
                        <select name="visible">
                            <option value="">全部</option>
                            <option value="1">显示</option>
                            <option value="0">隐藏</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <button type="submit" class="pear-btn pear-btn-md pear-btn-primary" lay-submit lay-filter="query">
                        <i class="layui-icon layui-icon-search"></i> 查询
                    </button>
                    <a href="{{ route('lpadmin.blog.ad.create') }}" class="pear-btn pear-btn-md pear-btn-success">
                        <i class="layui-icon layui-icon-add-1"></i> 新建
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="layui-card">
    <div class="layui-card-body">
        <table id="ad-table" lay-filter="ad-table"></table>
    </div>
</div>

<script type="text/html" id="table-toolbar">
    <div class="layui-btn-group">
        <button class="pear-btn pear-btn-primary pear-btn-sm" lay-event="add"><i class="layui-icon layui-icon-add-1"></i> 新增</button>
        <button class="pear-btn pear-btn-danger pear-btn-sm" lay-event="batchRemove"><i class="layui-icon layui-icon-delete"></i> 批量删除</button>
    </div>
    </script>

<script type="text/html" id="row-bar">
    <div style="white-space: nowrap; display: flex; gap: 4px; justify-content: center;">
        <button class="table-action-btn table-action-edit" lay-event="edit" title="编辑">
            <i class="layui-icon layui-icon-edit"></i>
        </button>
        <button class="table-action-btn table-action-delete" lay-event="remove" title="删除">
            <i class="layui-icon layui-icon-delete"></i>
        </button>
    </div>
</script>

<script src="/static/admin/component/layui/layui.js?v=2.8.12"></script>
<script src="/static/admin/component/pear/pear.js"></script>
<script>
    const SELECT_API = "{{ route('lpadmin.blog.ad.select') }}";
    const BATCH_DELETE_API = "{{ route('lpadmin.blog.ad.batch_delete') }}";
    const CREATE_URL = "{{ route('lpadmin.blog.ad.create') }}";
    const EDIT_URL = "{{ route('lpadmin.blog.ad.edit', ':id') }}";
    const DELETE_URL = "{{ route('lpadmin.blog.ad.destroy', ':id') }}";

    layui.use(['table','form'], function(){
        let table = layui.table; let form = layui.form; let $ = layui.$;

        let cols = [[
            {type:'checkbox'},
            {title:'ID', field:'id', width:60, align:'center'},
            {title:'标题', field:'title', width:220},
            {title:'类型', field:'type', width:140, align:'center', templet:d=> d.type_label || '未知'},
            {title:'链接', field:'link', width:240},
            {title:'图片', field:'image', width:120, align:'center', templet:d=> d.image?('<img src="'+d.image+'" style="height:34px"/>'):'-'},
            {title:'排序', field:'sort', width:90, align:'center'},
            {title:'显示', field:'visible', width:80, align:'center', templet:d=> d.visible?'<span class="layui-badge layui-bg-green">是</span>':'<span class="layui-badge">否</span>'},
            {title:'创建时间', field:'created_at', width:170, align:'center'},
            {title:'操作', width:100, align:'center', toolbar:'#row-bar', fixed:'right'}
        ]];

        table.render({
            elem:'#ad-table', url: SELECT_API, method:'GET', toolbar:'#table-toolbar', cols: cols, skin:'line', size:'lg', page:true,
            parseData: function(res){ return { code: res.code, msg: res.message, count: res.count||0, data: res.data||[] }; }
        });

        form.on('submit(query)', function(data){ table.reload('ad-table', { where: data.field, page:{curr:1} }); return false; });
        table.on('toolbar(ad-table)', function(obj){
            let check = table.checkStatus('ad-table');
            if (obj.event==='add') {
                layer.open({ type:2, title:'新建广告', shade:0.1, area:['900px','650px'], content: CREATE_URL });
            } else if (obj.event==='batchRemove') {
                let rows = check.data; if (!rows.length) return layer.msg('请选择要删除的数据', {icon:2});
                let ids = rows.map(r=>r.id);
                layer.confirm('确认批量删除选中记录？', {icon:3, title:'提示'}, function(index){
                    $.ajax({ url: BATCH_DELETE_API, method:'DELETE', data:{ids:ids}, headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, success:function(res){
                        if (res.code===0) { layer.msg('删除成功', {icon:1}); table.reload('ad-table'); } else { layer.msg(res.message||'删除失败', {icon:2}); }
                    }, error:function(){ layer.msg('网络异常', {icon:2}); }});
                    layer.close(index);
                });
            }
        });

        table.on('tool(ad-table)', function(obj){
            const data = obj.data;
            if (obj.event==='edit') {
                let url = EDIT_URL.replace(':id', data.id);
                layer.open({ type:2, title:'编辑广告', shade:0.1, area:['900px','650px'], content: url });
            } else if (obj.event==='remove') {
                layer.confirm('确认删除该记录？', {icon:3, title:'提示'}, function(index){
                    $.ajax({ url: DELETE_URL.replace(':id', data.id), method:'DELETE', headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, success:function(res){
                        if (res.code===0 || res.code===200) { layer.msg('删除成功', {icon:1}); table.reload('ad-table'); } else { layer.msg(res.message||'删除失败', {icon:2}); }
                    }, error:function(){ layer.msg('网络异常', {icon:2}); }});
                    layer.close(index);
                });
            }
        });
    });
</script>

</body>
</html>


