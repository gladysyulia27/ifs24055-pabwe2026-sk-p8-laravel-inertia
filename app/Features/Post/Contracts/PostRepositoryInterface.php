<?php
namespace App\Features\Post\Contracts;
use App\Models\User;use Illuminate\Contracts\Pagination\LengthAwarePaginator;
interface PostRepositoryInterface {public function publicPosts(array $filters=[]):LengthAwarePaginator;public function userPosts(User $user,array $filters=[]):LengthAwarePaginator;}
