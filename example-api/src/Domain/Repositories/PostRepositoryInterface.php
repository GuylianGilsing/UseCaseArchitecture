<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Post;

/**
 * The implementation schema for a class that retrieves posts from a persistence mechanism.
 */
interface PostRepositoryInterface
{
    /**
     * Retrieves all posts from a persistence mechanism.
     *
     * @return array<Post>
     */
    public function getAll(): array;

    /**
     * Retrieves a specific post from a persistence mechanism.
     */
    public function getByID(int $id): ?Post;

    /**
     * Retrieves a duplicate post from a persistence mechanism.
     */
    public function getDuplicate(Post $post): ?Post;

    /**
     * Stores a post within a persistence mechanism.
     */
    public function create(Post $post): ?Post;

    /**
     * Updates a post that is stored within a persistence mechanism.
     */
    public function update(Post $post): Post;
}
