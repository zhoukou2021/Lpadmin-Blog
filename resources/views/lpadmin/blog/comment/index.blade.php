<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>评论管理</title>
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
                        <label class="layui-form-label">状态</label>
                        <div class="layui-input-inline">
                            <select name="status">
                                <option value="">全部</option>
                                <option value="pending">待审</option>
                                <option value="approved">已通过</option>
                                <option value="blocked">已屏蔽</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">文章ID</label>
                        <div class="layui-input-inline">
                            <input type="number" name="post_id" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">用户ID</label>
                        <div class="layui-input-inline">
                            <input type="number" name="user_id" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">关键词</label>
                        <div class="layui-input-inline">
                            <input type="text" name="q" placeholder="评论内容" class="layui-input">
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
            <table id="comment-table" lay-filter="comment-table"></table>
        </div>
    </div>

    <script type="text/html" id="table-toolbar">
        <div class="layui-btn-group">
            <button class="pear-btn pear-btn-danger pear-btn-sm" lay-event="batchRemove"><i class="layui-icon layui-icon-delete"></i> 批量删除</button>
        </div>
    </script>

    <script type="text/html" id="row-bar">
        <div style="white-space: nowrap; display: flex; gap: 4px; justify-content: center;">
            <button class="table-action-btn" lay-event="approve" title="通过">
                <i class="layui-icon layui-icon-ok"></i>
            </button>
            <button class="table-action-btn" lay-event="block" title="屏蔽">
                <i class="layui-icon layui-icon-close"></i>
            </button>
            <button class="table-action-btn table-action-delete" lay-event="remove" title="删除">
                <i class="layui-icon layui-icon-delete"></i>
            </button>
        </div>
    </script>

    <script src="/static/admin/component/layui/layui.js?v=2.8.12"></script>
    <script src="/static/admin/component/pear/pear.js"></script>
    <script>
        const SELECT_API = "{{ route('lpadmin.blog.comment.select') }}";
        const APPROVE_API = "{{ route('lpadmin.blog.comment.approve', ':id') }}";
        const BLOCK_API = "{{ route('lpadmin.blog.comment.block', ':id') }}";
        const REMOVE_API = "{{ route('lpadmin.blog.comment.destroy', ':id') }}";

        layui.use(['table','form','jquery'], function(){
            let table = layui.table;
            let form = layui.form;
            let $ = layui.$;

            let cols = [[
                {type:'checkbox'},
                {title:'ID', field:'id', width:90, align:'center'},
                {title:'文章', field:'post_id', width:120, align:'center', templet:function(d){
                    return d.post_id ? ('【' +d.post_id + '】' + (d.post_title || '未知文章')) : '-';
                }},
                {title:'用户', field:'user_id', width:120, align:'center', templet:function(d){
                    return d.user_id ? ('【' +d.user_id + '】' + (d.username || '未知用户')) : '-';
                }},
                {title:'内容', field:'content', minWidth:260},
                {title:'状态', field:'status', width:110, align:'center', templet:d=> {
                    const map = {pending:'待审', approved:'已通过', blocked:'已屏蔽'};
                    const cls = d.status==='approved'?'layui-bg-green':(d.status==='pending'?'layui-bg-orange':'');
                    return '<span class="layui-badge '+cls+'">'+(map[d.status]||d.status)+'</span>';
                }},
                {title:'时间', field:'created_at', width:170, align:'center'},
                {title:'操作', width:140, align:'center', toolbar:'#row-bar', fixed:'right'}
            ]];

            table.render({
                elem:'#comment-table',
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
                table.reload('comment-table', { where: data.field, page:{curr:1} });
                return false;
            });

            table.on('tool(comment-table)', function(obj){
                const data = obj.data;
                if (obj.event === 'approve' || obj.event === 'block' || obj.event === 'remove') {
                    let url = '';
                    let method = 'POST';
                    if (obj.event === 'approve') url = APPROVE_API.replace(':id', data.id);
                    if (obj.event === 'block') url = BLOCK_API.replace(':id', data.id);
                    if (obj.event === 'remove') { url = REMOVE_API.replace(':id', data.id); method = 'DELETE'; }

                    $.ajax({
                        url: url,
                        method: method,
                        dataType: 'json',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(res){
                            if (res && (res.code === 0 || res.code === 200)) {
                                layer.msg(res.message || '操作成功', {icon:1});
                                table.reload('comment-table');
                            } else {
                                layer.msg(res && res.message ? res.message : '操作失败', {icon:2});
                            }
                        },
                        error: function(xhr){
                            let msg = '网络异常';
                            if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            } else if (xhr && xhr.responseText) {
                                try {
                                    const json = JSON.parse(xhr.responseText);
                                    if (json && json.message) msg = json.message;
                                } catch(e) {}
                            }
                            layer.msg(msg, {icon:2});
                        }
                    });
                }
            });

            table.on('toolbar(comment-table)', function(obj){
                let tableModule = layui.table;
                let checkStatus = tableModule.checkStatus('comment-table');
                if (obj.event === 'batchRemove') {
                    let rows = checkStatus.data; if (!rows.length) return layer.msg('请选择要删除的数据', {icon:2});
                    let ids = rows.map(r=>r.id);
                    layer.confirm('确认批量删除？', {icon:3, title:'提示'}, function(index){
                        $.ajax({ url: "{{ route('lpadmin.blog.comment.batch_delete') }}", method:'DELETE', data:{ids:ids}, headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, success:function(res){
                            if (res.code === 0) { layer.msg('删除成功', {icon:1}); tableModule.reload('comment-table'); } else { layer.msg(res.message||'删除失败', {icon:2}); }
                        }, error:function(){ layer.msg('网络异常', {icon:2}); }});
                        layer.close(index);
                    });
                }
            });
        });
    </script>

</body>
</html>


