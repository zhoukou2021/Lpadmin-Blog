<?php

namespace App\Http\Controllers\LPadmin\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog\Tag;

class TagController extends Controller
{
    public function select(Request $request)
    {
        $query = Tag::query()->orderByDesc('id');
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($w) use ($q){
                $w->where('name->zh', 'like', "%$q%")
                  ->orWhere('name->en', 'like', "%$q%")
                  ->orWhere('slug', 'like', "%$q%");
            });
        }
        $list = $query->paginate($request->get('limit', 20));
        $data = collect($list->items())->map(function(Tag $t){
            return [
                'id' => $t->id,
                'name' => $t->name['zh'] ?? (array_values($t->name)[0] ?? ''),
                'slug' => $t->slug ?? '',
                'created_at' => (string)$t->created_at,
            ];
        })->toArray();

        return response()->json([
            'code' => 0,
            'message' => 'success',
            'count' => $list->total(),
            'data' => $data,
        ]);
    }
    public function index()
    {
        $tags = Tag::query()->orderByDesc('id')->paginate(30);
        return view('lpadmin.blog.tag.index', compact('tags'));
    }

    public function create()
    {
        return view('lpadmin.blog.tag.create');
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => ['required', 'array'],
            'slug' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['code' => 400, 'message' => $validator->errors()->first(), 'errors' => $validator->errors()], 400);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $tag = Tag::create($validator->validated());
        // 如果slug为空，自动生成
        if (empty($tag->slug)) {
            $tag->slug = 'tags_' . $tag->id;
            $tag->save();
        }

        if ($request->ajax()) {
            return response()->json(['code' => 0, 'message' => '创建成功']);
        }
        return redirect()->route('lpadmin.blog.tag.index')->with('success', '创建成功');
    }

    public function edit(Tag $tag)
    {
        return view('lpadmin.blog.tag.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $validator = \Validator::make($request->all(), [
            'name' => ['required', 'array'],
            'slug' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['code' => 400, 'message' => $validator->errors()->first(), 'errors' => $validator->errors()], 400);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $tag->update($validator->validated());
        // 如果slug为空，自动生成
        if (empty($tag->slug)) {
            $tag->slug = 'tags_' . $tag->id;
            $tag->save();
        }

        if ($request->ajax()) {
            return response()->json(['code' => 0, 'message' => '已保存']);
        }
        return redirect()->route('lpadmin.blog.tag.index')->with('success', '已保存');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return redirect()->back()->with('success', '已删除');
    }

    public function batchDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['code' => 400, 'message' => '参数错误']);
        }
        Tag::whereIn('id', $ids)->delete();
        return response()->json(['code' => 0, 'message' => '批量删除成功']);
    }
}


