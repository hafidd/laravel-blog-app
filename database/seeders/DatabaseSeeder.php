<?php

namespace Database\Seeders;

use Faker\Generator;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        // create users with posts
        $user_count = 50;
        for ($x = 0; $x < $user_count; $x++) {
            \App\Models\User::factory()->hasPosts(random_int(1, 3))->create();
        }

        // followers
        $userIds = \App\Models\User::all()->pluck('id')->toArray();
        for ($x = 1; $x < ($user_count + 1); $x++) {
            $uids = array_rand($userIds, random_int(2, 5));
            foreach ($uids as $uid) {
                if ($x == $uid) continue;
                \App\Models\Follow::create(["user_id" => $x, "follower_id" => $uid + 1]);
            }
        }

        // create tags
        $faker = app(Generator::class);
        // $tags = $faker->unique()->words(10);
        //foreach ($tags as $tag) {
        for ($x = 0; $x < 20; $x++) {
            \App\Models\Tag::create(["name" => $faker->unique()->word]);
        }
        //}

        // insert post tags
        $postIds = \App\Models\Post::all()->pluck('id')->toArray();
        $tagIds = \App\Models\Tag::all()->pluck('id')->toArray();
        foreach ($postIds as $post) {
            $tagKeys = array_rand($tagIds, random_int(2, 5));
            foreach ($tagKeys as $tagKey) {
                \App\Models\PostTag::create(["post_id" => $post, "tag_id" => $tagIds[$tagKey]]);
            }
        }

        // post views, likes, comments, comment likes       
        foreach ($postIds as $post) {
            $usersKeys = array_rand($userIds, random_int(2, 10));
            $postCommentIds = [];

            // views, likes, comments,
            foreach ($usersKeys as $userKey) {
                // views
                \App\Models\PostView::create([
                    "post_id" => $post,
                    "user_id" => $userIds[$userKey],
                ]);
                // likes
                if (random_int(1, 10) > 5)
                    \App\Models\PostLike::create([
                        "post_id" => $post,
                        "user_id" => $userIds[$userKey],
                    ]);
                // comment 
                if (random_int(1, 10) > 5) {
                    $comment = \App\Models\Comment::create([
                        "post_id" => $post,
                        "user_id" => $userIds[$userKey],
                        "content" => $faker->sentence()
                    ]);
                    array_push($postCommentIds, $comment->id);
                }
            }

            // comment likes
            foreach ($postCommentIds as $postCommentId) {
                foreach ($usersKeys as $userKey) {
                    if (random_int(1, 10) > 7)
                        \App\Models\CommentLike::create([
                            "comment_id" => $postCommentId,
                            "user_id" => $userIds[$userKey],
                        ]);
                }
            }
        }
    }
}
