<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DeepSeek AI 文章生成配置</title>
    <link rel="stylesheet" href="/static/admin/component/pear/css/pear.css" />
    <link rel="stylesheet" href="/static/admin/css/reset.css" />
    <style>
        .config-form-item {
            margin-bottom: 20px;
        }
        .config-form-item label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .config-form-item .description {
            font-size: 12px;
            color: #999;
            margin-top: 4px;
        }
        .generate-section {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #e6e6e6;
        }
        .generate-form {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
        }
        .result-section {
            margin-top: 20px;
            display: none;
        }
        .result-success {
            background: #f6ffed;
            border: 1px solid #b7eb8f;
            color: #52c41a;
            padding: 12px;
            border-radius: 4px;
        }
        .result-error {
            background: #fff2f0;
            border: 1px solid #ffccc7;
            color: #ff4d4f;
            padding: 12px;
            border-radius: 4px;
        }
        .log-info {
            color: #1890ff;
        }
        .log-success {
            color: #52c41a;
        }
        .log-error {
            color: #ff4d4f;
        }
        #logs-content {
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            max-height: 400px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.8;
        }
        #logs-content::-webkit-scrollbar {
            width: 8px;
        }
        #logs-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        #logs-content::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        #logs-content::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        .loading-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
        }
        .loading-content .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #1890ff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="pear-container">

    <div class="layui-card">
        <div class="layui-card-header">
            <h3>DeepSeek AI 文章生成配置</h3>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" id="config-form">
                <div class="config-form-item">
                    <label>DeepSeek API Key <span style="color:red">*</span></label>
                    <input type="text" name="deepseek_api_key" placeholder="请输入 DeepSeek API Key" class="layui-input" value="{{ $options->where('name', 'deepseek_api_key')->first()->value ?? '' }}">
                    <div class="description">DeepSeek API 密钥，用于调用 AI 生成接口</div>
                </div>

                <div class="config-form-item">
                    <label>
                        <input type="checkbox" name="deepseek_auto_enabled" value="1" lay-skin="switch" lay-text="开启|关闭" {{ ($options->where('name', 'deepseek_auto_enabled')->first()->value ?? '0') === '1' ? 'checked' : '' }}>
                        开启自动生成
                    </label>
                    <div class="description">是否开启每天自动生成文章</div>
                </div>

                <div class="config-form-item">
                    <label>
                        <input type="checkbox" name="deepseek_auto_publish" value="1" lay-skin="switch" lay-text="开启|关闭" {{ ($options->where('name', 'deepseek_auto_publish')->first()->value ?? '0') === '1' ? 'checked' : '' }}>
                        自动发布
                    </label>
                    <div class="description">生成的文章是否自动发布（否则保存为草稿）</div>
                </div>

                <div class="layui-row layui-col-space15">
                    <div class="layui-col-md6">
                        <div class="config-form-item">
                            <label>每天最少生成条数</label>
                            <input type="number" name="deepseek_daily_count_min" placeholder="最少生成条数" class="layui-input" min="1" value="{{ $options->where('name', 'deepseek_daily_count_min')->first()->value ?? '1' }}">
                            <div class="description">每天自动生成文章的最少数量</div>
                        </div>
                    </div>
                    <div class="layui-col-md6">
                        <div class="config-form-item">
                            <label>每天最多生成条数</label>
                            <input type="number" name="deepseek_daily_count_max" placeholder="最多生成条数" class="layui-input" min="1" value="{{ $options->where('name', 'deepseek_daily_count_max')->first()->value ?? '3' }}">
                            <div class="description">每天自动生成文章的最多数量</div>
                        </div>
                    </div>
                </div>

                <div class="config-form-item">
                    <label>关键词列表 <span style="color:red">*</span></label>
                    <textarea name="deepseek_keywords" placeholder="每行一个关键词，例如：&#10;Laravel框架&#10;PHP开发&#10;前端技术" class="layui-textarea" rows="8">{{ $options->where('name', 'deepseek_keywords')->first()->value ?? '' }}</textarea>
                    <div class="description">用于生成文章的关键词，每行一个</div>
                </div>

                <div class="config-form-item">
                    <label>生成规则/提示词</label>
                    <textarea name="deepseek_prompt_rules" placeholder="文章生成的提示词模板" class="layui-textarea" rows="15">{{ $options->where('name', 'deepseek_prompt_rules')->first()->value ?? '' }}</textarea>
                    <div class="description">文章生成的提示词模板，用于指导 AI 生成内容。可使用占位符：{category_name}、{keyword}、{tags}</div>
                </div>

                <div class="config-form-item">
                    <label>AI 模型</label>
                    <input type="text" name="deepseek_model" placeholder="deepseek-chat" class="layui-input" value="{{ $options->where('name', 'deepseek_model')->first()->value ?? 'deepseek-chat' }}">
                    <div class="description">使用的 DeepSeek 模型名称</div>
                </div>

                <div class="layui-form-item">
                    <button type="submit" class="pear-btn pear-btn-primary" lay-submit lay-filter="save-config">
                        <i class="layui-icon layui-icon-ok"></i> 保存配置
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 手动生成区域 -->
    <div class="layui-card generate-section">
        <div class="layui-card-header">
            <h3>手动生成文章</h3>
        </div>
        <div class="layui-card-body">
            <form class="layui-form generate-form" id="generate-form">
                <div class="layui-row layui-col-space15">
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">生成数量</label>
                            <div class="layui-input-block">
                                <input type="number" name="count" placeholder="生成数量" class="layui-input" min="1" max="10" value="1">
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">关键词（可选）</label>
                            <div class="layui-input-block">
                                <input type="text" name="keyword" placeholder="留空则随机选择" class="layui-input">
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">分类（可选）</label>
                            <div class="layui-input-block">
                                <select name="category_id" class="layui-input" lay-search>
                                    <option value="">随机选择</option>
                                    @php
                                        $categories = \App\Models\Blog\Category::where('visible', true)->get();
                                    @endphp
                                    @foreach($categories as $category)
                                        @php
                                            $categoryName = is_array($category->name) 
                                                ? ($category->name['zh'] ?? (array_values($category->name)[0] ?? ''))
                                                : ($category->name ?? '');
                                        @endphp
                                        <option value="{{ $category->id }}">{{ $categoryName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <button type="submit" class="pear-btn pear-btn-success" lay-submit lay-filter="generate-articles">
                        <i class="layui-icon layui-icon-play"></i> 开始生成
                    </button>
                </div>
            </form>

            <div class="result-section" id="generate-result"></div>
            
            <!-- 生成日志区域 -->
            <div class="result-section" id="generate-logs" style="display:none; margin-top: 20px;">
                <div style="font-weight: 600; margin-bottom: 10px; color: #333;">生成过程日志：</div>
                <div id="logs-content" style="background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px; padding: 15px; max-height: 400px; overflow-y: auto; font-family: 'Courier New', monospace; font-size: 12px; line-height: 1.8;"></div>
            </div>
        </div>
    </div>

    <!-- 加载遮罩 -->
    <div class="loading-overlay" id="loading-overlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <div>正在生成文章，请稍候...</div>
            <div style="margin-top: 10px; font-size: 12px; color: #999;">生成过程可能需要1-3分钟，请耐心等待</div>
        </div>
    </div>

    <script src="/static/admin/component/layui/layui.js?v=2.8.12"></script>
    <script>
        layui.use(['form', 'layer'], function(){
            var form = layui.form;
            var layer = layui.layer;
            var $ = layui.$;

            // 保存配置
            form.on('submit(save-config)', function(data){
                var formData = data.field;
                
                // 处理开关值
                formData.deepseek_auto_enabled = formData.deepseek_auto_enabled === '1' ? true : false;
                formData.deepseek_auto_publish = formData.deepseek_auto_publish === '1' ? true : false;

                $.ajax({
                    url: '{{ route("lpadmin.blog.deepseek.config.store") }}',
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res){
                        if(res.code === 0){
                            layer.msg('配置保存成功', {icon: 1});
                        } else {
                            layer.msg(res.message || '保存失败', {icon: 2});
                        }
                    },
                    error: function(xhr){
                        var msg = '保存失败';
                        if(xhr.responseJSON && xhr.responseJSON.message){
                            msg = xhr.responseJSON.message;
                        }
                        layer.msg(msg, {icon: 2});
                    }
                });
                return false;
            });

            // 生成文章（简化版本）
            form.on('submit(generate-articles)', function(data){
                var formData = data.field;
                
                if(!formData.count || formData.count < 1){
                    layer.msg('请输入有效的生成数量', {icon: 2});
                    return false;
                }

                // 立即显示成功提示
                layer.msg('任务创建成功，请稍后刷新博客列表查看。', {icon: 1, time: 3000});
                
                // 重置表单
                form.val('generate-articles', {
                    count: '',
                    keyword: '',
                    category_id: ''
                });

                // 异步提交请求（不等待响应）
                $.ajax({
                    url: '{{ route("lpadmin.blog.deepseek.generate") }}',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(res){
                        // 静默处理成功响应，不显示任何提示
                    },
                    error: function(xhr){
                        // 静默处理错误，不显示任何提示
                        // 如果需要，可以在控制台记录错误
                        console.error('生成任务提交失败:', xhr);
                    }
                });
                
                return false;
            });
        });
    </script>
</body>
</html>

