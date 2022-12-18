<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->unique();
            $table->string('name');
            $table->timestamps();

            $table->index('created_at');
        });

        Schema::create('blog_posts_tags', function (Blueprint $table) {
            $table->uuid('post_id');
            $table->uuid('tag_id');

            $table->unique(['post_id', 'tag_id']);
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('excerpt');
            $table->text('body');
            $table->boolean('published')->default(false);
            $table->dateTime('publish_date')->default('2018-10-10 00:00:00');
            $table->string('featured_image')->nullable();
            $table->string('featured_image_caption');
            $table->uuid('author_id')->index();
            $table->timestamps();
        });

        Schema::create('blog_authors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->text('bio');
            $table->string('avatar')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('blog_pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('body');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog_tags');
        Schema::dropIfExists('blog_posts_tags');
        Schema::dropIfExists('blog_authors');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('blog_pages');
    }
}
