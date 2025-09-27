<?php

require_once 'vendor/autoload.php';

// 启动Laravel应用
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== 组件事务管理测试 ===\n";

use App\Models\LPadmin\Component;
use App\Services\LPadmin\ComponentManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "\n1. 测试组件安装...\n";
    
    // 确保组件不存在
    $component = Component::where('name', 'SystemLog')->first();
    if ($component) {
        echo "组件已存在，先卸载...\n";
        $result = ComponentManager::uninstallComponent('SystemLog');
        echo "卸载结果: " . ($result ? "✅ 成功" : "❌ 失败") . "\n";
        sleep(1); // 等待一秒
    }
    
    echo "开始安装SystemLog组件...\n";
    $result = ComponentManager::installComponent('SystemLog');
    echo "安装结果: " . ($result ? "✅ 成功" : "❌ 失败") . "\n";
    
    // 验证安装结果
    $component = Component::where('name', 'SystemLog')->first();
    $tableExists = Schema::hasTable('admin_logs');
    $permissions = \App\Models\LPadmin\Rule::where('name', 'like', '%system-log%')->count();
    
    echo "验证结果:\n";
    echo "  组件记录: " . ($component ? "✅ 存在" : "❌ 不存在") . "\n";
    echo "  数据表: " . ($tableExists ? "✅ 存在" : "❌ 不存在") . "\n";
    echo "  权限数量: {$permissions}\n";
    
    if ($result && $component && $tableExists && $permissions > 0) {
        echo "✅ 安装测试通过\n";
    } else {
        echo "❌ 安装测试失败\n";
    }
    
    echo "\n2. 测试组件卸载...\n";
    
    echo "开始卸载SystemLog组件...\n";
    $result = ComponentManager::uninstallComponent('SystemLog');
    echo "卸载结果: " . ($result ? "✅ 成功" : "❌ 失败") . "\n";
    
    // 验证卸载结果
    $component = Component::where('name', 'SystemLog')->first();
    $tableExists = Schema::hasTable('admin_logs');
    $permissions = \App\Models\LPadmin\Rule::where('name', 'like', '%system-log%')->count();
    
    echo "验证结果:\n";
    echo "  组件记录: " . ($component ? "❌ 仍存在" : "✅ 已删除") . "\n";
    echo "  数据表: " . ($tableExists ? "❌ 仍存在" : "✅ 已删除") . "\n";
    echo "  权限数量: {$permissions}\n";
    
    if ($result && !$component && !$tableExists && $permissions == 0) {
        echo "✅ 卸载测试通过\n";
    } else {
        echo "❌ 卸载测试失败\n";
    }
    
    echo "\n3. 重新安装以恢复功能...\n";
    
    $result = ComponentManager::installComponent('SystemLog');
    echo "重新安装结果: " . ($result ? "✅ 成功" : "❌ 失败") . "\n";
    
} catch (Exception $e) {
    echo "\n❌ 测试过程中出现错误: " . $e->getMessage() . "\n";
    echo "错误详情: " . $e->getTraceAsString() . "\n";
}

echo "\n=== 测试完成 ===\n";
