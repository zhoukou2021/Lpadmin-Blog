<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>新建文章</title>
    <link rel="stylesheet" href="/static/admin/component/pear/css/pear.css" />
    <link rel="stylesheet" href="/static/admin/css/reset.css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="pear-container">

<div class="layui-card">
    <div class="layui-card-body">
        <form class="layui-form" lay-filter="post-form">
            <div class="layui-form-item">
                <label class="layui-form-label">作者ID</label>
                <div class="layui-input-block">
                    <input type="number" name="author_id" class="layui-input" required>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">分类</label>
                <div class="layui-input-block">
                    @php
                        $langOption = \App\Models\LPadmin\Option::where('name','lang')->first();
                        $langsForCat = ($langOption && $langOption->value) ? explode(',', $langOption->value) : ['zh','en'];
                    @endphp
                    <select name="category_id">
                        <option value="">未选择</option>
                        @foreach($categories as $c)
                            @php $catName = '#'.$c->id; @endphp
                            @foreach($langsForCat as $lc)
                                @if(isset($c->name[$lc]) && $c->name[$lc] !== '')
                                    @php $catName = $c->name[$lc]; @endphp @break
                                @endif
                            @endforeach
                            <option value="{{ $c->id }}">{{ $catName }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">状态</label>
                <div class="layui-input-block">
                    <select name="status">
                        <option value="draft">草稿</option>
                        <option value="published">已发布</option>
                        <option value="offline">已下线</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">发布时间</label>
                <div class="layui-input-block">
                    <input type="datetime-local" name="published_at" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">封面图</label>
                <div class="layui-input-block">
                    <input type="file" name="cover_file" accept="image/*" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">是否推荐</label>
                <div class="layui-input-block">
                    <input type="hidden" name="recommend" value="0">
                    <input type="checkbox" name="recommend" value="1" title="推荐">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">Slug</label>
                <div class="layui-input-block">
                    <input type="text" name="slug" class="layui-input" placeholder="留空则自动生成 article_{id}">
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
                                <input type="text" name="title[{{ $l }}]" class="layui-input" {{ $i===0 ? 'required' : '' }}>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">摘要</label>
                            <div class="layui-input-block">
                                <textarea name="summary[{{ $l }}]" class="layui-textarea"></textarea>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">内容</label>
                            <div class="layui-input-block">
                                <textarea name="content[{{ $l }}]" class="layui-textarea" rows="12" data-editor="tinymce"></textarea>
                            </div>
                        </div>
                        <fieldset class="layui-elem-field layui-field-title"><legend>SEO</legend></fieldset>
                        <div class="layui-form-item">
                            <label class="layui-form-label">关键词</label>
                            <div class="layui-input-block">
                                <input type="text" name="meta_title[{{ $l }}]" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">描述</label>
                            <div class="layui-input-block">
                                <textarea name="meta_desc[{{ $l }}]" class="layui-textarea"></textarea>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">标签</label>
                <div class="layui-input-block">
                    @php $langsForTag = $langs; @endphp
                    <select name="tag_ids[]" multiple lay-ignore style="min-width: 300px; min-height: 100px;" class="layui-select-multi">
                        @foreach($tags as $t)
                            @php $tagName = '#'.$t->id; @endphp
                            @foreach($langsForTag as $lt)
                                @if(isset($t->name[$lt]) && $t->name[$lt] !== '')
                                    @php $tagName = $t->name[$lt]; @endphp @break
                                @endif
                            @endforeach
                            <option value="{{ $t->id }}">{{ $tagName }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button type="submit" class="pear-btn pear-btn-primary" lay-submit lay-filter="save">保存</button>
                    <a href="{{ route('lpadmin.blog.post.index') }}" class="pear-btn">返回</a>
                </div>
            </div>

        </form>
    </div>
</div>

<script src="/static/admin/component/layui/layui.js?v=2.8.12"></script>
<script src="/static/admin/component/pear/pear.js"></script>
<script>
// 参照系统设置页的富文本加载方式
(function loadTiny(){
    var base = '/static/admin/tinymce';
    function load(src, cb, onerr){ var s=document.createElement('script'); s.src=src; s.onload=cb; s.onerror=onerr; document.head.appendChild(s); }
    if (!window.tinymce) {
        load(base + '/tinymce.min.js', function(){ if (window.tinymce) window.tinymce.baseURL = base; }, function(){ load('https://cdn.staticfile.org/tinymce/6.8.3/tinymce.min.js', function(){}); });
    }
})();
</script>
<script>
layui.use(['form','jquery'], function(){
    let form = layui.form; let $ = layui.$;

    // 批量初始化编辑器
    (function initEditors(){
        function doInit(base){
            if (!window.tinymce) return;
            try { tinymce.remove('textarea[data-editor="tinymce"]'); } catch(e){}
            tinymce.init({
                selector: 'textarea[data-editor="tinymce"]',
                language_url: (base ? base : (window.tinymce.baseURL || '')) + '/langs/zh_CN.js',
                language: 'zh_CN',
                menubar: false,
                branding: false,
                height: 420,
                plugins: 'code preview link lists table image media fullscreen searchreplace autosave',
                toolbar: 'undo redo | bold italic underline forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table | removeformat | code preview fullscreen',
                convert_urls: false,
                images_upload_handler: function (blobInfo, success, failure) {
                    success('data:' + blobInfo.blob().type + ';base64,' + blobInfo.base64());
                }
            });
        }
        // 等待 tinymce 加载完成
        var interval = setInterval(function(){
            if (window.tinymce) { clearInterval(interval); doInit(window.tinymce.baseURL || '/static/admin/tinymce'); }
        }, 50);
        setTimeout(function(){ clearInterval(interval); if (window.tinymce) doInit(window.tinymce.baseURL || '/static/admin/tinymce'); }, 2000);
    })();

    form.on('submit(save)', function(data){
        // 使用 FormData 以支持文件上传
        const fd = new FormData($(data.form)[0]);
        // 统一多选标签
        let selectedTags = $('select[name="tag_ids[]"]').val() || [];
        fd.delete('tag_ids[]');
        selectedTags.forEach(id=>fd.append('tag_ids[]', id));

        // 处理recommend字段：checkbox选中时删除hidden input的值
        if ($('input[name="recommend"][type="checkbox"]').is(':checked')) {
            fd.delete('recommend'); // 删除所有recommend值
            fd.append('recommend', '1'); // 只保留checkbox的值
        } else {
            fd.delete('recommend'); // 删除所有recommend值
            fd.append('recommend', '0'); // 只保留hidden input的值
        }

        // 触发同步内容
        if (window.tinymce && typeof tinymce.triggerSave === 'function') {
            tinymce.triggerSave();
        }
        // 强制覆盖所有语言的富文本内容，避免隐藏Tab未同步
        (function ensureContentSync(){
            const langData = (function(){ try { return JSON.parse('{{ json_encode(enabled_langs()) }}'.replace(/&quot;/g,'"')); } catch(e){ return {codes:[]}; }})();
            (langData.codes||[]).forEach(function(code){
                const name = 'content['+code+']';
                const $ta = $('textarea[name="'+name+'"]');
                if ($ta.length) { fd.set(name, $ta.val()); }
            });
        })();
        $.ajax({
            url: "{{ route('lpadmin.blog.post.store') }}",
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
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


