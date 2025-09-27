<?php

require_once 'vendor/autoload.php';

// 启动Laravel应用
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== 检查SystemLog组件状态 ===\n";

// 1. 检查组件表记录
echo "\n1. 检查组件表记录:\n";
try {
    $component = \App\Models\LPadmin\Component::where('name', 'SystemLog')->first();
    if ($component) {
        echo "✓ 组件记录存在\n";
        echo "  ID: {$component->id}\n";
        echo "  Name: {$component->name}\n";
        echo "  Status: {$component->status} ({$component->status_label})\n";
        echo "  Installed At: " . ($component->installed_at ? $component->installed_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
    } else {
        echo "✗ 组件记录不存在\n";
    }
} catch (Exception $e) {
    echo "✗ 检查组件记录失败: " . $e->getMessage() . "\n";
}

// 2. 检查admin_logs表
echo "\n2. 检查admin_logs表:\n";
try {
    $tableExists = \Illuminate\Support\Facades\Schema::hasTable('admin_logs');
    echo "admin_logs表存在: " . ($tableExists ? "✓ 是" : "✗ 否") . "\n";
    
    if ($tableExists) {
        $count = \App\Components\SystemLog\Models\AdminLog::count();
        echo "admin_logs表记录数: {$count}\n";
    }
} catch (Exception $e) {
    echo "✗ admin_logs表检查失败: " . $e->getMessage() . "\n";
}

// 3. 检查SystemLog相关权限
echo "\n3. 检查SystemLog相关权限:\n";
try {
    $permissions = \App\Models\LPadmin\Rule::where('name', 'like', '%system-log%')->get();
    if ($permissions->count() > 0) {
        echo "✓ SystemLog权限记录数: {$permissions->count()}\n";
        foreach ($permissions as $perm) {
            echo "  - {$perm->name} ({$perm->title}) - 状态: {$perm->status}\n";
        }
    } else {
        echo "✗ SystemLog权限记录不存在\n";
    }
} catch (Exception $e) {
    echo "✗ 检查权限失败: " . $e->getMessage() . "\n";
}

// 4. 检查菜单记录
echo "\n4. 检查日志相关菜单:\n";
try {
    $menus = \App\Models\LPadmin\Rule::where('type', 1)->where('title', 'like', '%日志%')->get();
    if ($menus->count() > 0) {
        echo "✓ 日志相关菜单记录数: {$menus->count()}\n";
        foreach ($menus as $menu) {
            echo "  - {$menu->title} ({$menu->name}) - 状态: {$menu->status}\n";
        }
    } else {
        echo "✗ 日志相关菜单记录不存在\n";
    }
} catch (Exception $e) {
    echo "✗ 检查菜单失败: " . $e->getMessage() . "\n";
}

// 5. 检查迁移记录
echo "\n5. 检查迁移记录:\n";
try {
    $migrations = \Illuminate\Support\Facades\DB::table('migrations')
        ->where('migration', 'like', '%admin_logs%')
        ->orWhere('migration', 'like', '%SystemLog%')
        ->get();
    
    if ($migrations->count() > 0) {
        echo "✓ SystemLog相关迁移记录数: {$migrations->count()}\n";
        foreach ($migrations as $migration) {
            echo "  - {$migration->migration}\n";
        }
    } else {
        echo "✗ SystemLog相关迁移记录不存在\n";
    }
} catch (Exception $e) {
    echo "✗ 检查迁移记录失败: " . $e->getMessage() . "\n";
}

// 6. 检查组件文件结构
echo "\n6. 检查组件文件结构:\n";
$componentPath = base_path('app/Components/SystemLog');
echo "组件路径: {$componentPath}\n";

$files = [
    'component.json' => 'component.json',
    'SystemLogComponent.php' => 'SystemLogComponent.php',
    'Controllers' => 'Controllers',
    'routes/web.php' => 'routes/web.php',
    'database/migrations' => 'database/migrations',
    'Models/AdminLog.php' => 'Models/AdminLog.php'
];

foreach ($files as $file => $desc) {
    $fullPath = $componentPath . '/' . $file;
    $exists = file_exists($fullPath);
    echo "  {$desc}: " . ($exists ? "✓ 存在" : "✗ 不存在") . "\n";
}

echo "\n=== 检查完成 ===\n";
