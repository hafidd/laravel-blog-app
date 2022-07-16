<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->onDelete('cascade');
            $table->string("title");
            $table->string("subtitle");
            $table->string("picture")->nullable();
            $table->longText("content");
            $table->timestamp("published")->nullable();
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string("name")->unique();
        });

        Schema::create('post_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId("post_id")->onDelete('cascade');
            $table->foreignId("tag_id")->onDelete('cascade');
            $table->unique(['post_id', 'tag_id']);
        });

        Schema::create('post_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->nullable()->onDelete('set null');
            $table->foreignId("post_id")->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('post_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->onDelete('cascade');
            $table->foreignId("post_id")->onDelete('cascade');
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->onDelete('cascade');
            $table->foreignId("post_id")->onDelete('cascade');
            $table->foreignId("comment_id")->nullable()->onDelete('cascade');
            $table->boolean("pinned")->default(false);
            $table->longText("content");
            $table->timestamps();
        });

        Schema::create('comment_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->onDelete('cascade');
            $table->foreignId("comment_id")->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('post_tag');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('post_likes');
        Schema::dropIfExists('comment_likes');
    }
};
