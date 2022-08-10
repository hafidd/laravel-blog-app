<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\Post;
use App\Models\User;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function register()
    {
        return view('user.register');
    }

    public function login()
    {
        return view('user.login');
    }

    public function store()
    {
        $fields = request()->validate([
            "name" => "required|max:30",
            "username" => "required|min:3|max:16|unique:users",
            "email" => "required|email|unique:users",
            "password" => "required|min:6|confirmed"
        ]);

        $fields["password"] = bcrypt($fields["password"]);
        $user = User::create($fields);

        Auth::login($user);

        return redirect("/")->with("message", "Akun berhasil dibuat");
    }

    public function logout()
    {
        Auth::logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();

        return redirect('/');
    }

    public function auth()
    {
        $fields = request()->validate([
            "password" => "required",
            "email" => "required|email",
        ]);

        if (Auth::attempt($fields)) {
            request()->session()->regenerate();
            $to = request()->query()['prev'] ?? '/';
            return redirect($to)->with("message", "Login sukses");
        }

        return back()->withErrors(['email' => 'Email/Password salah'])->onlyInput('email');
    }

    public function show($username)
    {
        $user = $this->user->getUserWithFollowCount($username);
        $posts = $user->getPosts(10);
        $following = !auth()->user() ? false : $this->user->isFollowing($user->id, auth()->user()->id);

        return view('user.profile', [
            "user" => $user,
            "following" => $following,
            "posts" => $posts,
        ]);
    }

    public function updateName(User $user)
    {
        if ($user->id != auth()->user()->id) {
            abort(403, "Unauthorized");
        }
        request()->validate(['name' => 'required|max:30']);
        $user->name = request()->name;
        $user->save();
        return back();
    }

    public function updatePicture(User $user, UploadService $uploadService)
    {
        if ($user->id != auth()->user()->id) {
            abort(403, "Unauthorized");
        }
        request()->validate(["picture" => 'required|mimes:jpg,png|max:1024']);

        if (request()->hasFile('picture')) {
            $user->picture = $uploadService->uploadProfile($user->id, request()->picture);
            $user->save();
        }

        return back();
    }

    public function follow(User $user)
    {
        Follow::firstOrCreate(['user_id' => $user->id, 'follower_id' => auth()->user()->id]);
        return back();
    }

    public function unfollow($user)
    {
        $follow = Follow::where(['user_id' => $user, 'follower_id' => auth()->user()->id])->firstOrFail();
        $follow->delete();
        return back();
    }

    public function followers(User $user)
    {
        return response()->json($user->followers()->with('follower')->get());
    }

    public function follows(User $user)
    {
        return response()->json($user->follows()->with('user')->get());
    }
}
