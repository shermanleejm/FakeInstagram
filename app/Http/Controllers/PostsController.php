<?php

namespace App\Http\Controllers;

use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = auth()->user()->following()->pluck('profiles.user_id');

        $posts = \App\Post::whereIn("user_id", $users)->with('user')->orderBy('created_at', "DESC")->paginate(5);

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store()
    {
        // dd(request()->all());

        $data = request()->validate([
            'caption' => "required",
            "image" => ["required", "image"],
        ]);

        // the second argument to store is a driver, can use S3
        $imagePath = request('image')->store('uploads', "public");

        $image = Image::make(public_path("storage/{$imagePath}"))->fit(1200, 1200);
        $image->save();

        auth()->user()->posts()->create([
            'caption' => $data['caption'],
            'image' => $imagePath,
        ]);

        return redirect(('/profile/') . auth()->user()->id);
        // $user = Auth::user();
        // $user->posts()->create($data);

        // Old Method
        // $post = new \App\Post();
        // $post->caption = $data["caption"];
        // $post->save();

        // Simpler Method - enter your own data that wasnt in validation array
        // have to add the field into validate function on top
        // \App\Post::create([
        //  --- data ---
        // ])

        // Simplest Method



    }

    public function show(\App\Post $post)
    {
        // Old fashioned way
        // return view('posts.show', [
        //     'post' => $post,
        // ]);

        // New Way
        // return view('posts.show', compact("post", "anotherField"));
        return view('posts.show', compact("post"));
    }
}
