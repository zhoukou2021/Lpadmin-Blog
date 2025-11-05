# DeepSeek API 自动生成博客文章 - 开发方案

## 一、需求分析

### 1.1 核心功能
- **自动生成文章**：根据关键词每天随机生成随机条数的文章
- **手动生成文章**：支持后台手动触发生成
- **系统配置管理**：在 deepseek 分组下配置相关参数
- **多语言支持**：根据系统语言生成多语言内容

### 1.2 文章要求
- 生成完整的文章信息（分类、标题、摘要、内容、SEO信息、标签等）
- 多语言内容存储为 JSON 格式
- 状态根据是否自动发布决定
- 结合分类名称、标签、关键词、衍生关键词生成

## 二、技术架构

### 2.1 系统配置设计
在 `options` 表中增加 `deepseek` 分组配置项：

| 配置项名称 | 类型 | 说明 | 默认值 |
|-----------|------|------|--------|
| deepseek_api_key | string | DeepSeek API Key | 空 |
| deepseek_auto_enabled | boolean | 是否开启自动生成 | false |
| deepseek_auto_publish | boolean | 是否自动发布 | false |
| deepseek_daily_count_min | integer | 每天最少生成条数 | 1 |
| deepseek_daily_count_max | integer | 每天最多生成条数 | 3 |
| deepseek_keywords | text | 关键词（每行一个） | 空 |
| deepseek_prompt_rules | textarea | 文章生成规则/提示词 | 默认规则 |
| deepseek_model | string | 使用的模型 | deepseek-chat |

### 2.2 数据库表设计
无需新建表，使用现有的：
- `options` 表：存储配置
- `blog_posts` 表：存储生成的文章
- `blog_categories` 表：选择分类
- `blog_tags` 表：选择/创建标签

### 2.3 核心组件设计

#### 2.3.1 Service 层
```
app/Services/DeepSeekService.php
- 负责与 DeepSeek API 交互
- 发送请求、处理响应、错误处理
```

#### 2.3.2 Generator 层
```
app/Services/Blog/ArticleGeneratorService.php
- 文章生成逻辑
- 多语言内容生成
- 分类、标签选择逻辑
- SEO 信息生成
```

#### 2.3.3 Command 层
```
app/Console/Commands/GenerateArticlesCommand.php
- 定时任务命令
- 手动触发命令
- 日志记录
```

#### 2.3.4 Controller 层（后台管理）
```
app/Http/Controllers/LPadmin/Blog/DeepSeekController.php
- 配置管理页面
- 手动生成文章接口
- 生成历史查看
```

## 三、详细开发步骤

### 阶段一：系统配置管理

#### 步骤 1.1：创建配置迁移
创建迁移文件为 deepseek 分组添加配置项：
- deepseek_api_key
- deepseek_auto_enabled
- deepseek_auto_publish
- deepseek_daily_count_min
- deepseek_daily_count_max
- deepseek_keywords
- deepseek_prompt_rules
- deepseek_model

#### 步骤 1.2：后台配置管理界面
在 LPadmin 后台添加配置管理页面：
- 路由：`/lpadmin/blog/deepseek/config`
- 视图：显示/编辑 deepseek 分组配置
- 表单验证：API Key 必填、数量范围验证等
- 创建默认规则

### 阶段二：DeepSeek API 对接

#### 步骤 2.1：创建 DeepSeekService
```php
class DeepSeekService
{
    // 发送生成请求
    public function generateContent(array $messages, string $model = 'deepseek-chat'): array
    
    // 构建提示词
    public function buildPrompt(string $keyword, string $categoryName, array $tags, string $rules): string
    
    // 解析响应内容
    public function parseResponse(string $response): array
}
```

#### 步骤 2.2：API 配置
- 使用 Guzzle HTTP 客户端
- 配置 API 基础 URL：`https://api.deepseek.com/v1/chat/completions`
- 处理 API 限流、错误重试

### 阶段三：文章生成逻辑

#### 步骤 3.1：创建 ArticleGeneratorService
```php
class ArticleGeneratorService
{
    // 生成单篇文章
    public function generateArticle(array $params): ?Post
    
    // 批量生成文章
    public function generateBatch(int $count): array
    
    // 选择分类
    private function selectCategory(): ?Category
    
    // 选择/创建标签
    private function selectOrCreateTags(string $keyword): Collection
    
    // 生成多语言内容
    private function generateMultilingualContent(string $keyword, string $categoryName, array $tags, array $languages): array
}
```

#### 步骤 3.2：多语言处理
- 获取系统启用的语言列表（如：zh, en, tw）
- 对每种语言调用 DeepSeek API 生成内容
- 组装为 JSON 格式：
```json
{
  "zh": "中文内容",
  "en": "English content",
  "tw": "繁體中文內容"
}
```

#### 步骤 3.3：内容生成规则
构建提示词模板：
```
你是一位专业的博客文章写作专家。请根据以下信息生成一篇高质量的博客文章：

分类：{category_name}
关键词：{keyword}
标签：{tag1}, {tag2}, ...

要求：
1. 文章标题要有吸引力，符合SEO优化
2. 摘要控制在150-200字，概括文章核心内容
3. 正文内容不少于800字，结构清晰，段落分明
4. SEO标题要与文章标题相关但可以略有不同
5. SEO关键词包含：{keyword} 及相关标签
6. SEO描述要在150字以内，突出文章价值
7. 标题中要包含具体的问题，内容要围绕这个具体问题回答

生成格式要求：
- 使用JSON格式返回
- 包含字段：title, summary, content, meta_title, meta_keywords, meta_description
- 内容要原创、专业、有实用价值

请开始生成文章...
```

