<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\PostTag;
use App\Models\PostLike;
use App\Models\PostView;
use App\Services\UploadService;

class PostController extends Controller
{

    public function index(Post $post, PostTag $postTag, User $user)
    {
        if (request()->following == true) {
            // to login page if not logged in
            if (!auth()->check()) return redirect('/user/login?prev=/?following=true');
        }

        // 6 home posts
        $posts = $post->getHomepagePosts(10);
        // 10 top tags
        $tags = $postTag->getPopularTags(10);
        // 5 top users
        $users = $user->getPopularUsers(5);

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

    public function show($id, Post $post, PostView $postView)
    {
        $post = $post->getDetailPost($id);
        $comments = $post->comments()->with('user')->with('liked')->withCount('likes')->latest()->paginate(20);
        $morePosts = $post->user->getPublishedPost(3, [$post->id]);

        // +postview
        $postView->create(['user_id' => auth()->user()->id ?? null, 'post_id' => $post->id]);

        return view('post.index', [
            "post" => $post,
            "comments" => $comments,
            "morePosts" => $morePosts,
        ]);
    }

    public function create()
    {
        return view(
            'post.form',
            ['action' => 'create']
        );
    }

    public function store(UploadService $uploadService)
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
            $fields["picture"] = $uploadService->uploadPost(auth()->user()->id, request()->file('picture'));
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

    public function update(Post $post, UploadService $uploadService)
    {
        if ($post->user_id !== auth()->user()->id) abort(403, "Unauthorized");

        $fields = request()->validate([
            "title" => "required|min:2|max:100",
            "subtitle" => "required|min:5|max:200",
            "content" => "required",
            "tags" => "required|array|min:1|max:5",
            "picture" => ['mimes:jpg,png'],
        ]);

        // upload pic
        if (request()->hasFile('picture')) {
            $fields["picture"] = $uploadService->uploadPost(auth()->user()->id, request()->file('picture'));
        }

        $post->update($fields);

        // update tags
        PostTag::where('post_id', $post->id)->delete();
        foreach ($fields["tags"] as $tag) {
            $tag = Tag::firstOrCreate(["name" => strtolower($tag)]);
            PostTag::create(["post_id" => $post->id, "tag_id" => $tag->id]);
        }

        // to settings page
        if (request()->query()['next'] ?? false)
            return redirect('/post/' . $post->id . '/settings');

        return back()->with('message', 'Sukses');
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== auth()->user()->id) abort(403, "Unauthorized");

        $post->delete();
        return redirect('/')->with('message', 'Sukses dihapus');
    }

    // get tags by keyword, return json ['word', 'wordFromDb1', 'wordFromDb2']
    public function getTags($keyword, Tag $tag)
    {
        if (!$keyword) return response()->json([]);

        $tags = $tag->getTagNamesByKeyword($keyword)->toArray();
        array_unshift($tags, strtolower($keyword));

        return response()->json($tags);
    }

    // like/unlike post, return json
    public function like(Post $post, PostLike $postLike)
    {
        $userId = auth()->user()->id;
        $like = $post->getLikeByUser($userId);

        // delete if exist, create if not
        if ($like) $like->delete();
        else $postLike->create(['post_id' => $post->id, 'user_id' => $userId]);

        return response()->json([
            'liked' => $like ? false : true,
            'likeCount' => $post->likes->count()
        ]);
    }

    // for ckeditor image upload
    public function upload(UploadService $uploadService)
    {
        request()->validate([
            "upload" => ['mimes:jpg,png,gif|max:2048'],
        ]);

        if (request()->hasFile('upload')) {
            $CKEditorFuncNum = request()->input('CKEditorFuncNum');
            $url = $uploadService->uploadPost(auth()->user()->id, request()->file('upload'));
            $msg = 'Image successfully uploaded';
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
            @header('Content-type: text/html; charset=utf-8');
            return $response;
        }
    }

    // post settings page
    public function settings(Post $post)
    {
        if ($post->user_id !== auth()->user()->id) abort(403, "Unauthorized");

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

    // publish post, ngisi tanggal publishednya aj
    public function publish(Post $post)
    {
        $post->update(['published' => new \DateTime()]);
        return redirect('/post/' . $post->id . '/settings');
    }
}
