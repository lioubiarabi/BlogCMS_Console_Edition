<?php

class User {
    protected int $id;
    protected string $username;
    protected string $email;
    protected string $password;
    protected string $role;
    protected DateTime $createdAt;
    protected ?DateTime $lastLogin;
    

    public function auth($loginEmail, $loginPass) {
        if($this->email == $loginEmail && $this->password == $loginPass) return true;
        else return false;
    }

    public function updateLastLogin() {
        $this->lastLogin = new DateTime();
    }

}

class Author extends User {
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

class Editor extends User {
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

class Admin extends Editor {
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

class Article {
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

    public function publish() {
        if($this->status == 'public') return false;
        $this->status = 'public';
        return true;
    }

    public function archive() {
        if($this->status == 'archive') return false;
        $this->status = 'archive';
        return true;
    }

    public function updateContent($content) {
        $this->content = $content;
    }

    public function getComments() {
        return $this->comments;
    }

    public function addComment($username, $content) {
        $this->comments = ['username' => $username,'content' =>  $content, 'status'=> 'pending'];
    }

    public function approveComment() {
        $this->comments['status'] = 'approved';
    }

    public function refuseComment() {
        $this->comments['status'] = 'refused';
    }



}

class Category {
    private int $id;
    private string $name;
    private string $description;
    private ?int $parentId;
    private DateTime $createdAt;

    public function __construct($id, $name, $description, $parentId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->parentId = $parentId;
        $this->createdAt = new DateTime();
    }

}

$users = [
    new Author(1, "user1", "user1@email.com", "password", "this author hehe"),
    new Author(2, "user2", "user2@email.com", "password", "this author hehe"),
    new Author(3, "user3", "user3@email.com", "password", "this author hehe")
];

$articles = [
    new Article(1, 'title1', 'content1', 'user1'),
    new Article(2, 'title2', 'content2', 'user2'),
    new Article(3, 'title3', 'content3', 'user3'),
];


?>