<?php

namespace Tripsome\Blog\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Tripsome\Blog\Http\Resources\TeamResource;
use Tripsome\Blog\BlogAuthor;

class TeamController
{
    /**
     * Return posts.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if(auth()->user()->IsAdmin()){
            $entries = BlogAuthor::when(request()->has('search'), function ($q) {
                $q->where('name', 'LIKE', '%'.request('search').'%');
            })
                ->orderBy('created_at', 'DESC')
                ->withCount('posts')
                ->paginate(30);
    
            return TeamResource::collection($entries);
        }else{
            $entries = BlogAuthor::when(request()->has('search'), function ($q) {
                $q->where('name', 'LIKE', '%'.request('search').'%');
            })
                ->where('id', auth()->user()->id)
                ->orderBy('created_at', 'DESC')
                ->withCount('posts')
                ->paginate(30);
    
            return TeamResource::collection($entries);
        }
    }

    /**
     * Return a single post.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id = null)
    {
        if(auth()->user()->IsAdmin()){
            if ($id === 'new') {
                return response()->json([
                    'entry' => BlogAuthor::make([
                        'id' => Str::uuid(),
                    ]),
                ]);
            }

            $entry = BlogAuthor::findOrFail($id);

            return response()->json([
                'entry' => $entry,
            ]);
        }
        return redirect()->back();
    }

    /**
     * Store a single category.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($id)
    {
        if(auth()->user()->IsAdmin()){
            $data = [
                'name' => request('name'),
                'type' => 1,
                'slug' => request('slug'),
                'email' => request('email'),
                'bio' => request('bio'),
                'avatar' => request('avatar'),
                'meta' => request('meta', (object) []),
            ];

            validator($data, [
                'meta.theme' => 'in:dark,light',
                'name' => 'required',
                'slug' => 'required|'.Rule::unique(config('blog.database_connection').'.blog_authors', 'slug')->ignore(request('id')),
                'email' => 'required|email|'.Rule::unique(config('blog.database_connection').'.blog_authors', 'email')->ignore(request('id')),
            ])->validate();

            $entry = $id !== 'new' ? BlogAuthor::findOrFail($id) : new BlogAuthor(['id' => request('id')]);

            if (request('password')) {
                $entry->password = Hash::make(request('password'));
            }

            if (request('email') !== $entry->email && Str::contains($entry->avatar, 'gravatar')) {
                unset($data['avatar']);

                $entry->avatar = null;
            }

            $entry->fill($data);

            $entry->save();

            return response()->json([
                'entry' => $entry->fresh(),
            ]);
        }
        return redirect()->back();
    }

    /**
     * Return a single author.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function delete($id)
    {
        if(auth()->user()->IsAdmin()){
            $entry = BlogAuthor::findOrFail($id);

            if ($entry->posts()->count()) {
                return response()->json(['message' => 'Please remove the author\'s posts first.'], 402);
            }

            if ($entry->id == auth('blog')->user()->id) {
                return response()->json(['message' => 'You cannot delete yourself.'], 402);
            }

            $entry->delete();
        }
        return redirect()->back();
    }
}
