<?php

require 'file.php';

class collection
{
    private ?User $current_user;
    private array $users = [];
    private array $categories = [];

    public function __construct()
    {

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

        // temporary
        //$this->current_user = null;
        $this->current_user = $this->users['user1'];
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

    public function deleteArticle($id)
    {
        foreach ($this->categories as $category) {
            if ($category->removeArticle($id)) return true;
        }
        return false;
    }

    public function getAllArticles($condition)
    {
        $result = [];
        switch ($condition) {
            case 'public':
                foreach ($this->categories as $category) {
                    foreach ($category->getArticles() as $article) {
                        if ($article->getStatus() == 'public') $result[] = $article;
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
                    $pubArticles = $db->getAllArticles('public');
                    if ($pubArticles == null) {
                        echo "\n\nthere's no articles \n\n";
                        break;
                    }
                    printf("%-5s %-15s %-10s %-20s %-30s\n", "id", "title", "comments", "publishedAt", "content");

                    echo str_repeat("=", 80) . "\n";

                    foreach ($pubArticles as $key => $article) {
                        printf("%-5s %-15s %-10s %-20s %-30s\n", ($key + 1), $article->getTitle(), count($article->getComments()), $article->getPublishedAt(), $article->getContent());
                    }
                    echo "\n\n";
                    $loop = true;
                    while ($loop) {
                        echo "\nActions:\n";
                        echo "1. read comments and write one\n";
                        echo "0. Go back to menu\n";
                        echo "\nChoose a number: ";

                        $choice = trim(fgets(STDIN));

                        switch ($choice) {
                            case 0:
                                $loop = false;
                                break;

                            case 1:
                                echo "\nwhich article number: ";
                                $articleIndex = (int)trim(fgets(STDIN));
                                $index = $articleIndex - 1;

                                if (isset($pubArticles[$index])) {
                                    printf("\n%-15s %-30s %-20s\n", "Username", "Comment", "Created At");

                                    echo str_repeat("=", 70) . "\n";

                                    $comments = $pubArticles[$index]->getComments('approved');
                                    if (count($comments) == 0) echo "no comments yet\n";
                                    foreach ($comments as $key => $comment) {
                                        printf("%-15s %-30s %-20s\n", $comment['username'], $comment['content'], $comment['createdAt']);
                                    }

                                    echo "\nType your comment: ";
                                    $content = trim(fgets(STDIN));
                                    $pubArticles[$index]->addComment($db->getUser(), $content);
                                    echo "\ncomment added! It is now pending.\n";

                                    $loop = false;
                                } else {
                                    echo "\narticle number not found.\n";
                                }
                                break;

                            default:
                                echo "Please choose a correct number\n";
                                break;
                        }
                    }
                    break;
                case 2:
                    echo "\n My Articles: \n\n";
                    $myArti = $db->getAllArticles($db->getUser());
                    if ($myArti == null) {
                        echo "\n\nthere's no articles \n\n";
                        break;
                    }
                    printf("%-5s %-15s %-10s %-10s %-20s %-20s %-20s %-30s\n",  "id", "title", "status", "comments", "publishedAt", "created At", "updated At", "content");

                    echo str_repeat("=", 150) . "\n";

                    foreach ($myArti as $key => $article) {
                        printf("%-5s %-15s %-10s %-10s %-20s %-20s %-20s %-30s\n", ($key + 1), $article->getTitle(), $article->getStatus(), count($article->getComments()), $article->getPublishedAt(), $article->getCreatedAt(), $article->getUpdatedAt(), $article->getContent());
                    }
                    echo "\n\n";

                    $artiLoop = true;
                    while ($artiLoop) {
                        echo "\n1. modify an article\n";
                        echo "2. delete an article\n";
                        echo "3. publish an article\n";
                        echo "0. Go back to menu\n";
                        echo "\n choose a number: ";

                        $number = (int)trim(fgets(STDIN));
                        switch ($number) {
                            case 0:
                                $artiLoop = false;
                                break;
                            case 1:
                                echo "\nChoose an Article id: ";
                                $idInput = (int)trim(fgets(STDIN));
                                $index = $idInput - 1;

                                if (isset($myArti[$index])) {
                                    echo "Enter new content: ";
                                    $newContent = trim(fgets(STDIN));
                                    $myArti[$index]->updateContent($newContent);
                                    echo "\narticle updated successfully.\n";
                                    $artiLoop = false;
                                } else {
                                    echo "\narticle id not found.\n";
                                }
                                break;

                            case 2:

                                break;

                            case 3:
                                echo "\nChoose an Article id: ";
                                $idInput = (int)trim(fgets(STDIN));
                                $index = $idInput - 1;

                                if (isset($myArti[$index])) {
                                    if ($myArti[$index]->publish()) {
                                        echo "\narticle is now Public!\n";
                                        $artiLoop = false;
                                    } else {
                                        echo "\narticle is already public.\n";
                                    }
                                } else {
                                    echo "\narticle id not found.\n";
                                }
                                break;
                            default:
                                echo "please choose a coorect number\n";
                                break;
                        }
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
