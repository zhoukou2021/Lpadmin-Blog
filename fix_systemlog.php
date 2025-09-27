<?php

require_once 'vendor/autoload.php';

// 启动Laravel应用
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SystemLog组件修复脚本 ===\n";

use App\Models\LPadmin\Component;
use App\Services\LPadmin\ComponentManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

try {
    echo "\n1. 检查当前状态...\n";
    
    // 检查组件记录
    $component = Component::where('name', 'SystemLog')->first();
    if ($component) {
        echo "✓ 组件记录存在，状态: {$component->status}\n";
    } else {
        echo "✗ 组件记录不存在\n";
    }
    
    // 检查admin_logs表
    $tableExists = Schema::hasTable('admin_logs');
    echo "admin_logs表存在: " . ($tableExists ? "✓ 是" : "✗ 否") . "\n";
    
    // 检查权限
    $permissions = \App\Models\LPadmin\Rule::where('name', 'like', '%system-log%')->count();
    echo "SystemLog权限数量: {$permissions}\n";
    
    echo "\n2. 开始修复...\n";
    
    // 如果组件记录不存在或状态不正确，重新安装
    if (!$component || $component->status != Component::STATUS_INSTALLED) {
        echo "正在重新安装SystemLog组件...\n";
        
        // 先清理可能的残留数据
        if ($component) {
            echo "删除现有组件记录...\n";
            $component->delete();
        }
        
        // 确保数据表存在
        if (!$tableExists) {
            echo "运行迁移创建admin_logs表...\n";
            Artisan::call('migrate', [
                '--path' => 'app/Components/SystemLog/database/migrations',
                '--force' => true
            ]);
        }
        
        // 手动创建组件记录
        echo "创建组件记录...\n";
        $componentInfo = [
            'name' => 'SystemLog',
            'title' => '系统日志管理',
            'description' => '系统操作日志的查看、搜索、导出等功能',
            'version' => '1.0.0',
            'author' => 'LPadmin Team'
        ];
        
        $newComponent = Component::create([
            'name' => 'SystemLog',
            'title' => $componentInfo['title'],
            'description' => $componentInfo['description'],
            'version' => $componentInfo['version'],
            'author' => $componentInfo['author'],
            'config' => $componentInfo,
            'status' => Component::STATUS_INSTALLED,
            'installed_at' => now(),
        ]);
        
        echo "✓ 组件记录创建成功，ID: {$newComponent->id}\n";
        
        // 确保权限存在
        echo "检查和创建权限...\n";
        \App\Components\SystemLog\SystemLogComponent::install();
        
        // 注册路由
        echo "注册组件路由...\n";
        \App\Services\LPadmin\ComponentRouteManager::addComponentRouteConfig('SystemLog', [
            'name' => 'SystemLog',
            'middleware' => ['web', 'lpadmin.auth'],
            'enabled_check' => true
        ]);
        
        // 清除缓存
        echo "清除缓存...\n";
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        
        echo "✓ SystemLog组件修复完成\n";
        
    } else {
        echo "✓ 组件状态正常，无需修复\n";
    }
    
    echo "\n3. 验证修复结果...\n";
    
    // 重新检查状态
    $component = Component::where('name', 'SystemLog')->first();
    $tableExists = Schema::hasTable('admin_logs');
    $permissions = \App\Models\LPadmin\Rule::where('name', 'like', '%system-log%')->count();
    
    echo "组件记录: " . ($component ? "✓ 存在，状态: {$component->status}" : "✗ 不存在") . "\n";
    echo "admin_logs表: " . ($tableExists ? "✓ 存在" : "✗ 不存在") . "\n";
    echo "权限数量: {$permissions}\n";
    
    if ($component && $component->status == Component::STATUS_INSTALLED && $tableExists && $permissions > 0) {
        echo "\n🎉 SystemLog组件修复成功！\n";
    } else {
        echo "\n❌ 修复可能未完全成功，请检查上述状态\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ 修复过程中出现错误: " . $e->getMessage() . "\n";
    echo "错误详情: " . $e->getTraceAsString() . "\n";
}

echo "\n=== 修复完成 ===\n";
