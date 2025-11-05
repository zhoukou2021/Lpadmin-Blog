<?php

namespace App\Http\Controllers\LPadmin\Blog;

use App\Http\Controllers\LPadmin\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\Blog\Nav;

class NavController extends BaseController
{
    public function index(): View
    {
        return view('lpadmin.blog.nav.index');
    }

    public function select(Request $request): JsonResponse
    {
        $query = Nav::query()->orderByDesc('sort')->orderBy('id');
        if ($request->filled('visible')) {
            $query->where('visible', (bool)$request->visible);
        }
        
        // 获取所有数据（不分页），用于构建树状结构
        $allNavs = $query->get();

        $data = $allNavs->map(function(Nav $n){
            return [
                'id' => $n->id,
                'parent_id' => $n->parent_id ?? 0,
                'title' => $n->title['zh'] ?? (is_array($n->title)?(array_values($n->title)[0]??''):''),
                'url' => $n->url ?? '',
                'icon' => $n->icon,
                'sort' => $n->sort,
                'visible' => $n->visible ? 1 : 0,
                'created_at' => (string)$n->created_at,
            ];
        })->toArray();

        // 构建树状结构（扁平化显示，但保持层级关系）
        $treeData = $this->buildTreeData($data);

        return response()->json(['code'=>0,'message'=>'success','count'=>count($treeData),'data'=>$treeData]);
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

    public function create(): View
    {
        $langData = enabled_langs();
        $defaultLang = $langData['codes'][0] ?? 'zh';
        
        $allNavs = Nav::query()->orderByDesc('sort')->orderBy('id')->get();
        $parents = $this->buildTreeOptions($allNavs, $defaultLang);
        return view('lpadmin.blog.nav.create', compact('parents'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required','array'],
            'url' => ['required','string','max:255'],
            'icon' => ['nullable','string','max:50'],
            'sort' => ['nullable','integer'],
            'visible' => ['nullable','integer'],
            'parent_id' => ['nullable','integer'],
        ]);
        $data['sort'] = $data['sort'] ?? 0;
        $data['visible'] = isset($data['visible']) ? (bool)$data['visible'] : true;
        // 如果parent_id为0，设置为null
        if (isset($data['parent_id']) && $data['parent_id'] == 0) {
            $data['parent_id'] = null;
        }
        Nav::create($data);
        return $this->success(null,'创建成功');
    }

    public function edit(Nav $nav): View
    {
        $langData = enabled_langs();
        $defaultLang = $langData['codes'][0] ?? 'zh';
        
        // 排除当前导航及其所有子导航
        $excludeIds = $this->getNavAndChildrenIds($nav->id);
        $allNavs = Nav::query()->whereNotIn('id', $excludeIds)->orderByDesc('sort')->orderBy('id')->get();
        $parents = $this->buildTreeOptions($allNavs, $defaultLang);
        return view('lpadmin.blog.nav.edit', compact('nav', 'parents'));
    }

    public function update(Request $request, Nav $nav): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required','array'],
            'url' => ['required','string','max:255'],
            'icon' => ['nullable','string','max:50'],
            'sort' => ['nullable','integer'],
            'visible' => ['nullable','integer'],
            'parent_id' => ['nullable','integer'],
        ]);
        $data['sort'] = $data['sort'] ?? 0;
        $data['visible'] = isset($data['visible']) ? (bool)$data['visible'] : true;
        // 如果parent_id为0，设置为null
        if (isset($data['parent_id']) && $data['parent_id'] == 0) {
            $data['parent_id'] = null;
        }
        $nav->update($data);
        return $this->success(null,'已保存');
    }

    public function destroy(Nav $nav): JsonResponse
    {
        $nav->delete();
        return $this->success(null,'已删除');
    }

    public function batchDestroy(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) return $this->error('参数错误');
        Nav::whereIn('id',$ids)->delete();
        return $this->success(null,'批量删除成功');
    }

    /**
     * 构建树状结构的选项列表（用于下拉选择）
     */
    private function buildTreeOptions($navs, string $lang, int $parentId = 0, int $level = 0): array
    {
        $options = [];
        foreach ($navs as $nav) {
            $navParentId = $nav->parent_id ?? 0;
            if ($navParentId == $parentId) {
                $displayName = get_i18n_value($nav->title ?? [], $lang, '#'.$nav->id);
                $prefix = str_repeat('├─ ', $level);
                $nav->display_name = $prefix . $displayName;
                $nav->level = $level;
                $options[] = $nav;
                // 递归获取子导航
                $children = $this->buildTreeOptions($navs, $lang, $nav->id, $level + 1);
                $options = array_merge($options, $children);
            }
        }
        return $options;
    }

    /**
     * 获取导航及其所有子导航的ID
     */
    private function getNavAndChildrenIds(int $navId): array
    {
        $ids = [$navId];
        $children = Nav::where('parent_id', $navId)->pluck('id')->toArray();
        foreach ($children as $childId) {
            $ids = array_merge($ids, $this->getNavAndChildrenIds($childId));
        }
        return $ids;
    }
}


