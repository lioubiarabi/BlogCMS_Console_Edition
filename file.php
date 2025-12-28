<?php

class User
{
    protected int $id;
    public string $username;
    protected string $email;
    protected string $password;
    protected DateTime $createdAt;
    protected ?DateTime $lastLogin;


    public function auth($loginEmail, $loginPass)
    {
        if ($this->email == $loginEmail && $this->password == $loginPass) return true;
        else return false;
    }

    public function updateLastLogin()
    {
        $this->lastLogin = new DateTime();
    }

    public function getRole()
    {
        if ($this instanceof Admin) return "admin";
        elseif ($this instanceof Editor) return "Editor";
        elseif ($this instanceof Author) return "Author";
        else null;
    }
}

class Author extends User
{
    private string $bio;

    public function __construct($id, $username, $email, $password, $bio)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->role = 'author';
        $this->createdAt = new DateTime('today');
        $this->lastLogin = null;
        $this->bio = $bio;
    }
}

class Editor extends User
{
    protected string $moderationLevel;

    public function __construct($id, $username, $email, $password, $moderationLevel)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->role = 'editor';
        $this->createdAt = new DateTime('today');
        $this->lastLogin = null;
        $this->moderationLevel = $moderationLevel;
    }
}

class Admin extends Editor
{
    private bool $isSuperAdmin;

    public function __construct($id, $username, $email, $password, $isSuperAdmin)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->role = 'editor';
        $this->createdAt = new DateTime('today');
        $this->lastLogin = null;
        $this->moderationLevel = 'cheif';
        $this->isSuperAdmin = $isSuperAdmin;
    }
}

class Article
{
    private int $id;
    private string $title;
    private string $content;
    private string $status;
    private string $author;
    private array $comments = [];
    private DateTime $createdAt;
    private DateTime $updatedAt;
    private ?DateTime $publishedAt;

    public function __construct($id, $title, $content, $author)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->status = 'draft';
        $this->author = $author;
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->publishedAt = null;
    }

    public function publish()
    {
        if ($this->status == 'public') return false;
        $this->publishedAt = new DateTime();
        $this->status = 'public';
        return true;
    }

    public function archive()
    {
        if ($this->status == 'archive') return false;
        $this->status = 'archive';
        return true;
    }

    public function updateContent($content)
    {
        $this->updatedAt = new DateTime();
        $this->content = $content;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function addComment($username, $content)
    {
        $this->comments = ['username' => $username, 'content' =>  $content, 'status' => 'pending'];
    }

    public function approveComment()
    {
        $this->comments['status'] = 'approved';
    }

    public function refuseComment()
    {
        $this->comments['status'] = 'refused';
    }

    public function getId()
    {
        return $this->id;
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function getContent()
    {
        return $this->content;
    }
    public function getAuthor() {
        return $this->author;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}

class Category
{
    private int $id;
    private string $name;
    private string $description;
    private string $path;
    private array $articles = [];
    private DateTime $createdAt;

    public function __construct($id, $name, $description, $path)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->path = $path;
        $this->createdAt = new DateTime();
    }


    public function addArticle(Article $article)
    {
        $this->articles[] = $article;
    }

    public function getArticles()
    {
        return $this->articles;
    }
}
