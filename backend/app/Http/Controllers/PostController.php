<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    // Lister les posts (newsfeed)
    public function index()
    {
        $posts = Post::with('user')->orderByDesc('created_at')->paginate(10);
        return response()->json($posts);
    }

    // Créer un post
    public function store(Request $request)
    {
        $request->validate([
            'contenu' => 'required|string',
            'image' => 'nullable|image|max:4096',
        ]);

        $data = [
            'user_id' => Auth::id(),
            'contenu' => $request->contenu,
            
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        $post = Post::create($data);
        $post->load('user');
        return response()->json($post, 201);
    }

    // Afficher un post
    public function show($id)
    {
        $post = Post::with('user')->findOrFail($id);
        return response()->json($post);
    }

    // Supprimer un post
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $user = Auth::user();
        if ($user->role !== 'administrateur' && $user->id !== $post->user_id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }
        $post->delete();
        return response()->json(['message' => 'Post supprimé']);
    }
} 