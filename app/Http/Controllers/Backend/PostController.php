<?php
namespace App\Http\Controllers\Backend;

use App\Models\Post;
use App\Http\Requests;
use App\Jobs\PostFormFields;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostUpdateRequest;

class PostController extends Controller
{
    /**
     * Display a listing of the posts
     */
    public function index()
    {
        $data = Post::all();

        return view('backend.post.index', compact('data'));
    }

    /**
     * Show the new post form
     */
    public function create()
    {
        $data = $this->dispatch(new PostFormFields());
        return view('backend.post.create', $data);
    }

    /**
     * Store a newly created Post
     *
     * @param PostCreateRequest $request
     */
    public function store(PostCreateRequest $request)
    {
        $post = Post::create($request->postFillData());
        $post->syncTags($request->get('tags', []));
        return redirect()->route('admin.post.index')->withSuccess('New Post Successfully Created.');
    }

    /**
     * Show the post edit form
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $data = $this->dispatch(new PostFormFields($id));
        return view('backend.post.edit', $data);
    }

    /**
     * Update the Post
     *
     * @param PostUpdateRequest $request
     * @param int $id
     */
    public function update(PostUpdateRequest $request, $id)
    {
        $post = Post::findOrFail($id);
        $post->fill($request->postFillData());
        $post->save();
        $post->syncTags($request->get('tags', []));
        if ($request->action === 'continue') {
            return redirect()->back()->withSuccess('Post has been updated.');
        }
        return redirect()->route('admin.post.index')->withSuccess('Post has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->tags()->detach();
        $post->delete();
        return redirect()->route('admin.post.index')->withSuccess('Post deleted.');
    }
}