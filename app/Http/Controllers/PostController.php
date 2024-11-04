<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::paginate(10);

        return response()->json([
            'success' => true,
            'data' => $posts
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'image' => 'required|mimes:png,jpg|max:2048',
            'title' => 'required|max:255|string',
            'content' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());
        }

        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        $post = Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content
        ]);

        return response()->json([
            'success' => true,
            'data' => $post,
            'message' => 'Post successfully inserted'
        ], 200);
    }
}
