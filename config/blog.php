<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Blog Database Connection
    |--------------------------------------------------------------------------
    |
    | This is the database connection you want Blog to use while storing &
    | reading your content. By default Blog assumes you've prepared a
    | new connection called "blog". However, you can change that
    | to anything you want.
    |
    */

    'database_connection' => env('BLOG_DB_CONNECTION', 'blog'),

    /*
    |--------------------------------------------------------------------------
    | Blog Uploads Disk
    |--------------------------------------------------------------------------
    |
    | This is the storage disk Blog will use to put file uploads, you can use
    | any of the disks defined in your config/filesystems.php file. You may
    | also configure the path where the files should be stored.
    |
    */

    'storage_disk' => env('BLOG_STORAGE_DISK', 'local'),

    'storage_path' => env('BLOG_STORAGE_PATH', 'public/blog/images'),

    /*
    |--------------------------------------------------------------------------
    | Blog Domain
    |--------------------------------------------------------------------------
    |
    | This is the subdomain where Blog will be accessible from. By default it
    | will be accessible on the same domain as your app.
    |
    */

    'domain' => env('BLOG_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Blog Path
    |--------------------------------------------------------------------------
    |
    | This is the URI prefix where Blog will be accessible from. Feel free to
    | change this path to anything you like.
    |
    */

    'path' => env('BLOG_PATH', 'blog'),

    /*
    |--------------------------------------------------------------------------
    | Blog Middleware Group
    |--------------------------------------------------------------------------
    |
    | This is the middleware group that Blog uses.
    |
    */

    'middleware_group' => env('BLOG_MIDDLEWARE_GROUP', 'web'),

    /*
    |--------------------------------------------------------------------------
    | Blog Post Preview Path
    |--------------------------------------------------------------------------
    |
    | Blog uses this path to display a preview link in the editor. While
    | building the link tag, the {postSlug} placeholder will be replaced
    | by the actual post slug.
    |
    */

    'preview_path' => '/{postSlug}',

    'editor' => [

        /*
        |--------------------------------------------------------------------------
        | Default editor (for when you don't want options)
        |--------------------------------------------------------------------------
        |
        | Blog usually allows either markdown or rich text editing. If you're
        | setting up an environment where you only want one or the other
        | you can specify that here. (options: null, 'markdown', 'rich')
        |
        */

        'default' => null,

    ],
];
