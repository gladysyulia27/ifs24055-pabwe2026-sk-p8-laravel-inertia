<?php
namespace App\Features\Post\Policies;
use App\Models\User;use App\Features\Post\Models\Post;
class PostPolicy {public function view(User $user,Post $post):bool{return $user->isAdmin()||$post->user_id===$user->id;}public function update(User $user,Post $post):bool{return $post->user_id===$user->id;}public function delete(User $user,Post $post):bool{return $post->user_id===$user->id;}}
