<?php

namespace App\Http\Controllers\LPadmin\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog\Category;

class CategoryController extends Controller
{
    public function select(Request $request)
    {
        $query = Category::query()->orderByDesc('sort')->orderBy('id');
        if ($request->filled('visible')) {
            $query->where('visible', (bool)$request->visible);
        }
        
        // 获取所有数据（不分页），用于构建树状结构
        $allCategories = $query->get();

        $data = $allCategories->map(function(Category $c){
            return [
                'id' => $c->id,
                'parent_id' => $c->parent_id ?? 0,
                'name' => $c->name['cn'] ?? (array_values($c->name)[0] ?? ''),
                'slug' => $c->slug ?? '',
                'visible' => $c->visible ? 1 : 0,
                'sort' => $c->sort,
                'created_at' => (string)$c->created_at,
            ];
        })->toArray();

        // 构建树状结构（扁平化显示，但保持层级关系）
        $treeData = $this->buildTreeData($data);

        return response()->json([
            'code' => 0,
            'message' => 'success',
            'count' => count($treeData),
            'data' => $treeData,
        ]);
    }

    /**
     * 构建树状结构数据（扁平化，但添加层级标识）
     */
    private function buildTreeData(array $items, int $parentId = 0, int $level = 0): array
    {
        $result = [];
        foreach ($items as $item) {
            if ($item['parent_id'] == $parentId) {
                // 添加层级标识
                $item['level'] = $level;
                // 查找子项
                $children = $this->buildTreeData($items, $item['id'], $level + 1);
                $result[] = $item;
                // 将子项添加到结果中（扁平化）
                $result = array_merge($result, $children);
            }
        }
        return $result;
    }
    public function index()
    {
        $categories = Category::query()->orderByDesc('sort')->orderBy('id')->paginate(20);
        return view('lpadmin.blog.category.index', compact('categories'));
    }

    public function create()
    {
        $langData = enabled_langs();
        $defaultLang = $langData['codes'][0] ?? 'cn';
        
        $allCategories = Category::query()->orderByDesc('sort')->orderBy('id')->get();
        $parents = $this->buildTreeOptions($allCategories, $defaultLang);
        return view('lpadmin.blog.category.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $category = Category::create($data);
        // 如果slug为空，自动生成
        if (empty($category->slug)) {
            $category->slug = 'category_' . $category->id;
            $category->save();
        }
        if ($request->ajax()) {
            return response()->json(['code' => 0, 'message' => '创建成功']);
        }
        return redirect()->route('lpadmin.blog.category.index')->with('success', '创建成功');
    }

    public function edit(Category $category)
    {
        $langData = enabled_langs();
        $defaultLang = $langData['codes'][0] ?? 'cn';
        
        // 排除当前分类及其所有子分类
        $excludeIds = $this->getCategoryAndChildrenIds($category->id);
        $allCategories = Category::query()->whereNotIn('id', $excludeIds)->orderByDesc('sort')->orderBy('id')->get();
        $parents = $this->buildTreeOptions($allCategories, $defaultLang);
        return view('lpadmin.blog.category.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $this->validatedData($request);
        $category->update($data);
        // 如果slug为空，自动生成
        if (empty($category->slug)) {
            $category->slug = 'category_' . $category->id;
            $category->save();
        }
        if ($request->ajax()) {
            return response()->json(['code' => 0, 'message' => '已保存']);
        }
        return redirect()->route('lpadmin.blog.category.index')->with('success', '已保存');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        if (request()->ajax()) {
            return response()->json(['code' => 0, 'message' => '已删除']);
        }
        return redirect()->back()->with('success', '已删除');
    }

    public function batchDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['code' => 400, 'message' => '参数错误']);
        }
        Category::whereIn('id', $ids)->delete();
        return response()->json(['code' => 0, 'message' => '批量删除成功']);
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'parent_id' => ['nullable', 'integer'],
            'sort' => ['nullable', 'integer'],
            'visible' => ['nullable', 'boolean'],
            'name' => ['required', 'array'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
        ]);
    }

    /**
     * 构建树状结构的选项列表（用于下拉选择）
     */
    private function buildTreeOptions($categories, string $lang, int $parentId = 0, int $level = 0): array
    {
        $options = [];
        foreach ($categories as $category) {
            $catParentId = $category->parent_id ?? 0;
            if ($catParentId == $parentId) {
                $displayName = get_i18n_value($category->name ?? [], $lang, '#'.$category->id);
                $prefix = str_repeat('├─ ', $level);
                $category->display_name = $prefix . $displayName;
                $category->level = $level;
                $options[] = $category;
                // 递归获取子分类
                $children = $this->buildTreeOptions($categories, $lang, $category->id, $level + 1);
                $options = array_merge($options, $children);
            }
        }
        return $options;
    }

    /**
     * 获取分类及其所有子分类的ID
     */
    private function getCategoryAndChildrenIds(int $categoryId): array
    {
        $ids = [$categoryId];
        $children = Category::where('parent_id', $categoryId)->pluck('id')->toArray();
        foreach ($children as $childId) {
            $ids = array_merge($ids, $this->getCategoryAndChildrenIds($childId));
        }
        return $ids;
    }
}


