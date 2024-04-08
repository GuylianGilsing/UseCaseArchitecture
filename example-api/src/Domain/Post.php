<?php

declare(strict_types=1);

namespace App\Domain;

use ErrorException;

use function App\Helpers\string_is_blank;

/**
 * Represents a post within the application.
 */
final class Post
{
    private ?int $id = null;
    private string $title;
    private string $content;

    /**
     * @throws ErrorException when a blank title is given.
     * @throws ErrorException when a title with more than 32 characters is given.
     * @throws ErrorException when no content is given.
     */
    public function __construct(?int $id, string $title, string $content)
    {
        $this->id = $id;
        $this->setTitle($title);
        $this->setContent($content);
    }

    public function getID(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @throws ErrorException when a blank title is given.
     * @throws ErrorException when a title with more than 32 characters is given.
     */
    public function setTitle(string $title): void
    {
        if (string_is_blank($title)) {
            throw new ErrorException('Title can\'t be blank');
        }

        if (strlen($title) > 32) {
            throw new ErrorException('Title can\'t have more than 32 characters');
        }

        $this->title = $title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @throws ErrorException when no content is given.
     */
    public function setContent(string $content): void
    {
        if (string_is_blank($content)) {
            throw new ErrorException('No content has been given');
        }

        $this->content = $content;
    }

    /**
     * @return array<string, mixed>
     */
    public function toJSONArray(): array
    {
        $json = [];

        if ($this->getID() !== null) {
            $json['id'] = $this->getID();
        }

        $json['title'] = $this->getTitle();
        $json['content'] = $this->getContent();

        return $json;
    }
}
