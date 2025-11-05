<?php

namespace App\Http\Controllers\LPadmin\Blog;

use App\Http\Controllers\LPadmin\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\Blog\Ad;

class AdController extends BaseController
{
    public function index(): View
    {
        return view('lpadmin.blog.ad.index');
    }

    public function select(Request $request): JsonResponse
    {
        $query = Ad::query()->orderByDesc('sort')->orderBy('id');
        if ($request->filled('type')) { $query->where('type', (int)$request->type); }
        if ($request->filled('visible')) { $query->where('visible', (bool)$request->visible); }
        $list = $query->paginate($request->get('limit', 20));

        $data = collect($list->items())->map(function(Ad $a){
            return [
                'id' => $a->id,
                'title' => $a->title['zh'] ?? (is_array($a->title)?(array_values($a->title)[0]??''):''),
                'type' => $a->type,
                'type_label' => Ad::getTypeLabel($a->type), // 添加类型标签
                'link' => $a->link,
                'image' => $a->image,
                'sort' => $a->sort,
                'visible' => $a->visible ? 1 : 0,
                'created_at' => (string)$a->created_at,
            ];
        })->toArray();
        return response()->json(['code'=>0,'message'=>'success','count'=>$list->total(),'data'=>$data]);
    }

    public function create(): View
    {
        return view('lpadmin.blog.ad.create');
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('uploads/ads', 'public');
            $data['image'] = '/storage/' . $path;
        }
        $data['sort'] = $data['sort'] ?? 0;
        $data['visible'] = isset($data['visible']) ? (bool)$data['visible'] : true;
        Ad::create($data);
        return $this->success(null, '创建成功');
    }

    public function edit(Ad $ad): View
    {
        return view('lpadmin.blog.ad.edit', compact('ad'));
    }

    public function update(Request $request, Ad $ad): JsonResponse
    {
        $data = $this->validated($request);
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('uploads/ads', 'public');
            $data['image'] = '/storage/' . $path;
        }
        $data['sort'] = $data['sort'] ?? 0;
        $data['visible'] = isset($data['visible']) ? (bool)$data['visible'] : true;
        $ad->update($data);
        return $this->success(null, '已保存');
    }

    public function destroy(Ad $ad): JsonResponse
    {
        $ad->delete();
        return $this->success(null, '已删除');
    }

    public function batchDestroy(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) return $this->error('参数错误');
        Ad::whereIn('id', $ids)->delete();
        return $this->success(null, '批量删除成功');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required','array'],
            'content' => ['nullable','array'],
            'link' => ['nullable','string','max:255'],
            'type' => ['required','integer','in:1,2,3,4,5,6,7'],
            'image' => ['nullable','string','max:255'],
            'image_file' => ['nullable','file','mimes:jpg,jpeg,png,gif,webp','max:2048'],
            'sort' => ['nullable','integer'],
            'visible' => ['nullable','integer'],
        ]);
    }
}


