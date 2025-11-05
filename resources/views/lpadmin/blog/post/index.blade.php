<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>文章管理</title>
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
                            <input type="text" name="q" placeholder="标题/摘要" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">状态</label>
                        <div class="layui-input-inline">
                            <select name="status">
                                <option value="">全部</option>
                                <option value="draft">草稿</option>
                                <option value="published">已发布</option>
                                <option value="offline">已下线</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">创建日期</label>
                        <div class="layui-input-inline">
                            <input type="date" name="date_start" class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="date" name="date_end" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <button type="submit" class="pear-btn pear-btn-md pear-btn-primary" lay-submit lay-filter="query">
                            <i class="layui-icon layui-icon-search"></i> 查询
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="layui-card">
        <div class="layui-card-body">
            <table id="post-table" lay-filter="post-table"></table>
        </div>
    </div>

    <script type="text/html" id="table-toolbar">
        <div class="layui-btn-group">
            <button class="pear-btn pear-btn-primary pear-btn-sm" lay-event="add"><i class="layui-icon layui-icon-add-1"></i> 新增</button>
            <button class="pear-btn pear-btn-success pear-btn-sm" lay-event="batchPublish"><i class="layui-icon layui-icon-release"></i> 批量发布</button>
            <button class="pear-btn pear-btn-warm pear-btn-sm" lay-event="batchDraft"><i class="layui-icon layui-icon-file"></i> 批量草稿</button>
            <button class="pear-btn pear-btn-normal pear-btn-sm" lay-event="batchOffline"><i class="layui-icon layui-icon-pause"></i> 批量下线</button>
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
        const SELECT_API = "{{ route('lpadmin.blog.post.select') }}";
        const BATCH_DELETE_API = "{{ route('lpadmin.blog.post.batch_delete') }}";
        const BATCH_STATUS_API = "{{ route('lpadmin.blog.post.batch_status') }}";
        const CREATE_URL = "{{ route('lpadmin.blog.post.create') }}";
        const EDIT_URL = "{{ route('lpadmin.blog.post.edit', ':id') }}";
        const DELETE_URL = "{{ route('lpadmin.blog.post.destroy', ':id') }}";

        layui.use(['table','form','jquery'], function(){
            let table = layui.table;
            let form = layui.form;
            let $ = layui.jquery;

            let cols = [[
                {type:'checkbox'},
                {title:'ID', field:'id', width:40, align:'center'},
                {title:'标题', field:'title', width:200},
                {title:'分类', field:'category', width:100},
                {title:'标签', field:'tags', width:220},
                {title:'状态', field:'status', width:110, align:'center', templet:d=> {
                    const map = {draft:'草稿', published:'已发布', offline:'已下线'};
                    const cls = d.status==='published'?'layui-bg-green':(d.status==='draft'?'layui-bg-gray':'');
                    return '<span class="layui-badge '+cls+'">'+(map[d.status]||d.status)+'</span>';
                }},
                {title:'推荐', field:'recommend', width:80, align:'center', templet:d=> d.recommend?'<span class="layui-badge layui-bg-orange">是</span>':'<span class="layui-badge">否</span>'},
                {title:'评论', field:'comments_count', width:90, align:'center'},
                {title:'点赞', field:'likes_count', width:90, align:'center'},
                {title:'收藏', field:'favorites_count', width:90, align:'center'},
                {title:'发布时间', field:'published_at', width:170, align:'center'},
                {title:'创建时间', field:'created_at', width:170, align:'center'},
                {title:'操作', width:100, align:'center', toolbar:'#row-bar', fixed:'right'}
            ]];

            table.render({
                elem:'#post-table',
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
                table.reload('post-table', { where: data.field, page:{curr:1} });
                return false;
            });
            // 工具栏事件
            table.on('toolbar(post-table)', function(obj){
                let checkStatus = table.checkStatus('post-table');
                let rows = checkStatus.data;
                
                if (obj.event === 'add') {
                    layer.open({ type:2, title:'新建文章', shade:0.1, area:['1000px','700px'], content: CREATE_URL });
                } else if (obj.event === 'batchPublish' || obj.event === 'batchDraft' || obj.event === 'batchOffline') {
                    if (!rows.length) {
                        return layer.msg('请选择要操作的文章', {icon:2});
                    }
                    
                    let status = obj.event === 'batchPublish' ? 'published' : (obj.event === 'batchDraft' ? 'draft' : 'offline');
                    let statusText = obj.event === 'batchPublish' ? '发布' : (obj.event === 'batchDraft' ? '草稿' : '下线');
                    let ids = rows.map(r=>r.id);
                    
                    layer.confirm('确认批量设置为' + statusText + '？', {icon:3, title:'提示'}, function(index){
                        $.ajax({ 
                            url: BATCH_STATUS_API, 
                            method:'POST', 
                            data:{
                                ids: ids,
                                status: status
                            }, 
                            headers:{
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }, 
                            success:function(res){
                                if (res.code === 0) { 
                                    layer.msg(res.message || '操作成功', {icon:1}); 
                                    table.reload('post-table'); 
                                } else { 
                                    layer.msg(res.message || '操作失败', {icon:2}); 
                                }
                            }, 
                            error:function(){ 
                                layer.msg('网络异常', {icon:2}); 
                            }
                        });
                        layer.close(index);
                    });
                } else if (obj.event === 'batchRemove') {
                    if (!rows.length) return layer.msg('请选择要删除的数据', {icon:2});
                    let ids = rows.map(r=>r.id);
                    layer.confirm('确认批量删除选中记录？', {icon:3, title:'提示'}, function(index){
                        $.ajax({ url: BATCH_DELETE_API, method:'DELETE', data:{ids:ids}, headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, success:function(res){
                            if (res.code === 0) { layer.msg('删除成功', {icon:1}); table.reload('post-table'); } else { layer.msg(res.message||'删除失败', {icon:2}); }
                        }, error:function(){ layer.msg('网络异常', {icon:2}); }});
                        layer.close(index);
                    });
                }
            });

            // 行工具—编辑/删除
            table.on('tool(post-table)', function(obj){
                const data = obj.data;
                if (obj.event === 'edit') {
                    let url = EDIT_URL.replace(':id', data.id);
                    layer.open({ type:2, title:'编辑文章', shade:0.1, area:['1000px','700px'], content: url });
                } else if (obj.event === 'remove') {
                    layer.confirm('确认删除该记录？', {icon:3, title:'提示'}, function(index){
                        $.ajax({ url: DELETE_URL.replace(':id', data.id), method:'DELETE', headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, success:function(res){
                            if (res.code === 0 || res.code === 200) { layer.msg('删除成功', {icon:1}); table.reload('post-table'); } else { layer.msg(res.message||'删除失败', {icon:2}); }
                        }, error:function(){ layer.msg('网络异常', {icon:2}); }});
                        layer.close(index);
                    });
                }
            });
        });
    </script>

</body>
</html>


