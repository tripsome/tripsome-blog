<?php

namespace Tripsome\Blog\Http\Controllers;

use Tripsome\Blog\Blog;

class SPAViewController
{
    /**
     * Single page application catch-all route.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        return view('blog::layout', [
            'blogScriptVariables' => Blog::scriptVariables(),
        ]);
    }
}
