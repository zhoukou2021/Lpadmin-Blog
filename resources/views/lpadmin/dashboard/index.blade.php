<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LPadmin 仪表盘</title>
    <link rel="stylesheet" href="/static/admin/component/layui/css/layui.css" />
    <link rel="stylesheet" href="/static/admin/component/pear/css/pear.css" />
</head>
<body>
<style>
/* 仪表盘样式 */
.dashboard-container { padding: 15px; }
.stat-card {
    background: #fff;
    border: 1px solid #e6e6e6;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 10px;
    position: relative;
    transition: box-shadow 0.3s ease;
}
.stat-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.stat-card .stat-icon {
    font-size: 32px;
    color: var(--theme-color, #1E9FFF);
    float: right;
    margin-top: -5px;
}
.stat-card .stat-number {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 3px;
    color: #333;
}
.stat-card .stat-label {
    font-size: 12px;
    color: #666;
}

.system-info-card {
    background: #fff;
    border: 1px solid #e6e6e6;
    border-radius: 6px;
    margin-bottom: 10px;
}
.system-info-card .layui-card-header {
    background: #f8f9fa;
    color: #333;
    border-radius: 6px 6px 0 0;
    font-weight: bold;
    font-size: 14px;
    padding: 10px 15px;
}
.system-info-card .layui-card-header i {
    color: var(--theme-color, #1E9FFF);
    margin-right: 5px;
}
.info-item {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    border-bottom: 1px solid #f0f0f0;
    font-size: 12px;
}
.info-item:last-child { border-bottom: none; }
.info-label { color: #666; }
.info-value { color: #333; font-weight: 500; }

.chart-container {
    background: #fff;
    border: 1px solid #e6e6e6;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 10px;
}
.chart-container .chart-title {
    font-size: 14px;
    font-weight: bold;
    color: #333;
    margin-bottom: 15px;
    text-align: center;
}
.chart-container .chart-title i {
    color: var(--theme-color, #1E9FFF);
    margin-right: 5px;
}

.top-pages-card {
    background: #fff;
    border: 1px solid #e6e6e6;
    border-radius: 6px;
    margin-bottom: 10px;
}
.top-pages-card .layui-card-header {
    background: #f8f9fa;
    color: #333;
    border-radius: 6px 6px 0 0;
    font-weight: bold;
    font-size: 14px;
    padding: 10px 15px;
}
.top-pages-card .layui-card-header i {
    color: var(--theme-color, #1E9FFF);
    margin-right: 5px;
}
.top-pages-list {
    padding: 0;
    margin: 0;
    list-style: none;
}
.top-pages-item {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.3s ease;
}
.top-pages-item:last-child {
    border-bottom: none;
}
.top-pages-item:hover {
    background-color: #f8f9fa;
}
.top-pages-rank {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #f0f0f0;
    color: #666;
    font-size: 12px;
    font-weight: bold;
    margin-right: 10px;
    flex-shrink: 0;
}
.top-pages-item:nth-child(1) .top-pages-rank {
    background: #FFD700;
    color: #fff;
}
.top-pages-item:nth-child(2) .top-pages-rank {
    background: #C0C0C0;
    color: #fff;
}
.top-pages-item:nth-child(3) .top-pages-rank {
    background: #CD7F32;
    color: #fff;
}
.top-pages-content {
    flex: 1;
    min-width: 0;
}
.top-pages-title {
    font-size: 13px;
    color: #333;
    margin-bottom: 4px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.top-pages-title a {
    color: #333;
    text-decoration: none;
}
.top-pages-title a:hover {
    color: var(--theme-color, #1E9FFF);
}
.top-pages-meta {
    font-size: 11px;
    color: #999;
    display: flex;
    align-items: center;
    gap: 10px;
}
.top-pages-views {
    color: var(--theme-color, #1E9FFF);
    font-weight: 500;
}

/* 移动端响应式样式 */
@media (max-width: 768px) {
    .dashboard-container {
        padding: 10px;
    }
    
    /* 统计卡片移动端优化 */
    .stat-card {
        padding: 12px;
        margin-bottom: 8px;
    }
    .stat-card .stat-icon {
        font-size: 24px;
        margin-top: -3px;
    }
    .stat-card .stat-number {
        font-size: 20px;
        margin-bottom: 2px;
    }
    .stat-card .stat-label {
        font-size: 11px;
    }
    
    /* 图表容器移动端优化 */
    .chart-container {
        padding: 10px;
        margin-bottom: 10px;
    }
    .chart-container .chart-title {
        font-size: 13px;
        margin-bottom: 10px;
    }
    #trend-chart {
        height: 250px !important;
    }
    
    /* 访问量前十卡片移动端优化 */
    .top-pages-card {
        margin-bottom: 10px;
    }
    .top-pages-card .layui-card-header {
        font-size: 13px;
        padding: 8px 12px;
    }
    .top-pages-item {
        padding: 8px 12px;
    }
    .top-pages-rank {
        width: 20px;
        height: 20px;
        font-size: 11px;
    margin-right: 8px;
}
    .top-pages-title {
        font-size: 12px;
        margin-bottom: 3px;
    }
    .top-pages-meta {
        font-size: 10px;
        gap: 8px;
    }
    
    /* 系统信息卡片移动端优化 */
    .system-info-card .layui-card-header {
        font-size: 13px;
        padding: 8px 12px;
    }
    .system-info-card .layui-card-body {
        padding: 8px 12px !important;
    }
    .info-item {
        padding: 5px 0;
        font-size: 11px;
    }
}

/* 超小屏幕优化（小于480px） */
@media (max-width: 480px) {
    .dashboard-container {
        padding: 8px;
    }
    
    /* 统计卡片超小屏幕优化 */
    .stat-card {
        padding: 10px;
        margin-bottom: 6px;
    }
    .stat-card .stat-icon {
        font-size: 20px;
    }
    .stat-card .stat-number {
        font-size: 18px;
    }
    .stat-card .stat-label {
        font-size: 10px;
    }
    
    /* 图表超小屏幕优化 */
    .chart-container {
        padding: 8px;
    }
    .chart-container .chart-title {
        font-size: 12px;
        margin-bottom: 8px;
    }
    #trend-chart {
        height: 200px !important;
    }
    
    /* 访问量前十超小屏幕优化 */
    .top-pages-card .layui-card-header {
        font-size: 12px;
        padding: 6px 10px;
    }
    .top-pages-item {
        padding: 6px 10px;
    }
    .top-pages-rank {
        width: 18px;
        height: 18px;
        font-size: 10px;
        margin-right: 6px;
    }
    .top-pages-title {
        font-size: 11px;
    }
    .top-pages-meta {
        font-size: 9px;
        gap: 6px;
        flex-wrap: wrap;
    }
    
    /* 系统信息卡片超小屏幕优化 */
    .system-info-card .layui-card-header {
        font-size: 12px;
        padding: 6px 10px;
    }
    .system-info-card .layui-card-body {
        padding: 6px 10px !important;
    }
    .info-item {
        padding: 4px 0;
        font-size: 10px;
        flex-wrap: wrap;
    }
    .info-label, .info-value {
        font-size: 10px;
    }
}
</style>
<div class="pear-container dashboard-container">
    <!-- 数据统计卡片 -->
    <div class="layui-row layui-col-space10">
        <div class="layui-col-xs6 layui-col-md2">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="layui-icon layui-icon-group"></i>
                </div>
                <div class="stat-number" id="user-count">{{ $statistics['blog']['user_count'] ?? 0 }}</div>
                <div class="stat-label">用户数</div>
            </div>
        </div>
        <div class="layui-col-xs6 layui-col-md2">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="layui-icon layui-icon-file"></i>
                </div>
                <div class="stat-number" id="post-count">{{ $statistics['blog']['post_count'] ?? 0 }}</div>
                <div class="stat-label">博客数</div>
            </div>
        </div>
        <div class="layui-col-xs6 layui-col-md2">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="layui-icon layui-icon-praise"></i>
                </div>
                <div class="stat-number" id="like-count">{{ $statistics['blog']['like_count'] ?? 0 }}</div>
                <div class="stat-label">点赞数</div>
            </div>
        </div>
        <div class="layui-col-xs6 layui-col-md2">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="layui-icon layui-icon-dialogue"></i>
                </div>
                <div class="stat-number" id="comment-count">{{ $statistics['blog']['comment_count'] ?? 0 }}</div>
                <div class="stat-label">评论数</div>
            </div>
        </div>
        <div class="layui-col-xs6 layui-col-md2">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="layui-icon layui-icon-star"></i>
                </div>
                <div class="stat-number" id="favorite-count">{{ $statistics['blog']['favorite_count'] ?? 0 }}</div>
                <div class="stat-label">收藏数</div>
            </div>
        </div>
        <div class="layui-col-xs6 layui-col-md2">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="layui-icon layui-icon-eye"></i>
                </div>
                <div class="stat-number" id="total-views">{{ number_format($statistics['blog']['total_views'] ?? 0) }}</div>
                <div class="stat-label">总访问量</div>
            </div>
        </div>
    </div>

    <!-- 趋势图表和访问量前十 -->
    <div class="layui-row layui-col-space10">
        <div class="layui-col-xs12 layui-col-md8">
            <div class="chart-container">
                <div class="chart-title">
                    <i class="layui-icon layui-icon-chart"></i> 数据趋势图
                </div>
                <div id="trend-chart" style="height: 350px;"></div>
            </div>
        </div>
        <div class="layui-col-xs12 layui-col-md4">
            <div class="top-pages-card layui-card">
                <div class="layui-card-header">
                    <i class="layui-icon layui-icon-fire"></i> 访问量前十
                </div>
                <div class="layui-card-body" style="padding: 0;height: 350px;overflow-y: auto;">
                    <ul class="top-pages-list" id="top-pages-list">
                        <li class="top-pages-item" style="text-align: center; padding: 20px; color: #999;">
                            <i class="layui-icon layui-icon-loading layui-anim layui-anim-rotate layui-anim-loop"></i> 加载中...
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- 系统信息和开发环境 -->
    <div class="layui-row layui-col-space10">
        <div class="layui-col-xs12 layui-col-md4">
            <div class="system-info-card layui-card">
                <div class="layui-card-header">
                    <i class="layui-icon layui-icon-component"></i> 引用扩展
                </div>
                <div class="layui-card-body" style="padding: 10px 15px;">
                    <div class="info-item">
                        <span class="info-label">前端框架</span>
                        <span class="info-value">PearAdminLayui</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">UI组件</span>
                        <span class="info-value">Layui 2.8+</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">图表组件</span>
                        <span class="info-value">ECharts 5.0+</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">HTTP客户端</span>
                        <span class="info-value">Guzzle HTTP</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">认证组件</span>
                        <span class="info-value">Laravel Sanctum</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-xs12 layui-col-md4">
            <div class="system-info-card layui-card">
                <div class="layui-card-header">
                    <i class="layui-icon layui-icon-component"></i> 开发环境
                </div>
                <div class="layui-card-body" style="padding: 10px 15px;">
                    <div class="info-item">
                        <span class="info-label">数据库</span>
                        <span class="info-value">MySQL</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">缓存驱动</span>
                        <span class="info-value">{{ config('cache.default') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">队列驱动</span>
                        <span class="info-value">{{ config('queue.default') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">会话驱动</span>
                        <span class="info-value">{{ config('session.driver') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">调试模式</span>
                        <span class="info-value">{{ config('app.debug') ? '开启' : '关闭' }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-xs12 layui-col-md4">
            <div class="system-info-card layui-card">
                <div class="layui-card-header">
                    <i class="layui-icon layui-icon-engine"></i> 系统信息
                </div>
                <div class="layui-card-body" style="padding: 10px 15px;">
                    <div class="info-item">
                        <span class="info-label">系统名称</span>
                        <span class="info-value">LPadmin</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Laravel版本</span>
                        <span class="info-value">{{ app()->version() }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">PHP版本</span>
                        <span class="info-value">{{ PHP_VERSION }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">操作系统</span>
                        <span class="info-value">{{ PHP_OS }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">运行环境</span>
                        <span class="info-value">{{ app()->environment() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/static/admin/component/layui/layui.js"></script>
<script src="/static/admin/component/pear/pear.js"></script>
<script>
layui.use(['layer', 'element', 'echarts', 'count', 'jquery'], function() {
    const layer = layui.layer;
    const element = layui.element;
    const echarts = layui.echarts;
    const count = layui.count;
    const $ = layui.jquery;

    // CSRF Token配置
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // 数字动画效果
    count.up("user-count", {
        time: 2000,
        num: {{ $statistics['blog']['user_count'] ?? 0 }},
        bit: 0,
        regulator: 10
    });

    count.up("post-count", {
        time: 2000,
        num: {{ $statistics['blog']['post_count'] ?? 0 }},
        bit: 0,
        regulator: 10
    });

    count.up("like-count", {
        time: 2000,
        num: {{ $statistics['blog']['like_count'] ?? 0 }},
        bit: 0,
        regulator: 10
    });

    count.up("comment-count", {
        time: 2000,
        num: {{ $statistics['blog']['comment_count'] ?? 0 }},
        bit: 0,
        regulator: 10
    });

    count.up("favorite-count", {
        time: 2000,
        num: {{ $statistics['blog']['favorite_count'] ?? 0 }},
        bit: 0,
        regulator: 10
    });

    // 加载访问量前十页面
    function loadTopPages() {
        $.get('{{ route("lpadmin.dashboard.top_pages") }}', { lang: 'cn' }, function(res) {
            if (res.code === 0 && res.data) {
                const list = $('#top-pages-list');
                list.empty();
                
                if (res.data.length === 0) {
                    list.html('<li class="top-pages-item" style="text-align: center; padding: 20px; color: #999;">暂无数据</li>');
                    return;
                }
                
                res.data.forEach(function(page, index) {
                    const rank = index + 1;
                    const item = $('<li class="top-pages-item"></li>');
                    const blogUrl = '/' + page.slug;
                    item.html(
                        '<span class="top-pages-rank">' + rank + '</span>' +
                        '<div class="top-pages-content">' +
                        '<div class="top-pages-title">' +
                        '<a href="' + blogUrl + '" target="_blank" title="' + (page.title || '') + '">' + (page.title || '无标题') + '</a>' +
                        '</div>' +
                        '<div class="top-pages-meta">' +
                        '<span class="top-pages-views"><i class="layui-icon layui-icon-eye"></i> ' + (page.view_count || 0) + '</span>' +
                        '<span>' + (page.published_at || '') + '</span>' +
                        '</div>' +
                        '</div>'
                    );
                    list.append(item);
                });
            } else {
                $('#top-pages-list').html('<li class="top-pages-item" style="text-align: center; padding: 20px; color: #999;">加载失败</li>');
            }
        }).fail(function() {
            $('#top-pages-list').html('<li class="top-pages-item" style="text-align: center; padding: 20px; color: #999;">加载失败</li>');
        });
    }

    // 初始加载访问量前十页面
    loadTopPages();

    // 初始化趋势图表
    const trendChart = echarts.init(document.getElementById('trend-chart'));
    
    // 加载趋势数据
    function loadTrendData(days = 7) {
        $.get('{{ route("lpadmin.dashboard.trend_data") }}', { days: days }, function(res) {
            if (res.code === 0 && res.data) {
                const data = res.data;
                const option = {
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'cross',
                label: {
                    backgroundColor: '#6a7985'
                }
            }
        },
        legend: {
                        data: ['用户数', '点赞数', '评论数', '收藏数'],
                        top: 10
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
                        top: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            boundaryGap: false,
                        data: data.dates.map(function(date) {
                            // 格式化日期显示，只显示月-日
                            const d = new Date(date);
                            return (d.getMonth() + 1) + '-' + d.getDate();
                        })
        },
        yAxis: {
            type: 'value'
        },
        series: [
            {
                            name: '用户数',
                type: 'line',
                smooth: true,
                areaStyle: {
                    color: {
                        type: 'linear',
                        x: 0,
                        y: 0,
                        x2: 0,
                        y2: 1,
                        colorStops: [{
                                        offset: 0, color: 'rgba(30, 159, 255, 0.8)'
                        }, {
                                        offset: 1, color: 'rgba(30, 159, 255, 0.1)'
                        }]
                    }
                },
                            data: data.user,
                            itemStyle: {
                                color: '#1E9FFF'
                            }
            },
            {
                            name: '点赞数',
                type: 'line',
                smooth: true,
                areaStyle: {
                    color: {
                        type: 'linear',
                        x: 0,
                        y: 0,
                        x2: 0,
                        y2: 1,
                        colorStops: [{
                            offset: 0, color: 'rgba(245, 87, 108, 0.8)'
                        }, {
                            offset: 1, color: 'rgba(245, 87, 108, 0.1)'
                        }]
                    }
                },
                            data: data.like,
                            itemStyle: {
                                color: '#F5576C'
                            }
            },
            {
                            name: '评论数',
                type: 'line',
                smooth: true,
                areaStyle: {
                    color: {
                        type: 'linear',
                        x: 0,
                        y: 0,
                        x2: 0,
                        y2: 1,
                        colorStops: [{
                            offset: 0, color: 'rgba(67, 233, 123, 0.8)'
                        }, {
                            offset: 1, color: 'rgba(67, 233, 123, 0.1)'
                        }]
                    }
                },
                            data: data.comment,
                            itemStyle: {
                                color: '#43E97B'
                            }
                        },
                        {
                            name: '收藏数',
                            type: 'line',
                            smooth: true,
                            areaStyle: {
                                color: {
                                    type: 'linear',
                                    x: 0,
                                    y: 0,
                                    x2: 0,
                                    y2: 1,
                                    colorStops: [{
                                        offset: 0, color: 'rgba(255, 193, 7, 0.8)'
                                    }, {
                                        offset: 1, color: 'rgba(255, 193, 7, 0.1)'
                                    }]
                                }
                            },
                            data: data.favorite,
                    itemStyle: {
                                color: '#FFC107'
                }
            }
        ]
    };
                trendChart.setOption(option);
            } else {
                layer.msg('加载趋势数据失败', {icon: 2});
            }
        }).fail(function() {
            layer.msg('加载趋势数据失败', {icon: 2});
        });
    }

    // 初始加载7天数据
    loadTrendData(7);

    // 响应式图表
    function resizeChart() {
        if (trendChart) {
        trendChart.resize();
        }
    }
    
    window.addEventListener('resize', resizeChart);
    
    // 移动端横竖屏切换时重新调整图表
    window.addEventListener('orientationchange', function() {
        setTimeout(resizeChart, 100);
    });
});
</script>
</body>
</html>
