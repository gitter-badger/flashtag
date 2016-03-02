<?php

namespace Flashtag\Admin\Http\Controllers\Api;

use Flashtag\Admin\Http\Controllers\Controller;
use Flashtag\Data\Post;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    public function index()
    {
        return Post::with('category')->orderBy('created_at', 'desc')->get();
    }

    public function store(Request $request)
    {
        $post = Post::create($this->buildPostFromRequest($request));

        return response('ok');
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $post->update($this->buildPostFromRequest($request));

        return response('ok');
    }

    public function lock($id)
    {
        $post = Post::findOrFail($id);
        $post->is_locked = true;
        $post->locked_by_id = \Auth::user()->id;
        $post->save();
    }

    public function unlock($id)
    {
        $post = Post::findOrFail($id);
        $post->is_locked = false;
        $post->locked_by_id = null;
        $post->save();
    }

    /**
     * Build the post data array from the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    private function buildPostFromRequest(Request $request)
    {
        return [
            'title' => $request->get('title'),
            'slug' => str_slug($request->get('title')),
            'subtitle' => $request->get('subtitle'),
            'order' => $request->get('order'),
            'category_id' => $request->get('category_id'),
            'author_id' => $request->get('author_id'),
            'show_author' => $request->get('show_author', false),
            'body' => $request->get('body'),
            'is_published' => $request->get('is_published', false),
            'start_showing_at' => $request->get('start_showing_at') ?: null,
            'stop_showing_at' => $request->get('stop_showing_at') ?: null,
        ];
    }
}