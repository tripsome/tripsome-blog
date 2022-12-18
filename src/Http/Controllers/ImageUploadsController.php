<?php

namespace Tripsome\Blog\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class ImageUploadsController
{
    /**
     * Upload a new image.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload()
    {
        $path = request()->image->store(config('blog.storage_path'), [
            'disk' => config('blog.storage_disk'),
            'visibility' => 'public',
        ]
        );

        return response()->json([
            'url' => Storage::disk(config('blog.storage_disk'))->url($path),
        ]);
    }
}
