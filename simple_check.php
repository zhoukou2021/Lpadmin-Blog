<?php

echo "=== SystemLog组件状态检查 ===\n";

// 检查组件文件结构
$componentPath = __DIR__ . '/app/Components/SystemLog';
echo "\n1. 检查组件文件结构:\n";
echo "组件路径: {$componentPath}\n";

$files = [
    'component.json',
    'SystemLogComponent.php',
    'Controllers',
    'routes/web.php',
    'database/migrations',
    'Models/AdminLog.php'
];

foreach ($files as $file) {
    $fullPath = $componentPath . '/' . $file;
    $exists = file_exists($fullPath);
    echo ($exists ? "✓" : "✗") . " {$file}\n";
}

// 检查component.json内容
echo "\n2. 检查component.json内容:\n";
$configFile = $componentPath . '/component.json';
if (file_exists($configFile)) {
    $config = json_decode(file_get_contents($configFile), true);
    echo "✓ component.json存在\n";
    echo "  Name: " . ($config['name'] ?? 'N/A') . "\n";
    echo "  Title: " . ($config['title'] ?? 'N/A') . "\n";
    echo "  Version: " . ($config['version'] ?? 'N/A') . "\n";
} else {
    echo "✗ component.json不存在\n";
}

// 检查迁移文件
echo "\n3. 检查迁移文件:\n";
$migrationsPath = $componentPath . '/database/migrations';
if (is_dir($migrationsPath)) {
    $migrations = glob($migrationsPath . '/*.php');
    echo "✓ 迁移目录存在\n";
    echo "  迁移文件数: " . count($migrations) . "\n";
    foreach ($migrations as $migration) {
        echo "  - " . basename($migration) . "\n";
    }
} else {
    echo "✗ 迁移目录不存在\n";
}

// 检查控制器
echo "\n4. 检查控制器:\n";
$controllerPath = $componentPath . '/Controllers/SystemLogController.php';
if (file_exists($controllerPath)) {
    echo "✓ SystemLogController.php存在\n";
} else {
    echo "✗ SystemLogController.php不存在\n";
}

// 检查路由文件
echo "\n5. 检查路由文件:\n";
$routesPath = $componentPath . '/routes/web.php';
if (file_exists($routesPath)) {
    echo "✓ routes/web.php存在\n";
    echo "  文件大小: " . filesize($routesPath) . " bytes\n";
} else {
    echo "✗ routes/web.php不存在\n";
}

echo "\n=== 检查完成 ===\n";
