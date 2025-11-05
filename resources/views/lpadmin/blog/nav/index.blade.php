<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>前端顶部菜单</title>
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
                    <label class="layui-form-label">是否显示</label>
                    <div class="layui-input-inline">
                        <select name="visible">
                            <option value="">全部</option>
                            <option value="1">显示</option>
                            <option value="0">隐藏</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <button type="submit" class="pear-btn pear-btn-md pear-btn-primary" lay-submit lay-filter="query"><i class="layui-icon layui-icon-search"></i> 查询</button>
                    <button type="reset" class="pear-btn pear-btn-md"><i class="layui-icon layui-icon-refresh"></i> 重置</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="layui-card">
    <div class="layui-card-body">
        <table id="nav-table" lay-filter="nav-table"></table>
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
    <button class="table-action-btn table-action-edit" lay-event="edit" title="编辑"><i class="layui-icon layui-icon-edit"></i></button>
    <button class="table-action-btn table-action-delete" lay-event="remove" title="删除"><i class="layui-icon layui-icon-delete"></i></button>
  </div>
</script>

<script src="/static/admin/component/layui/layui.js?v=2.8.12"></script>
<script src="/static/admin/component/pear/pear.js"></script>
<script>
layui.use(['table','form','jquery'], function(){
  const table = layui.table; const form = layui.form; const $ = layui.$;
  const SELECT_API = "{{ route('lpadmin.blog.nav.select') }}";
  const CREATE_URL = "{{ route('lpadmin.blog.nav.create') }}";
  const EDIT_URL = "{{ route('lpadmin.blog.nav.edit', ':id') }}";
  const DELETE_URL = "{{ route('lpadmin.blog.nav.destroy', ':id') }}";
  const BATCH_DELETE_API = "{{ route('lpadmin.blog.nav.batch_delete') }}";

  const cols = [[
    {type:'checkbox'},
    {title:'ID', field:'id', width:90, align:'center'},
    {title:'标题', field:'title', width:300, templet: function(d){
      let indent = '';
      if (d.level > 0) {
        indent = '<span style="display:inline-block;width:' + (d.level * 20) + 'px;"></span><span style="color:#999;">├─</span> ';
      }
      return indent + (d.title || '');
    }},
    {title:'图标', field:'icon', width:120, align:'center', templet:d=> d.icon?('<i class="layui-icon '+ d.icon +'"></i>'):''},
    {title:'链接', field:'url', width:320},
    {title:'排序', field:'sort', width:100, align:'center'},
    {title:'显示', field:'visible', width:80, align:'center', templet:d=> d.visible==1?'<span class="layui-badge layui-bg-green">显示</span>':'<span class="layui-badge">隐藏</span>'},
    {title:'创建时间', field:'created_at', width:160, align:'center'},
    {title:'操作', width:110, align:'center', toolbar:'#row-bar', fixed:'right'}
  ]];

  table.render({
    elem:'#nav-table', url:SELECT_API, method:'GET', toolbar:'#table-toolbar', cols:cols, skin:'line', size:'lg', page:false, // 树状结构不分页
    parseData:function(res){ return {code: res.code, msg: res.message, count: res.count||0, data: res.data||[]}; }
  });

  form.on('submit(query)', function(data){ table.reload('nav-table', {where:data.field, page:{curr:1}}); return false; });

  table.on('toolbar(nav-table)', function(obj){
    let check = table.checkStatus('nav-table');
    if (obj.event==='add') {
      layer.open({type:2,title:'新增菜单',shade:0.1,area:['800px','600px'],content:CREATE_URL});
    } else if (obj.event==='batchRemove') {
      let rows = check.data; if (!rows.length) return layer.msg('请选择要删除的数据',{icon:2});
      let ids = rows.map(r=>r.id);
      layer.confirm('确认批量删除？',{icon:3,title:'提示'},function(index){
        $.ajax({url:BATCH_DELETE_API, method:'DELETE', data:{ids:ids}, headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, success:function(res){
          if (res.code===0) { layer.msg('删除成功',{icon:1}); table.reload('nav-table'); } else { layer.msg(res.message||'删除失败',{icon:2}); }
        }, error:function(){ layer.msg('网络异常',{icon:2}); }});
        layer.close(index);
      });
    }
  });

  table.on('tool(nav-table)', function(obj){
    const data = obj.data;
    if (obj.event==='edit') {
      layer.open({type:2,title:'编辑菜单',shade:0.1,area:['800px','600px'],content: EDIT_URL.replace(':id', data.id)});
    } else if (obj.event==='remove') {
      layer.confirm('确认删除该记录？',{icon:3,title:'提示'}, function(index){
        $.ajax({url: DELETE_URL.replace(':id', data.id), method:'DELETE', headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, success:function(res){
          if (res.code===0||res.code===200) { layer.msg('删除成功',{icon:1}); table.reload('nav-table'); } else { layer.msg(res.message||'删除失败',{icon:2}); }
        }, error:function(){ layer.msg('网络异常',{icon:2}); }});
        layer.close(index);
      });
    }
  });
});
</script>

</body>
</html>


