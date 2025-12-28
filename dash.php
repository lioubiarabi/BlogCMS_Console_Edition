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
            "user1" => new Author(1, "user1", "user", "user", "this author hehe"),
            "user2" => new Author(2, "user2", "user2@email.com", "password", "this author hehe"),
            "user3" => new Author(3, "user3", "user3@email.com", "password", "this author hehe")
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

    public function isLoggedIn()
    {
        if ($this->current_user != null) return true;
        return false;
    }

    public function logout()
    {
        $this->current_user = null;
    }

    public function getUser()
    {
        if ($this->current_user != null) return $this->current_user->username;
        return "Anonymous";
    }

    public function getRole()
    {
        if ($this->current_user != null) return $this->current_user->getRole();
        return "visitor";
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function getAllArticles($condition)
    {
        $result = [];
        switch ($condition) {
            case 'published':
                foreach ($this->categories as $category) {
                    foreach ($category->getArticles() as $article) {
                        if ($article->getStatus() == 'published') $result[] = $article;
                    }
                }
                break;

            default:
                // to get articles by the username of the author
                foreach ($this->categories as $category) {
                    foreach ($category->getArticles() as $article) {
                        if ($article->getAuthor() == $condition) $result[] = $article;
                    }
                }
                break;
        }

        if (count($result) == 0) return null;
        else return $result;
    }
}

$db = new collection();

while (true) {

    echo "Hello, Dear " . $db->getUser() . " || " . $db->getRole();
    switch ($db->getRole()) {
        case 'Author':
            echo "\n1. show all article\n";
            echo "2. show my articles\n";
            echo "0. logout\n";
            echo "\n choose a number: ";

            switch (trim(fgets(STDIN))) {
                case 0:
                    $db->logout();
                    break;
                case 1:
                    $pubArticles = $db->getAllArticles('published');
                    if ($pubArticles == null) {
                        echo "\n\nthere's no articles \n\n";
                        break;
                    }
                    foreach ($pubArticles as $article) {
                        print_r($article);
                    }
                    break;
                case 2:
                    echo "\n My Articles: \n";
                    $myArti = $db->getAllArticles($db->getUser());
                    if ($myArti == null) {
                        echo "\n\nthere's no articles \n\n";
                        break;
                    }
                    foreach ($myArti as $article) {
                        print_r($article);
                    }
                    break;

                default:
                    echo "Please choose a correct number";
                    break;
            }
            break;

        case 'Editor':
            # code...
            break;

        case 'Admin':
            # code...
            break;

        default:
            echo "\n1. show all article\n";
            echo "2. login to your account\n";
            echo "0. exit\n";
            echo "\n choose a number: ";

            switch (trim(fgets(STDIN))) {
                case 0:
                    exit;
                    break;
                case 1:
                    $pubArticles = $db->getAllArticles('published');
                    if ($pubArticles == null) {
                        echo "\n\nthere's no articles \n\n";
                        break;
                    }
                    foreach ($pubArticles as $article) {
                        print_r($article);
                    }
                    break;
                case 2:
                    echo "Entre your email: ";
                    $email = trim(fgets(STDIN));
                    echo "Entre your password: ";
                    $password = trim(fgets(STDIN));

                    echo ($db->login($email, $password)) ? "Welcome back!\n" : "Wrong email/password!\n";
                    break;

                default:
                    echo "Please choose a correct number";
                    break;
            }
            break;
    }
}
