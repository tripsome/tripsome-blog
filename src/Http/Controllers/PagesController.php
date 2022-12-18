<?php

namespace Tripsome\Blog\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Tripsome\Blog\Http\Resources\PagesResource;
use Tripsome\Blog\BlogPage;

class PagesController
{
    /**
     * Return pages.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $entries = BlogPage::when(request()->has('search'), function ($q) {
            $q->where('title', 'LIKE', '%'.request('search').'%');
        })
            ->orderBy('created_at', 'DESC')
            ->paginate(30);

        return PagesResource::collection($entries);
    }

    /**
     * Return a single page.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id = null)
    {
        if ($id === 'new') {
            return response()->json([
                'entry' => BlogPage::make(['id' => Str::uuid()]),
            ]);
        }

        $entry = BlogPage::findOrFail($id);

        return response()->json([
            'entry' => $entry,
        ]);
    }

    /**
     * Store a single page.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($id)
    {
        $data = [
            'title' => request('title'),
            'slug' => request('slug'),
            'lang' => request('lang', 'EN'),
            'parent_id' => request('parent', ''),
            'body' => request('body', ''),
            'meta' => request('meta', (object) []),
        ];

        validator($data, [
            'title' => 'required',
            'slug' => 'required|'.Rule::unique(config('blog.database_connection').'.blog_pages', 'slug')->ignore(request('id')),
        ])->validate();

        $entry = $id !== 'new' ? BlogPage::findOrFail($id) : new BlogPage(['id' => request('id')]);

        $entry->fill($data);

        $entry->save();

        return response()->json([
            'entry' => $entry,
        ]);
    }

    /**
     * Delete a single page.
     *
     * @param  string  $id
     * @return void
     */
    public function delete($id)
    {
        $entry = BlogPage::findOrFail($id);

        $entry->delete();
    }
}
