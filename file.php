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
    private string $moderationLevel;

}

class Admin extends User {
    private bool $isSuperAdmin;
}

class Article {
    private int $id;
    private string $title;
    private string $content;
    private string $status;
    private User $author;
    private array $comments = [];
    private DateTime $createdAt;
    private DateTime $updatedAt;
    private ?DateTime $publishedAt;

    public function __construct($id, $title, $content, $status, $author)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->status = $status;
        $this->author = $author;
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->publishedAt = null;

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
    new Author(1, "usera", "usera@email.com", "password", "this author hehe"),
    new Author(2, "userb", "userb@email.com", "password", "this author hehe"),
    new Author(3, "userc", "userc@email.com", "password", "this author hehe")
];

print_r($users[0]->auth('usera@email.com' , 'password'));

?>