### 阶段四：定时任务

#### 步骤 4.1：创建 Command
```php
php artisan make:command GenerateArticlesCommand
```

命令功能：
- 检查是否开启自动生成
- 随机生成数量（在 min-max 之间）
- 从关键词列表中随机选择关键词
- 调用 ArticleGeneratorService 生成文章
- 记录生成日志

#### 步骤 4.2：配置定时任务
在 `app/Console/Kernel.php` 中注册：
```php
protected function schedule(Schedule $schedule)
{
    // 每天凌晨 2 点执行
    $schedule->command('articles:generate')
             ->dailyAt('02:00')
             ->withoutOverlapping();
}
```

### 阶段五：手动生成功能

#### 步骤 5.1：后台控制器
创建 `DeepSeekController`：
- `index()`: 显示配置页面和手动生成按钮
- `store()`: 保存配置
- `generate()`: 手动触发生成（POST）
- `history()`: 查看生成历史

#### 步骤 5.2：手动生成接口
```
POST /lpadmin/blog/deepseek/generate
参数：
- count: 生成数量（可选，默认1）
- keywords: 指定关键词（可选，否则随机）
- category_id: 指定分类（可选，否则随机）
```

### 阶段六：错误处理与日志

#### 步骤 6.1：错误处理
- API 调用失败重试（最多3次）
- 生成失败记录到日志
- 部分成功部分失败的情况处理

#### 步骤 6.2：日志记录
记录内容：
- 生成时间
- 生成数量
- 成功/失败数量
- 使用的关键词
- 错误信息

## 四、API 调用示例

### DeepSeek API 请求格式
```php
POST https://api.deepseek.com/v1/chat/completions
Headers:
  Authorization: Bearer {api_key}
  Content-Type: application/json

Body:
{
  "model": "deepseek-chat",
  "messages": [
    {
      "role": "system",
      "content": "你是一位专业的博客文章写作专家..."
    },
    {
      "role": "user",
      "content": "请根据以下信息生成文章：\n分类：PHP\n关键词：Laravel框架..."
    }
  ],
  "temperature": 0.7,
  "max_tokens": 4000
}
```

### 响应解析
期望返回 JSON 格式：
```json
{
  "title": "文章标题",
  "summary": "文章摘要...",
  "content": "文章正文内容...",
  "meta_title": "SEO标题",
  "meta_keywords": "SEO关键词1,关键词2",
  "meta_description": "SEO描述..."
}
```

## 五、数据流程

```
1. 定时任务触发 / 手动触发
   ↓
2. 读取配置（关键词列表、数量范围等）
   ↓
3. 随机选择关键词和生成数量
   ↓
4. 循环生成文章（每次生成1篇）：
   ├─ 随机选择分类
   ├─ 根据关键词选择/创建标签
   ├─ 获取系统语言列表
   ├─ 为每种语言调用 DeepSeek API
   ├─ 解析 API 返回的内容
   ├─ 组装多语言 JSON 数据
   ├─ 生成 slug（如果为空则自动生成）
   ├─ 设置作者 ID = 1
   ├─ 设置状态（自动发布则为 published，否则为 draft）
   └─ 保存文章到数据库
   ↓
5. 记录生成日志
```

## 六、注意事项

### 6.1 API 限制
- DeepSeek API 可能有速率限制，需要控制请求频率
- 多语言生成时，需要为每种语言单独调用 API
- 建议添加请求间隔（如每次请求间隔 2-3 秒）

### 6.2 内容质量
- 提示词要详细、清晰
- 生成的 JSON 格式要严格验证
- 如果返回格式不正确，要记录错误并跳过

### 6.3 性能优化
- 批量生成时使用队列（可选）
- 生成过程中显示进度（手动生成时）
- 避免同时生成过多文章导致超时

### 6.4 安全性
- API Key 加密存储（可选）
- 限制手动生成的权限
- 防止恶意触发生成请求

## 七、测试计划

### 7.1 单元测试
- DeepSeekService API 调用测试
- ArticleGeneratorService 生成逻辑测试
- 多语言 JSON 组装测试

### 7.2 集成测试
- 完整生成流程测试
- 定时任务执行测试
- 错误处理测试

### 7.3 手动测试
- 配置页面功能测试
- 手动生成功能测试
- 生成的文章质量检查

## 八、开发时间估算

| 阶段 | 工作量 | 说明 |
|------|--------|------|
| 阶段一：系统配置 | 0.5天 | 迁移文件、后台页面 |
| 阶段二：API对接 | 1天 | Service 层开发、测试 |
| 阶段三：生成逻辑 | 2天 | 核心逻辑、多语言处理 |
| 阶段四：定时任务 | 0.5天 | Command 开发、调度配置 |
| 阶段五：手动生成 | 1天 | 后台控制器、界面 |
| 阶段六：错误处理 | 0.5天 | 日志、错误处理 |
| 测试与优化 | 1天 | 功能测试、性能优化 |
| **总计** | **6.5天** | |

## 九、后续优化建议

1. **队列化处理**：大批量生成时使用队列异步处理
2. **生成模板**：支持多种文章模板（技术、生活、教程等）
3. **内容审核**：生成后自动检查内容质量
4. **统计分析**：统计生成成功率、热门关键词等
5. **AI 优化建议**：根据现有文章优化生成内容

