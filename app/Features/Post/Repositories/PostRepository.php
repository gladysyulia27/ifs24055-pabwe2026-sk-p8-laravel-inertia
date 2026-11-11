<?php

namespace App\Features\Post\Repositories;

use App\Features\Post\Contracts\PostRepositoryInterface;
use App\Features\Post\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostRepository implements PostRepositoryInterface
{
    public function publicPosts(array $filters = []): LengthAwarePaginator
    {
        return $this->filtered(Post::with('user')->where('is_published', true), $filters);
    }

    public function userPosts(User $user, array $filters = []): LengthAwarePaginator
    {
        return $this->filtered($user->posts()->with('user'), $filters);
    }

    private function filtered($q, array $filters): LengthAwarePaginator
    {
        if (! empty($filters['search'])) {
            $term = '%'.mb_strtolower($filters['search']).'%';
            $q->where(fn ($s) => $s
                ->whereRaw('LOWER(title) LIKE ?', [$term])
                ->orWhereRaw('LOWER(content) LIKE ?', [$term])
                ->orWhereRaw('LOWER(COALESCE(excerpt, \'\')) LIKE ?', [$term]));
        }

        if (! empty($filters['category'])) {
            $q->where('category', $filters['category']);
        }

        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $q->where('is_published', $filters['status'] === 'published');
        }

        return $q->latest()->paginate(9)->withQueryString();
    }
}
