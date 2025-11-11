#!/bin/bash

###############################################################################
# 自动生成博客文章脚本 - 适用于宝塔计划任务
# 
# 使用方法：
# 1. 修改下面的配置项（项目路径、PHP路径等）
# 2. 在宝塔面板 -> 计划任务 -> 添加Shell脚本
# 3. 设置执行周期（建议：每天凌晨2点）
# 4. 脚本路径填写：/path/to/your/project/scripts/generate-articles.sh
###############################################################################

# ==================== 配置区域 ====================
# 项目根目录（请修改为实际路径）
PROJECT_PATH="/www/wwwroot/your-domain.com"

# PHP 路径（宝塔默认PHP路径，根据实际版本修改）
# 常见路径：
# PHP 7.4: /www/server/php/74/bin/php
# PHP 8.0: /www/server/php/80/bin/php
# PHP 8.1: /www/server/php/81/bin/php
# PHP 8.2: /www/server/php/82/bin/php
# PHP 8.3: /www/server/php/83/bin/php
PHP_PATH="/www/server/php/82/bin/php"

# 日志文件路径（可选，不设置则不记录日志）
LOG_FILE="${PROJECT_PATH}/storage/logs/generate-articles-cron.log"

# 生成文章数量（可选，留空则使用后台配置的随机数量）
# COUNT="1"

# 指定关键词（可选，留空则随机选择）
# KEYWORD=""

# 指定分类ID（可选，留空则随机选择）
# CATEGORY_ID=""

# ==================== 脚本开始 ====================

# 记录开始时间
START_TIME=$(date '+%Y-%m-%d %H:%M:%S')

# 检查项目目录是否存在
if [ ! -d "$PROJECT_PATH" ]; then
    echo "[$START_TIME] 错误: 项目目录不存在: $PROJECT_PATH" >&2
    exit 1
fi

# 切换到项目目录
cd "$PROJECT_PATH" || {
    echo "[$START_TIME] 错误: 无法切换到项目目录: $PROJECT_PATH" >&2
    exit 1
}

# 检查 PHP 是否存在
if [ ! -f "$PHP_PATH" ]; then
    echo "[$START_TIME] 错误: PHP 路径不存在: $PHP_PATH" >&2
    echo "[$START_TIME] 提示: 请检查 PHP 路径配置，或使用 which php 查找 PHP 路径" >&2
    exit 1
fi

# 检查 artisan 文件是否存在
if [ ! -f "$PROJECT_PATH/artisan" ]; then
    echo "[$START_TIME] 错误: artisan 文件不存在: $PROJECT_PATH/artisan" >&2
    exit 1
fi

# 构建命令
CMD="$PHP_PATH artisan articles:generate"

# 添加可选参数
if [ -n "$COUNT" ]; then
    CMD="$CMD --count=$COUNT"
fi

if [ -n "$KEYWORD" ]; then
    CMD="$CMD --keyword=\"$KEYWORD\""
fi

if [ -n "$CATEGORY_ID" ]; then
    CMD="$CMD --category=$CATEGORY_ID"
fi

# 执行命令并记录输出
if [ -n "$LOG_FILE" ]; then
    # 创建日志目录（如果不存在）
    LOG_DIR=$(dirname "$LOG_FILE")
    mkdir -p "$LOG_DIR"
    
    # 执行命令并记录日志
    {
        echo "=========================================="
        echo "执行时间: $START_TIME"
        echo "项目路径: $PROJECT_PATH"
        echo "PHP 路径: $PHP_PATH"
        echo "执行命令: $CMD"
        echo "----------------------------------------"
        eval $CMD 2>&1
        EXIT_CODE=$?
        echo "----------------------------------------"
        echo "退出代码: $EXIT_CODE"
        echo "结束时间: $(date '+%Y-%m-%d %H:%M:%S')"
        echo "=========================================="
        echo ""
    } >> "$LOG_FILE" 2>&1
    
    # 如果执行失败，也在标准错误输出
    if [ $EXIT_CODE -ne 0 ]; then
        echo "[$START_TIME] 错误: 文章生成失败，退出代码: $EXIT_CODE" >&2
        echo "[$START_TIME] 详细日志请查看: $LOG_FILE" >&2
    fi
    
    exit $EXIT_CODE
else
    # 不记录日志，直接输出
    eval $CMD
    exit $?
fi

