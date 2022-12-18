<?php

namespace Tripsome\Blog;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractBlogModel extends Model
{
    /**
     * Get the current connection name for the model.
     *
     * @return string
     */
    public function getConnectionName()
    {
        return config('blog.database_connection');
    }
}
