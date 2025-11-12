<?php

namespace App\Http\Controllers\LPadmin\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog\Post;
use App\Models\Blog\Tag;

class PostController extends Controller
{
    public function select(Request $request)
    {
        $query = Post::query()->orderByDesc('id');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $searchTerm = '%' . $q . '%';
            $query->where(function($w) use ($searchTerm){
                // 使用 MySQL 的 JSON 操作符 ->> 来查询 JSON 字段（MySQL 5.7+）
                // 支持查询 title 和 summary 的所有语言版本（zh, en, cn, tw）
                $w->whereRaw('title->>"$.zh" LIKE ?', [$searchTerm])
                  ->orWhereRaw('title->>"$.en" LIKE ?', [$searchTerm])
                  ->orWhereRaw('title->>"$.cn" LIKE ?', [$searchTerm])
                  ->orWhereRaw('title->>"$.tw" LIKE ?', [$searchTerm])
                  ->orWhereRaw('summary->>"$.zh" LIKE ?', [$searchTerm])
                  ->orWhereRaw('summary->>"$.en" LIKE ?', [$searchTerm])
                  ->orWhereRaw('summary->>"$.cn" LIKE ?', [$searchTerm])
                  ->orWhereRaw('summary->>"$.tw" LIKE ?', [$searchTerm]);
            });
        }
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }
        $list = $query->with(['category','tags'])->withCount(['comments','likes','favorites'])->paginate($request->get('limit', 20));
        $data = collect($list->items())->map(function(Post $p){
            $tagNames = [];
            foreach ($p->tags as $t) {
                $tagNames[] = $t->name['zh'] ?? (is_array($t->name) ? (array_values($t->name)[0] ?? '') : '');
            }
            return [
                'id' => $p->id,
                'title' => $p->title['zh'] ?? (array_values($p->title)[0] ?? ''),
                'status' => $p->status,
                'category' => $p->category ? ($p->category->name['zh'] ?? (array_values($p->category->name)[0] ?? '')) : '',
                'tags' => implode('，', array_filter($tagNames)),
                'recommend' => (bool)($p->recommend ?? false), // 确保返回布尔值
                'comments_count' => $p->comments_count ?? 0,
                'likes_count' => $p->likes_count ?? 0,
                'favorites_count' => $p->favorites_count ?? 0,
                'published_at' => optional($p->published_at)->toDateTimeString(),
                'created_at' => (string)$p->created_at,
            ];
        })->toArray();

        return response()->json([
            'code' => 0,
            'message' => 'success',
            'count' => $list->total(),
            'data' => $data,
        ]);
    }
    public function index(Request $request)
    {
        $posts = Post::query()
            ->orderByDesc('id')
            ->paginate(15);

        return view('lpadmin.blog.post.index', compact('posts'));
    }

    public function create()
    {
        $tags = Tag::query()->orderByDesc('id')->get();
        $categories = \App\Models\Blog\Category::query()->orderBy('sort','desc')->orderBy('id')->get();
        return view('lpadmin.blog.post.create', compact('tags','categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        if ($request->hasFile('cover_file')) {
            $path = $request->file('cover_file')->store('uploads/posts', 'public');
            $data['cover'] = '/storage/' . $path;
        }
        $post = Post::create($data);
        // 如果slug为空，自动生成
        if (empty($post->slug)) {
            $post->slug = 'article_' . $post->id;
            $post->save();
        }
        $tagIds = $request->input('tag_ids', $request->input('tag_ids', []));
        if (!is_array($tagIds)) {
            if (is_string($tagIds)) {
                $tagIds = array_filter(explode(',', $tagIds));
            } elseif (is_null($tagIds)) {
                $tagIds = [];
            } else {
                $tagIds = [$tagIds];
            }
        }
        $post->tags()->sync($tagIds);
        if ($request->ajax()) {
            return response()->json(['code' => 0, 'message' => '创建成功']);
        }
        return redirect()->route('lpadmin.blog.post.index')->with('success', '创建成功');
    }

    public function edit(Post $post)
    {
        $tags = Tag::query()->orderByDesc('id')->get();
        $categories = \App\Models\Blog\Category::query()->orderBy('sort','desc')->orderBy('id')->get();
        $selectedTagIds = $post->tags()->pluck('id')->all();
        return view('lpadmin.blog.post.edit', compact('post', 'tags', 'selectedTagIds','categories'));
    }

    public function update(Request $request, Post $post)
    {
        $data = $this->validatedData($request);
        if ($request->hasFile('cover_file')) {
            $path = $request->file('cover_file')->store('uploads/posts', 'public');
            $data['cover'] = '/storage/' . $path;
        }
        
        // 使用 fill 方法填充数据并保存，确保所有字段包括 recommend 都被更新
        $post->fill($data);
        $post->save();
        
        // 如果slug为空，自动生成
        if (empty($post->slug)) {
            $post->slug = 'article_' . $post->id;
            $post->save();
        }
        $tagIds = $request->input('tag_ids', $request->input('tag_ids', []));
        if (!is_array($tagIds)) {
            if (is_string($tagIds)) {
                $tagIds = array_filter(explode(',', $tagIds));
            } elseif (is_null($tagIds)) {
                $tagIds = [];
            } else {
                $tagIds = [$tagIds];
            }
        }
        $post->tags()->sync($tagIds);
        if ($request->ajax()) {
            return response()->json(['code' => 0, 'message' => '已保存']);
        }
        return redirect()->route('lpadmin.blog.post.index')->with('success', '已保存');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->back()->with('success', '已删除');
    }

    public function batchDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['code' => 400, 'message' => '参数错误']);
        }
        Post::whereIn('id', $ids)->delete();
        return response()->json(['code' => 0, 'message' => '批量删除成功']);
    }

    public function batchUpdateStatus(Request $request)
    {
        $ids = $request->input('ids', []);
        $status = $request->input('status');

        if (empty($ids) || !is_array($ids)) {
            return response()->json(['code' => 400, 'message' => '请选择要操作的文章']);
        }

        if (empty($status) || !in_array($status, ['draft', 'published', 'offline'])) {
            return response()->json(['code' => 400, 'message' => '状态参数错误']);
        }

        $updateData = ['status' => $status];
        
        // 如果设置为已发布且没有发布时间，设置发布时间为当前时间
        if ($status === 'published') {
            $updateData['published_at'] = now();
        }

        Post::whereIn('id', $ids)->update($updateData);

        $statusMap = [
            'draft' => '草稿',
            'published' => '已发布',
            'offline' => '已下线',
        ];

        return response()->json([
            'code' => 0,
            'message' => '批量设置为' . ($statusMap[$status] ?? $status) . '成功',
        ]);
    }

    protected function validatedData(Request $request): array
    {
        // 处理recommend字段：如果接收的是数组，取最后一个值（checkbox的值优先级更高）
        $recommend = $request->input('recommend');
        if (is_array($recommend)) {
            // 如果checkbox被选中，数组会包含['0', '1']，取最后一个（即'1'）
            // 如果checkbox未选中，只有hidden input的值['0']
            $recommend = end($recommend);
        }
        // 标准化为布尔值：'1', 1, true, 'true' 都视为 true，其他视为 false
        $recommend = in_array($recommend, ['1', 1, true, 'true'], true);
        
        $validated = $request->validate([
            'author_id' => ['required', 'integer'],
            'status' => ['required', 'string'],
            'cover' => ['nullable', 'string'],
            'cover_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:4096'],
            'published_at' => ['nullable', 'date'],
            'category_id' => ['nullable', 'integer'],
            'title' => ['required', 'array'],
            'slug' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'array'],
            'content' => ['nullable', 'array'],
            'meta_title' => ['nullable', 'array'],
            'meta_desc' => ['nullable', 'array'],
            'meta_json' => ['nullable', 'array'],
        ]);
        
        // 将处理后的recommend值添加到验证后的数据中
        // 注意：即使是false也要显式设置，确保能正确更新
        $validated['recommend'] = $recommend;

        return $validated;
    }
}


