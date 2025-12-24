<?php

class User {
    protected int $id;
    protected string $username;
    protected string $email;
    protected string $password;
    protected string $role;
    protected DateTime $createdAt;
    protected ?DateTime $lastLogin;

    

}

class Author extends User {
    private string $bio;

}

class Editor extends User {
    private string $moderationLevel;

}

class Admin extends User {
    private Bool $isSuperAdmin;
}

class Article {
    private int $id;
    private string $title;
    private string $content;
    private string $excerpt;
    private string $status;
    private User $author;
    private array $comments = [];
    private DateTime $createdAt;
    private ?DateTime $updatedAt;
    private ?DateTime $publishedAt;

}

class Category {
    private int $id;
    private string $name;
    private string $description;
    private ?int $parentId;
    private DateTime $createdAt;

}

?>