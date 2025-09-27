<?php

// 简单的诊断脚本
echo "=== SystemLog组件诊断 ===\n\n";

// 检查数据库连接
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=lpadmin_a', 'lpadmin_a', 'lpadmin_a');
    echo "✓ 数据库连接成功\n";
} catch (Exception $e) {
    echo "✗ 数据库连接失败: " . $e->getMessage() . "\n";
    exit(1);
}

// 1. 检查components表
echo "\n1. 检查components表:\n";
try {
    $stmt = $pdo->query("SELECT * FROM lp_components WHERE name = 'SystemLog'");
    $component = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($component) {
        echo "✓ SystemLog组件记录存在\n";
        echo "  ID: {$component['id']}\n";
        echo "  Name: {$component['name']}\n";
        echo "  Status: {$component['status']}\n";
        echo "  Installed At: {$component['installed_at']}\n";
    } else {
        echo "✗ SystemLog组件记录不存在\n";
    }
} catch (Exception $e) {
    echo "✗ 检查components表失败: " . $e->getMessage() . "\n";
}

// 2. 检查admin_logs表
echo "\n2. 检查admin_logs表:\n";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'lp_admin_logs'");
    $tableExists = $stmt->fetch();

    if ($tableExists) {
        echo "✓ lp_admin_logs表存在\n";
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM lp_admin_logs");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "  记录数: {$count['count']}\n";
    } else {
        echo "✗ lp_admin_logs表不存在\n";
    }
} catch (Exception $e) {
    echo "✗ 检查admin_logs表失败: " . $e->getMessage() . "\n";
}

// 3. 检查SystemLog相关权限
echo "\n3. 检查SystemLog相关权限:\n";
try {
    $stmt = $pdo->query("SELECT * FROM lp_rules WHERE name LIKE '%system-log%'");
    $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($permissions) > 0) {
        echo "✓ SystemLog权限记录数: " . count($permissions) . "\n";
        foreach ($permissions as $perm) {
            echo "  - {$perm['name']} ({$perm['title']}) - 状态: {$perm['status']}\n";
        }
    } else {
        echo "✗ SystemLog权限记录不存在\n";
    }
} catch (Exception $e) {
    echo "✗ 检查权限失败: " . $e->getMessage() . "\n";
}

// 4. 检查日志相关菜单
echo "\n4. 检查日志相关菜单:\n";
try {
    $stmt = $pdo->query("SELECT * FROM lp_rules WHERE type = 1 AND title LIKE '%日志%'");
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($menus) > 0) {
        echo "✓ 日志相关菜单记录数: " . count($menus) . "\n";
        foreach ($menus as $menu) {
            echo "  - {$menu['title']} ({$menu['name']}) - 状态: {$menu['status']}\n";
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
    $stmt = $pdo->query("SELECT * FROM lp_migrations WHERE migration LIKE '%admin_logs%'");
    $migrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($migrations) > 0) {
        echo "✓ SystemLog相关迁移记录数: " . count($migrations) . "\n";
        foreach ($migrations as $migration) {
            echo "  - {$migration['migration']}\n";
        }
    } else {
        echo "✗ SystemLog相关迁移记录不存在\n";
    }
} catch (Exception $e) {
    echo "✗ 检查迁移记录失败: " . $e->getMessage() . "\n";
}

echo "\n=== 诊断完成 ===\n";
