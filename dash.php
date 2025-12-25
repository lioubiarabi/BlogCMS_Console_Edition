<?php

require 'file.php';

class collection
{
    private ?User $current_user;
    private array $users = [];
    private array $categories = [];

    public function __construct()
    {
        $this->current_user = null;

        $this->users = [
            new Author(1, "user1", "user1@email.com", "password", "this author hehe"),
            new Author(2, "user2", "user2@email.com", "password", "this author hehe"),
            new Author(3, "user3", "user3@email.com", "password", "this author hehe")
        ];

        $this->categories = [
            new Category(1, "Techno", "this is all about techno", "Techno"),
            new Category(2, "coding", "this is all about techno/coding", "Techno/coding"),
            new Category(3, "php", "this is all about techno/coding/php", "Techno/coding/php"),
            new Category(4, "Learn", "this is all about Learn", "Learn"),

        ];

        $this->categories[0]->addArticle(new Article(1, 'title1', 'content1', 'user1'));
        $this->categories[2]->addArticle(new Article(2, 'title2', 'content2', 'user2'));
        $this->categories[3]->addArticle(new Article(3, 'title3', 'content3', 'user3'));
    }

    public function isLoggedIn()
    {
        if ($this->current_user != null) return true;
        return false;
    }

    public function login($username, $password)
    {
        foreach ($this->users as $user) {
            if ($user->auth($username, $password)) {
                $this->current_user = $user;
                return true;
            }
            return false;
        }
    }
}

$db = new collection();

while (true) {

    echo "\n1. show all article\n";
    echo "2. login to your account\n";
    echo "0. exit\n";
    echo "\n choose a number: ";
    $chiox = trim(fgets(STDIN));

    switch ($chiox) {
        case 0:
            exit;
            break;
        case 1:
            
            break;
        case 2:
            echo "Entre your email: ";
            $email = trim(fgets(STDIN));
            echo "Entre your password: ";
            $password = trim(fgets(STDIN));

            echo ($db->login($email, $password)) ? "Welcome back!\n" : "Wrong email/password!\n";
            continue;
            break;

        default:
            echo "Please choose a correct number";
            break;
    }
}
