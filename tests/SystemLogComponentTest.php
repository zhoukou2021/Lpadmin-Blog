<?php

/**
 * SystemLog组件事务冲突测试脚本
 * 
 * 使用方法：
 * php tests/SystemLogComponentTest.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Services\LPadmin\ComponentManager;
use App\Components\SystemLog\SystemLogComponent;
use App\Models\LPadmin\Rule;

class SystemLogComponentTest
{
    private $testResults = [];
    
    public function __construct()
    {
        // 初始化Laravel应用
        $app = require_once __DIR__ . '/../bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    }
    
    /**
     * 运行所有测试
     */
    public function runAllTests()
    {
        echo "🚀 开始SystemLog组件事务冲突测试\n";
        echo "=====================================\n\n";
        
        // 测试前清理
        $this->cleanup();
        
        // 运行测试
        $this->testInstallComponent();
        $this->testUninstallComponent();
        $this->testReinstallComponent();
        $this->testTransactionRollback();
        
        // 显示测试结果
        $this->showResults();
    }
    
    /**
     * 测试组件安装
     */
    private function testInstallComponent()
    {
        echo "📦 测试1：组件安装\n";
        
        try {
            // 确保组件未安装
            $this->cleanup();
            
            // 检查初始状态
            $initialState = [
                'table_exists' => Schema::hasTable('admin_logs'),
                'permissions_exist' => Rule::where('name', 'system-log')->exists(),
                'component_record' => DB::table('components')->where('name', 'SystemLog')->exists()
            ];
            
            echo "  初始状态: " . json_encode($initialState) . "\n";
            
            // 执行安装
            $result = ComponentManager::installComponent('SystemLog');
            
            // 检查安装后状态
            $finalState = [
                'install_result' => $result,
                'table_exists' => Schema::hasTable('admin_logs'),
                'permissions_exist' => Rule::where('name', 'system-log')->exists(),
                'component_record' => DB::table('components')->where('name', 'SystemLog')->exists(),
                'permission_count' => Rule::where('name', 'like', 'system-log%')->count()
            ];
            
            echo "  最终状态: " . json_encode($finalState) . "\n";
            
            // 验证结果
            $success = $result && 
                      $finalState['table_exists'] && 
                      $finalState['permissions_exist'] && 
                      $finalState['component_record'] &&
                      $finalState['permission_count'] >= 4; // 1个组权限 + 3个具体权限
            
            $this->testResults['install'] = [
                'success' => $success,
                'message' => $success ? '安装成功' : '安装失败',
                'details' => $finalState
            ];
            
            echo "  结果: " . ($success ? "✅ 通过" : "❌ 失败") . "\n\n";
            
        } catch (Exception $e) {
            $this->testResults['install'] = [
                'success' => false,
                'message' => '安装异常: ' . $e->getMessage(),
                'details' => ['exception' => $e->getTraceAsString()]
            ];
            echo "  结果: ❌ 异常 - " . $e->getMessage() . "\n\n";
        }
    }
    
    /**
     * 测试组件卸载
     */
    private function testUninstallComponent()
    {
        echo "🗑️  测试2：组件卸载\n";
        
        try {
            // 确保组件已安装
            if (!ComponentManager::isComponentInstalled('SystemLog')) {
                ComponentManager::installComponent('SystemLog');
            }
            
            // 检查卸载前状态
            $initialState = [
                'table_exists' => Schema::hasTable('admin_logs'),
                'permissions_exist' => Rule::where('name', 'system-log')->exists(),
                'component_record' => DB::table('components')->where('name', 'SystemLog')->exists()
            ];
            
            echo "  卸载前状态: " . json_encode($initialState) . "\n";
            
            // 执行卸载
            $result = ComponentManager::uninstallComponent('SystemLog');
            
            // 检查卸载后状态
            $finalState = [
                'uninstall_result' => $result,
                'table_exists' => Schema::hasTable('admin_logs'),
                'permissions_exist' => Rule::where('name', 'system-log')->exists(),
                'component_record' => DB::table('components')->where('name', 'SystemLog')->exists(),
                'permission_count' => Rule::where('name', 'like', 'system-log%')->count()
            ];
            
            echo "  最终状态: " . json_encode($finalState) . "\n";
            
            // 验证结果（卸载后权限应该被删除，但表可能保留）
            $success = $result && 
                      !$finalState['permissions_exist'] && 
                      !$finalState['component_record'] &&
                      $finalState['permission_count'] == 0;
            
            $this->testResults['uninstall'] = [
                'success' => $success,
                'message' => $success ? '卸载成功' : '卸载失败',
                'details' => $finalState
            ];
            
            echo "  结果: " . ($success ? "✅ 通过" : "❌ 失败") . "\n\n";
            
        } catch (Exception $e) {
            $this->testResults['uninstall'] = [
                'success' => false,
                'message' => '卸载异常: ' . $e->getMessage(),
                'details' => ['exception' => $e->getTraceAsString()]
            ];
            echo "  结果: ❌ 异常 - " . $e->getMessage() . "\n\n";
        }
    }
    
    /**
     * 测试重复安装
     */
    private function testReinstallComponent()
    {
        echo "🔄 测试3：重复安装\n";
        
        try {
            // 先安装一次
            ComponentManager::installComponent('SystemLog');
            
            // 再次安装
            $result = ComponentManager::installComponent('SystemLog');
            
            // 检查状态
            $finalState = [
                'reinstall_result' => $result,
                'table_exists' => Schema::hasTable('admin_logs'),
                'permissions_exist' => Rule::where('name', 'system-log')->exists(),
                'permission_count' => Rule::where('name', 'like', 'system-log%')->count()
            ];
            
            echo "  重复安装状态: " . json_encode($finalState) . "\n";
            
            // 重复安装应该成功，不应该产生重复数据
            $success = $result && 
                      $finalState['table_exists'] && 
                      $finalState['permissions_exist'] &&
                      $finalState['permission_count'] >= 4;
            
            $this->testResults['reinstall'] = [
                'success' => $success,
                'message' => $success ? '重复安装处理正确' : '重复安装处理失败',
                'details' => $finalState
            ];
            
            echo "  结果: " . ($success ? "✅ 通过" : "❌ 失败") . "\n\n";
            
        } catch (Exception $e) {
            $this->testResults['reinstall'] = [
                'success' => false,
                'message' => '重复安装异常: ' . $e->getMessage(),
                'details' => ['exception' => $e->getTraceAsString()]
            ];
            echo "  结果: ❌ 异常 - " . $e->getMessage() . "\n\n";
        }
    }
    
    /**
     * 测试事务回滚
     */
    private function testTransactionRollback()
    {
        echo "🔄 测试4：事务回滚\n";
        
        try {
            // 清理环境
            $this->cleanup();
            
            // 模拟安装过程中的异常
            DB::beginTransaction();
            
            try {
                // 运行迁移
                ComponentManager::runMigrations('SystemLog');
                
                // 检查迁移后状态
                $afterMigration = Schema::hasTable('admin_logs');
                echo "  迁移后表存在: " . ($afterMigration ? 'true' : 'false') . "\n";
                
                // 模拟权限创建失败
                throw new Exception('模拟权限创建失败');
                
            } catch (Exception $e) {
                DB::rollBack();
                echo "  事务已回滚: " . $e->getMessage() . "\n";
            }
            
            // 检查回滚后状态
            $finalState = [
                'table_exists' => Schema::hasTable('admin_logs'),
                'permissions_exist' => Rule::where('name', 'system-log')->exists(),
                'component_record' => DB::table('components')->where('name', 'SystemLog')->exists()
            ];
            
            echo "  回滚后状态: " . json_encode($finalState) . "\n";
            
            // 验证事务回滚是否正确（表应该不存在，权限应该不存在）
            $success = !$finalState['table_exists'] && 
                      !$finalState['permissions_exist'] && 
                      !$finalState['component_record'];
            
            $this->testResults['rollback'] = [
                'success' => $success,
                'message' => $success ? '事务回滚正确' : '事务回滚失败',
                'details' => $finalState
            ];
            
            echo "  结果: " . ($success ? "✅ 通过" : "❌ 失败") . "\n\n";
            
        } catch (Exception $e) {
            $this->testResults['rollback'] = [
                'success' => false,
                'message' => '回滚测试异常: ' . $e->getMessage(),
                'details' => ['exception' => $e->getTraceAsString()]
            ];
            echo "  结果: ❌ 异常 - " . $e->getMessage() . "\n\n";
        }
    }
    
    /**
     * 清理测试环境
     */
    private function cleanup()
    {
        try {
            // 删除组件记录
            DB::table('components')->where('name', 'SystemLog')->delete();
            
            // 删除权限
            Rule::where('name', 'like', 'system-log%')->forceDelete();
            
            // 删除表（如果存在）
            Schema::dropIfExists('admin_logs');
            
            // 清理迁移记录
            DB::table('migrations')
                ->where('migration', 'like', '%create_lp_admin_logs_table%')
                ->delete();
                
        } catch (Exception $e) {
            echo "清理环境时出错: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * 显示测试结果
     */
    private function showResults()
    {
        echo "📊 测试结果汇总\n";
        echo "=====================================\n";
        
        $totalTests = count($this->testResults);
        $passedTests = 0;
        
        foreach ($this->testResults as $testName => $result) {
            $status = $result['success'] ? '✅ 通过' : '❌ 失败';
            echo sprintf("%-15s: %s - %s\n", $testName, $status, $result['message']);
            
            if ($result['success']) {
                $passedTests++;
            }
        }
        
        echo "\n总计: {$passedTests}/{$totalTests} 个测试通过\n";
        
        if ($passedTests == $totalTests) {
            echo "🎉 所有测试通过！SystemLog组件事务冲突问题已修复。\n";
        } else {
            echo "⚠️  仍有测试失败，需要进一步调试。\n";
        }
    }
}

// 运行测试
if (php_sapi_name() === 'cli') {
    $test = new SystemLogComponentTest();
    $test->runAllTests();
}
