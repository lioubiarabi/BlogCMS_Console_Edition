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



?>