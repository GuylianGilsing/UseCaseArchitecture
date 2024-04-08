<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\PostRepository;

use App\Domain\Post;
use App\Domain\Repositories\PostRepositoryInterface;
use ErrorException;

final class InMemory implements PostRepositoryInterface
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!array_key_exists('temp-post-repository', $_SESSION)) {
            $_SESSION['temp-post-repository'] = [];
        }
    }

    /**
     * @return array<Post>
     */
    public function getAll(): array
    {
        if (!array_key_exists('temp-post-repository', $_SESSION)) {
            throw new ErrorException('Repo can\'t access session storage');
        }

        $posts = [];

        foreach (array_values($_SESSION['temp-post-repository']) as $post) {
            $posts[] = $this->serializeSessionPost($post);
        }

        return $posts;
    }

    public function getByID(int $id): ?Post
    {
        if (!array_key_exists('temp-post-repository', $_SESSION)) {
            throw new ErrorException('Repo can\'t access session storage');
        }

        $postID = 'post-'.$id;

        if (!array_key_exists($postID, $_SESSION['temp-post-repository'])) {
            return null;
        }

        $post = $_SESSION['temp-post-repository'][$postID];

        return $this->serializeSessionPost($post);
    }

    public function getDuplicate(Post $post): ?Post
    {
        if (!array_key_exists('temp-post-repository', $_SESSION)) {
            throw new ErrorException('Repo can\'t access session storage');
        }

        foreach ($_SESSION['temp-post-repository'] as $persistedPost) {
            if ($post->getTitle() === $persistedPost['title']) {
                return $this->serializeSessionPost($persistedPost);
            }
        }

        return null;
    }

    public function create(Post $post): ?Post
    {
        if (!array_key_exists('temp-post-repository', $_SESSION)) {
            throw new ErrorException('Repo can\'t access session storage');
        }

        if ($this->getDuplicate($post) !== null) {
            throw new ErrorException('Post with same name already exists');
        }

        $postID = time();
        $createdPost = new Post($postID, $post->getTitle(), $post->getContent());

        $_SESSION['temp-post-repository']['post-'.$postID] = [
            'id' => $createdPost->getID(),
            'title' => $createdPost->getTitle(),
            'content' => $createdPost->getContent(),
        ];

        return $createdPost;
    }

    public function update(Post $post): Post
    {
        if ($this->getByID($post->getID()) === null) {
            throw new ErrorException('Post with ID: "'.$post->getID().'" does not exist');
        }

        $postID = 'post-'.$post->getID();
        $_SESSION['temp-post-repository'][$postID]['title'] = $post->getTitle();
        $_SESSION['temp-post-repository'][$postID]['content'] = $post->getContent();

        return $post;
    }

    /**
     * @param array<string, mixed> $post
     */
    private function serializeSessionPost(array $post): Post
    {
        return new Post(
            $post['id'],
            $post['title'],
            $post['content'],
        );
    }
}
