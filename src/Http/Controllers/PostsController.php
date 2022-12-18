<?php

namespace Tripsome\Blog\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Tripsome\Blog\Http\Resources\PostsResource;
use Tripsome\Blog\BlogPost;
use Tripsome\Blog\BlogTag;

class PostsController
{
    /**
     * Return posts.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if(auth()->user()->IsAdmin()){
            $entries = BlogPost::when(request()->has('search'), function ($q) {
                $q->where('title', 'LIKE', '%'.request('search').'%');
            })->when(request('status'), function ($q, $value) {
                $q->$value();
            })->when(request('author_id'), function ($q, $value) {
                $q->whereAuthorId($value);
            })->when(request('tag_id'), function ($q, $value) {
                $q->whereHas('tags', function ($query) use ($value) {
                    $query->where('id', $value);
                });
            })
                ->orderBy('created_at', 'DESC')
                ->with('tags')
                ->paginate(30);
            return PostsResource::collection($entries);
        }else{
            $entries = BlogPost::when(request()->has('search'), function ($q) {
                $q->where('title', 'LIKE', '%'.request('search').'%');
            })->when(request('status'), function ($q, $value) {
                $q->$value();
            })->when(request('author_id'), function ($q, $value) {
                $q->whereAuthorId($value);
            })->when(request('tag_id'), function ($q, $value) {
                $q->whereHas('tags', function ($query) use ($value) {
                    $query->where('id', $value);
                });
            })
                ->where('author_id', auth()->user()->id)
                ->orderBy('created_at', 'DESC')
                ->with('tags')
                ->paginate(30);
            return PostsResource::collection($entries);
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
        if ($id === 'new') {
            return response()->json([
                'entry' => BlogPost::make([
                    'id' => Str::uuid(),
                    'publish_date' => now()->format('Y-m-d H:i:00'),
                    'markdown' => null,
                ]),
            ]);
        }

        $entry = BlogPost::with('tags')->findOrFail($id);

        return response()->json([
            'entry' => $entry,
        ]);
    }

    /**
     * Store a single post.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($id)
    {
        $data = [
            'title' => request('title'),
            'excerpt' => request('excerpt', ''),
            'lang' => request('lang', 'EN'),
            'parent_id' => request('parent', ''),
            'slug' => request('slug'),
            'body' => request('body', ''),
            'published' => request('published'),
            'markdown' => request('markdown'),
            'author_id' => request('author_id'),
            'featured_image' => request('featured_image'),
            'featured_image_caption' => request('featured_image_caption', ''),
            'publish_date' => request('publish_date', ''),
            'meta' => request('meta', (object) []),
        ];

        validator($data, [
            'publish_date' => 'required|date',
            'author_id' => 'required',
            'title' => 'required',
            'slug' => 'required|'.Rule::unique(config('blog.database_connection').'.blog_posts', 'slug')->ignore(request('id')),
        ])->validate();

        $entry = $id !== 'new' ? BlogPost::findOrFail($id) : new BlogPost(['id' => request('id')]);

        $entry->fill($data);

        $entry->save();

        $entry->tags()->sync(
            $this->collectTags(request('tags'))
        );

        return response()->json([
            'entry' => $entry,
        ]);
    }

    /**
     * Tags incoming from the request.
     *
     * @param  array  $incomingTags
     * @return array
     */
    private function collectTags($incomingTags)
    {
        $allTags = BlogTag::all();

        return collect($incomingTags)->map(function ($incomingTag) use ($allTags) {
            $tag = $allTags->where('id', $incomingTag['id'])->first();

            if (! $tag) {
                $tag = BlogTag::create([
                    'id' => $id = Str::uuid(),
                    'name' => $incomingTag['name'],
                    'slug' => Str::slug($incomingTag['name']),
                ]);
            }

            return (string) $tag->id;
        })->toArray();
    }

    /**
     * Return a single post.
     *
     * @param  string  $id
     * @return void
     */
    public function delete($id)
    {
        $entry = BlogPost::findOrFail($id);

        $entry->delete();
    }
}
