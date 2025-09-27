<?php

/**
 * SystemLog组件简化测试脚本
 */

require_once __DIR__ . '/vendor/autoload.php';

// 启动Laravel应用
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\LPadmin\ComponentManager;
use App\Models\LPadmin\Rule;

echo "🚀 SystemLog组件事务冲突测试\n";
echo "=====================================\n\n";

// 测试1：检查当前状态
echo "📋 当前状态检查:\n";
$currentState = [
    'admin_logs_table' => Schema::hasTable('admin_logs'),
    'system_log_permissions' => Rule::where('name', 'system-log')->exists(),
    'component_record' => DB::table('components')->where('name', 'SystemLog')->exists(),
    'permission_count' => Rule::where('name', 'like', 'system-log%')->count()
];

foreach ($currentState as $key => $value) {
    echo "  {$key}: " . (is_bool($value) ? ($value ? 'true' : 'false') : $value) . "\n";
}
echo "\n";

// 测试2：尝试安装组件
echo "📦 测试组件安装:\n";
try {
    $installResult = ComponentManager::installComponent('SystemLog');
    echo "  安装结果: " . ($installResult ? '成功' : '失败') . "\n";
    
    // 检查安装后状态
    $afterInstall = [
        'admin_logs_table' => Schema::hasTable('admin_logs'),
        'system_log_permissions' => Rule::where('name', 'system-log')->exists(),
        'component_record' => DB::table('components')->where('name', 'SystemLog')->exists(),
        'permission_count' => Rule::where('name', 'like', 'system-log%')->count()
    ];
    
    echo "  安装后状态:\n";
    foreach ($afterInstall as $key => $value) {
        echo "    {$key}: " . (is_bool($value) ? ($value ? 'true' : 'false') : $value) . "\n";
    }
    
} catch (Exception $e) {
    echo "  安装异常: " . $e->getMessage() . "\n";
    echo "  错误详情: " . $e->getFile() . ':' . $e->getLine() . "\n";
}
echo "\n";

// 测试3：检查数据库表结构
if (Schema::hasTable('admin_logs')) {
    echo "📊 admin_logs表结构检查:\n";
    $columns = Schema::getColumnListing('admin_logs');
    echo "  字段: " . implode(', ', $columns) . "\n";
    
    // 检查外键约束
    try {
        $foreignKeys = DB::select("
            SELECT 
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM 
                INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE 
                TABLE_NAME = 'admin_logs' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        if (!empty($foreignKeys)) {
            echo "  外键约束:\n";
            foreach ($foreignKeys as $fk) {
                echo "    {$fk->COLUMN_NAME} -> {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
            }
        } else {
            echo "  外键约束: 无\n";
        }
    } catch (Exception $e) {
        echo "  外键检查失败: " . $e->getMessage() . "\n";
    }
}
echo "\n";

// 测试4：尝试卸载组件
echo "🗑️  测试组件卸载:\n";
try {
    $uninstallResult = ComponentManager::uninstallComponent('SystemLog');
    echo "  卸载结果: " . ($uninstallResult ? '成功' : '失败') . "\n";
    
    // 检查卸载后状态
    $afterUninstall = [
        'admin_logs_table' => Schema::hasTable('admin_logs'),
        'system_log_permissions' => Rule::where('name', 'system-log')->exists(),
        'component_record' => DB::table('components')->where('name', 'SystemLog')->exists(),
        'permission_count' => Rule::where('name', 'like', 'system-log%')->count()
    ];
    
    echo "  卸载后状态:\n";
    foreach ($afterUninstall as $key => $value) {
        echo "    {$key}: " . (is_bool($value) ? ($value ? 'true' : 'false') : $value) . "\n";
    }
    
} catch (Exception $e) {
    echo "  卸载异常: " . $e->getMessage() . "\n";
    echo "  错误详情: " . $e->getFile() . ':' . $e->getLine() . "\n";
}
echo "\n";

// 测试5：重新安装测试
echo "🔄 测试重新安装:\n";
try {
    $reinstallResult = ComponentManager::installComponent('SystemLog');
    echo "  重新安装结果: " . ($reinstallResult ? '成功' : '失败') . "\n";
    
    // 最终状态检查
    $finalState = [
        'admin_logs_table' => Schema::hasTable('admin_logs'),
        'system_log_permissions' => Rule::where('name', 'system-log')->exists(),
        'component_record' => DB::table('components')->where('name', 'SystemLog')->exists(),
        'permission_count' => Rule::where('name', 'like', 'system-log%')->count()
    ];
    
    echo "  最终状态:\n";
    foreach ($finalState as $key => $value) {
        echo "    {$key}: " . (is_bool($value) ? ($value ? 'true' : 'false') : $value) . "\n";
    }
    
} catch (Exception $e) {
    echo "  重新安装异常: " . $e->getMessage() . "\n";
    echo "  错误详情: " . $e->getFile() . ':' . $e->getLine() . "\n";
}

echo "\n✅ 测试完成！\n";
