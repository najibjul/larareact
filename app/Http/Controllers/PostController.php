<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::paginate(5);

        return response()->json([
            'success' => true,
            'data' => $posts,
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
            return response()->json([$validator->errors()],400);
        }

        $image = $request->file('image');
        $image->storeAs('posts', $image->hashName(), 'public');

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

    public function edit(Post $id)
    {
        return response()->json([
            'success' => true,
            'data' => $id
        ]);
    }

    public function destroy(Post $id)
    {

        if(Storage::disk('public')->exists('posts/'.basename($id->image))){
            Storage::disk('public')->delete('posts/'.basename($id->image));
        }

        $id->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post successfully deleted'
        ]);
    }

    public function update(Request $request , Post $id){
        
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'image' => 'nullable|mimes:png,jpg',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $image = $request->file('image');

        if($image){
            if(Storage::disk('public')->exists('posts/'.basename($id->image))){
                Storage::disk('public')->delete('posts/'.basename($id->image));
            }

            $hashName = $image->hashName();

            $image->storeAs('posts', $hashName, 'public');

            $id->update([
                'title' => $request->title,
                'content' => $request->content,
                'image' => $hashName
            ]);

        }else{
            $id->update([
                'title' => $request->title,
                'content' => $request->content,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Post successfully updated'
        ]);
    }
}