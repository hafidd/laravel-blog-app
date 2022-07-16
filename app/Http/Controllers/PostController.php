<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\PostTag;
use App\Models\PostLike;
use App\Models\PostView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{

    public function index()
    {
        // 6 posts
        $posts = Post::where('published', '<>', null)
            ->excludeCols(['content'])
            ->with(['tags', 'user']);

        if (request()->following == true) {
            $posts = $posts
                ->select("posts.id", "posts.user_id", "title", "subtitle", "picture", "published", "created_at", "updated_at", 'follows.id as fid')
                ->join('follows', function ($join) {
                    $join->on('posts.user_id', '=', 'follows.user_id')
                        ->select('follows.id as fid')
                        ->where('follows.follower_id', '=', auth()->user()->id);
                });
        }
        if (!empty(request()->search)) {
            $posts = $posts->where(DB::raw('lower(title)'), 'like', '%' . (request()->query()['search'] ?? '') . '%');
        }
        if (!empty(request()->tags)) {
            $tags = request()->tags;
            $posts = $posts->whereHas('tags', function ($q) use ($tags) {
                $q->whereIn(DB::raw('lower(name)'), $tags);
            });
        }
        $posts = $posts->orderBy('published', 'desc')
            ->orderBy('id', 'desc')
            ->simplePaginate(6);

        // 10 top tags
        $tags = PostTag::select('tags.name', DB::raw('count(tags.id) as tag_count'))
            ->join('post_views', 'post_tag.post_id', 'post_views.post_id')
            ->join('tags', 'tags.id', 'post_tag.tag_id')
            ->groupBy('tags.id')
            ->orderBy('tag_count', 'desc')
            ->take(10)->get();

        // 5 top users
        $users = User::select('users.*', DB::raw('count(users.id) as view_count'))
            ->join('posts', 'users.id', 'posts.user_id')
            ->join('post_views', 'post_views.post_id', 'posts.id')
            ->groupBy('users.id')
            ->orderBy('view_count', 'desc')
            ->take(5);
        if (auth()->user()) {
            $users = $users
                ->leftJoin('follows', function ($join) {
                    $join->on('users.id', '=', 'follows.user_id')
                        ->where('follower_id', auth()->user()->id);
                })
                ->whereNull('follows.id')
                ->where('users.id', '<>', auth()->user()->id);
        }
        $users = $users->get();

        return view(
            'index',
            [
                "search" => request()->search ?? null,
                "tagsSearch" => request()->tags ?? [],
                "following" => request()->following == true,
                "posts" => $posts,
                "tags" => $tags,
                "users" => $users,
            ]
        );
    }

    public function show($id)
    {
        $post =
            Post::with([
                'tags',
            ])
            ->withCount(['likes', 'views'])
            ->where('published', "<>", null)
            ->find($id);

        //dd($post->toArray());

        $comments = $post->comments()->with(['user'])->withCount(['likes'])->latest()->paginate(20);
        $morePosts =
            Post::where('user_id', $post->user->id)
            //->with(['tags'])
            //->withCount(['likes', 'views'])
            ->where('published', "<>", null)
            ->where('id', "<>", $post->id)
            ->latest()->take(3)->get();
        $pinnedComment = null;

        //$views = PostView::where('post_id', $post->id)->count();

        $liked = auth()->user() ? PostLike::where('user_id', auth()->user()->id)
            ->where('post_id', $post->id)->count() > 0 : false;

        PostView::create(['user_id' => auth()->user()->id ?? null, 'post_id' => $post->id]);

        return view('post.index', [
            "post" => $post,
            "comments" => $comments,
            "pinnedComment" => $pinnedComment,
            "morePosts" => $morePosts,
            "liked" => $liked
        ]);
    }

    public function getTags()
    {
        if (!request()->tag) return response()->json([]);

        $tags = Tag::where(DB::raw('lower(name)'), 'like', ('%' . strtolower(request()->tag) . '%'))
            ->where('name', '<>', request()->tag)
            ->limit(3)->pluck("name")->toArray();

        array_unshift($tags, strtolower(request()->tag));

        return response()->json($tags);
    }

    // like/unlike post
    public function like(Post $post)
    {
        $like = PostLike::where('user_id', auth()->user()->id)
            ->where('post_id', $post->id)->first();

        if ($like) {
            $like->delete();
            return response()->json([
                'liked' => false,
                'likeCount' => PostLike::where('post_id', $post->id)->count()
            ]);
        }
        PostLike::create(['post_id' => $post->id, 'user_id' => auth()->user()->id]);
        return response()->json([
            'liked' => true,
            'likeCount' => PostLike::where('post_id', $post->id)->count()
        ]);
    }

    public function create()
    {
        return view(
            'post.form',
            ['action' => 'create']
        );
    }

    public function store()
    {
        // validate
        $fields = request()->validate([
            "title" => "required|min:2|max:100",
            "subtitle" => "required|min:5|max:200",
            "content" => "required",
            "tags" => "required|array|min:1|max:5",
            "picture" => ['mimes:jpg,png'],
        ]);
        $fields["user_id"] = auth()->user()->id;

        // upload pic
        if (request()->hasFile('picture')) {
            if (config('app.use_cloudinary')) {
                // cloudinary
                $result = request()->picture->storeOnCloudinary('laravel-blog/images/' . auth()->user()->id);
                $fields["picture"] = $result->getSecurePath();
            } else {
                // local
                $fields["picture"] = '/storage/' . request()->file('picture')->store('post', 'public');
            }
        }

        // create post
        $post = Post::create($fields);

        // insert tags
        foreach ($fields["tags"] as $tag) {
            $tag = Tag::firstOrCreate(["name" => strtolower($tag)]);
            PostTag::create(["post_id" => $post->id, "tag_id" => $tag->id]);
        }

        if (request()->query()['next'] ?? false)
            return redirect('/post/' . $post->id . '/settings');

        return redirect('/post/' . $post->id . '/edit')->with('message', 'Post created');
    }

    public function edit(Post $post)
    {
        if ($post->user_id !== auth()->user()->id) {
            abort(403, "Unauthorized");
        }

        $tags = array_map(function ($tag) {
            return $tag['name'];
        }, $post->tags->toArray());

        return view(
            'post.form',
            [
                "action" => "update",
                "post" => $post,
                "tags" => $tags,
            ]
        );
    }

    public function settings(Post $post)
    {
        if ($post->user_id !== auth()->user()->id) {
            abort(403, "Unauthorized");
        }

        $tags = array_map(function ($tag) {
            return $tag['name'];
        }, $post->tags->toArray());

        return view(
            'post.settings',
            [
                "action" => "update",
                "post" => $post,
                "tags" => $tags,
            ]
        );
    }

    public function update(Post $post)
    {
        if ($post->user_id !== auth()->user()->id) {
            abort(403, "Unauthorized");
        }

        $fields = request()->validate([
            "title" => "required|min:2|max:100",
            "subtitle" => "required|min:5|max:200",
            "content" => "required",
            "tags" => "required|array|min:1|max:5",
            "picture" => ['mimes:jpg,png'],
        ]);

        // upload pic
        if (request()->hasFile('picture')) {
            if (config('app.use_cloudinary')) {
                // cloudinary
                $result = request()->picture->storeOnCloudinary('laravel-blog/images/' . auth()->user()->id);
                $fields["picture"] = $result->getSecurePath();
            } else {
                // local
                $fields["picture"] = '/storage/' . request()->file('picture')->store('post', 'public');
            }
        }

        $post->update($fields);

        // update tags
        PostTag::where('post_id', $post->id)->delete();
        foreach ($fields["tags"] as $tag) {
            $tag = Tag::firstOrCreate(["name" => strtolower($tag)]);
            PostTag::create(["post_id" => $post->id, "tag_id" => $tag->id]);
        }

        if (request()->query()['next'] ?? false)
            return redirect('/post/' . $post->id . '/settings');

        return back()->with('message', 'Sukses');
    }

    public function publish(Post $post)
    {
        $post->update(['published' => new \DateTime()]);
        return redirect('/post/' . $post->id . '/settings');
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== auth()->user()->id) {
            abort(403, "Unauthorized");
        }
        $post->delete();
        return redirect('/')->with('message', 'Sukses dihapus');
    }

    public function upload()
    {
        $fields = request()->validate([
            "upload" => ['mimes:jpg,png,gif|max:2048'],
        ]);

        if (request()->hasFile('upload')) {
            $CKEditorFuncNum = request()->input('CKEditorFuncNum');

            if (config('app.use_cloudinary')) {
                // cloudinary
                $result = request()->upload->storeOnCloudinary('laravel-blog/images/' . auth()->user()->id);
                $url = $result->getSecurePath();
            } else {
                // local
                $file = request()->file('upload')->store('images', 'public');
                $url = asset('storage/' . $file);
            }

            $msg = 'Image successfully uploaded';
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
            @header('Content-type: text/html; charset=utf-8');
            return $response;
        }
    }
}
