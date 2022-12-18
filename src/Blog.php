<?php

namespace Tripsome\Blog;

class Blog
{
    /**
     * Get the default JavaScript variables for Blog.
     *
     * @return array
     */
    public static function scriptVariables()
    {
        return [
            'unsplash_key' => config('services.unsplash.key'),
            'path' => config('blog.path'),
            'preview_path' => config('blog.preview_path'),
            'author' => auth('blog')->check() ? auth('blog')->user()->only('name', 'avatar', 'id') : null,
            'default_editor' => config('blog.editor.default'),
        ];
    }
}
