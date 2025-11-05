<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>标签管理</title>
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
                        <label class="layui-form-label">关键词</label>
                        <div class="layui-input-inline">
                            <input type="text" name="q" placeholder="名称/Slug" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <button type="submit" class="pear-btn pear-btn-md pear-btn-primary" lay-submit lay-filter="query">
                            <i class="layui-icon layui-icon-search"></i> 查询
                        </button>
                        <button type="reset" class="pear-btn pear-btn-md">
                            <i class="layui-icon layui-icon-refresh"></i> 重置
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="layui-card">
        <div class="layui-card-body">
            <table id="tag-table" lay-filter="tag-table"></table>
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
        const SELECT_API = "{{ route('lpadmin.blog.tag.select') }}";
        const CREATE_URL = "{{ route('lpadmin.blog.tag.create') }}";
        const EDIT_URL = "{{ route('lpadmin.blog.tag.edit', ':id') }}";
        const DELETE_URL = "{{ route('lpadmin.blog.tag.destroy', ':id') }}";
        const BATCH_DELETE_API = "{{ route('lpadmin.blog.tag.batch_delete') }}";

        layui.use(['table','form'], function(){
            let table = layui.table;
            let form = layui.form;

            let cols = [[
                {type:'checkbox'},
                {title:'ID', field:'id', width:90, align:'center'},
                {title:'名称', field:'name', width:260},
                {title:'Slug', field:'slug', width:260},
                {title:'创建时间', field:'created_at', width:160, align:'center'},
                {title:'操作', width:80, align:'center', toolbar:'#row-bar', fixed:'right'}
            ]];

            table.render({
                elem:'#tag-table',
                url: SELECT_API,
                method:'GET',
                toolbar:'#table-toolbar',
                cols: cols,
                skin:'line',
                size:'lg',
                page:true,
                parseData: function(res){
                    return { code: res.code, msg: res.message, count: res.count || 0, data: res.data || [] };
                }
            });

            form.on('submit(query)', function(data){
                table.reload('tag-table', { where: data.field, page:{curr:1} });
                return false;
            });
            // 工具栏
            table.on('toolbar(tag-table)', function(obj){
                let checkStatus = table.checkStatus('tag-table');
                if (obj.event === 'add') {
                    layer.open({ type:2, title:'新增标签', shade:0.1, area:['700px','500px'], content: CREATE_URL });
                } else if (obj.event === 'batchRemove') {
                    let rows = checkStatus.data; if (!rows.length) return layer.msg('请选择要删除的数据', {icon:2});
                    let ids = rows.map(r=>r.id);
                    layer.confirm('确认批量删除？', {icon:3, title:'提示'}, function(index){
                        $.ajax({ url: BATCH_DELETE_API, method:'DELETE', data:{ids:ids}, headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, success:function(res){
                            if (res.code === 0) { layer.msg('删除成功', {icon:1}); table.reload('tag-table'); } else { layer.msg(res.message||'删除失败', {icon:2}); }
                        }, error:function(){ layer.msg('网络异常', {icon:2}); }});
                        layer.close(index);
                    });
                }
            });

            // 行工具
            table.on('tool(tag-table)', function(obj){
                const data = obj.data;
                if (obj.event === 'edit') {
                    layer.open({ type:2, title:'编辑标签', shade:0.1, area:['700px','500px'], content: EDIT_URL.replace(':id', data.id) });
                } else if (obj.event === 'remove') {
                    layer.confirm('确认删除该记录？', {icon:3, title:'提示'}, function(index){
                        $.ajax({ url: DELETE_URL.replace(':id', data.id), method:'DELETE', headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, success:function(res){
                            if (res.code === 0 || res.code === 200) { layer.msg('删除成功', {icon:1}); table.reload('tag-table'); } else { layer.msg(res.message||'删除失败', {icon:2}); }
                        }, error:function(){ layer.msg('网络异常', {icon:2}); }});
                        layer.close(index);
                    });
                }
            });
        });
    </script>

</body>
</html>


