<?php

require 'file.php';

class collection
{
    private ?User $current_user;
    private array $users = [];
    private array $categories = [];
    private int $newArticle = 10;
    private int $newCategory = 10;
    private int $newUser = 10;

    public function __construct()
    {

        $this->users = [
            "author" => new Author(1, "author", "author", "author", "this author hehe"),
            "user2" => new Author(2, "user2", "user2@email.com", "password", "this author hehe"),
            "user3" => new Author(3, "user3", "user3@email.com", "password", "this author hehe"),
            "editor" => new Editor(4, "editor", "editor", "editor", "this Editor hehe"),
            "admin" => new Admin(5, "admin", "admin", "admin", "this Editor hehe"),
        ];

        $this->categories = [
            new Category(1, "Techno", "this is all about techno"),
            new Category(2, "coding", "this is all about techno/coding", 1),
            new Category(3, "php", "this is all about techno/coding/php", 2),
            new Category(4, "Learn", "this is all about Learn"),

        ];

        $this->categories[0]->addArticle(new Article(1, 'title1', 'content1', 'user1'));
        $this->categories[2]->addArticle(new Article(2, 'title2', 'content2', 'user2'));
        $this->categories[3]->addArticle(new Article(3, 'title3', 'content3', 'user3'));

        // temporary
        //$this->current_user = null;
        $this->current_user = $this->users['admin'];
    }


    public function login($username, $password)
    {
        foreach ($this->users as $user) {
            if ($user->auth($username, $password)) {
                $this->current_user = $user;
                return true;
            }
        }
        return false;
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

    public function searchAuthor($username)
    {
        foreach ($this->users as $user) {
            if ($user->getRole() == "Author")
                if ($user->username == $username) return true;
        }
        return false;
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

    public function listCategories()
    {
        echo "\nselect a Category: \n";
        $roots = [];
        $children = [];

        foreach ($this->categories as $cat) {
            if ($cat->getParentId() == null) {
                $roots[] = $cat;
            } else {
                $children[$cat->getParentId()][] = $cat;
            }
        }

        foreach ($roots as $root) {
            printf("ID: %-3d | %s\n", $root->getId(), $root->getName());
            if (isset($children[$root->getId()])) {
                foreach ($children[$root->getId()] as $child) {
                    printf("       |__ ID: %-3d | %s\n", $child->getId(), $child->getName());
                }
            }
        }
        echo "-------------------------\n";
    }

    public function createCategory($name, $description, $parentId = null)
    {
        $newId = $this->newCategory++;

        $newCat = new Category($newId, $name, $description, $parentId);
        $this->categories[] = $newCat;
        return true;
    }

    public function updateCategory($categoryId, $name)
    {
        foreach ($this->categories as $key => $category) {
            if ($category->getId() == $categoryId) {
                $this->categories[$key]->update($name);
                return true;
            }
        }
        return false;
    }

    public function removeCategory($categoryId)
    {
        foreach ($this->categories as $key => $category) {
            if ($category->getId() == $categoryId) {
                unset($this->categories[$key]);
                $this->categories = array_values($this->categories);
                return true;
            }
        }
        return false;
    }

    public function createArticle($title, $content, $catId, $authorName)
    {
        $targetCategory = null;
        foreach ($this->categories as $cat) {
            if ($cat->getId() == $catId) {
                $targetCategory = $cat;
                break;
            }
        }

        if ($targetCategory) {
            $targetCategory->addArticle(new Article($this->newArticle++, $title, $content, $authorName));
            return true;
        }

        return false;
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
            case 'all':
                foreach ($this->categories as $category) {
                    foreach ($category->getArticles() as $article) {
                        $result[] = $article;
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
            echo "3. create an article\n";
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
                        echo "\n1. read comments and write one\n";
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
                                echo "\nwhich article to delete: ";
                                $numInput = (int)trim(fgets(STDIN));
                                $index = $numInput - 1;

                                if (isset($myArti[$index])) {
                                    if ($db->deleteArticle($myArti[$index]->getId())) {
                                        echo "\narticle deleted.\n\n";
                                        $artiLoop = false;
                                    } else {
                                        echo "\nnot supposed to happen\n";
                                    }
                                } else {
                                    echo "\narticle number not found.\n";
                                }

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
                case 3:
                    echo "Enter title: ";
                    $newTitle = trim(fgets(STDIN));

                    echo "Enter content: ";
                    $newContent = trim(fgets(STDIN));

                    $db->listCategories();

                    echo "\nChoose Category ID: ";
                    $catId = (int)trim(fgets(STDIN));
                    if ($db->createArticle($newTitle, $newContent, $catId, $db->getUser())) {
                        echo "\narticle created successfully!\n";
                    } else {
                        echo "\ncategory ID not found and article was not saved.\n";
                    }
                    break;

                default:
                    echo "Please choose a correct number";
                    break;
            }
            break;

        case 'Editor':
        case 'Admin':
            echo "\n1. manage articles\n";
            echo "2. create an article\n";
            echo "3. manage categories\n";
            if ($db->getRole() == "Admin") echo "4. manage users\n";
            echo "0. logout\n";
            echo "\n choose a number: ";

            switch (trim(fgets(STDIN))) {
                case 0:
                    $db->logout();
                    break;
                case 1:
                    echo "\n My Articles: \n\n";
                    $allArti = $db->getAllArticles('all');
                    if ($allArti == null) {
                        echo "\n\nthere's no articles \n\n";
                        break;
                    }
                    printf("%-5s %-15s %-10s %-10s %-20s %-20s %-20s %-10s %-30s\n",  "id", "title", "status", "comments", "publishedAt", "created At", "updated At", "auhtor", "content");

                    echo str_repeat("=", 150) . "\n";

                    foreach ($allArti as $key => $article) {
                        printf("%-5s %-15s %-10s %-10s %-20s %-20s %-20s %-10s %-30s\n", ($key + 1), $article->getTitle(), $article->getStatus(), count($article->getComments()), $article->getPublishedAt(), $article->getCreatedAt(), $article->getUpdatedAt(), $article->getAuthor(), $article->getContent());
                    }
                    echo "\n\n";

                    $artiLoop = true;
                    while ($artiLoop) {
                        echo "\n1. modify an article\n";
                        echo "2. delete an article\n";
                        echo "3. publish an article\n";
                        echo "4. manage comments\n";
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

                                if (isset($allArti[$index])) {
                                    echo "Enter new content: ";
                                    $newContent = trim(fgets(STDIN));
                                    $allArti[$index]->updateContent($newContent);
                                    echo "\narticle updated successfully.\n";
                                    $artiLoop = false;
                                } else {
                                    echo "\narticle id not found.\n";
                                }
                                break;

                            case 2:
                                echo "\nwhich article to delete: ";
                                $numInput = (int)trim(fgets(STDIN));
                                $index = $numInput - 1;

                                if (isset($allArti[$index])) {
                                    if ($db->deleteArticle($allArti[$index]->getId())) {
                                        echo "\narticle deleted.\n\n";
                                        $artiLoop = false;
                                    } else {
                                        echo "\nnot supposed to happen\n";
                                    }
                                } else {
                                    echo "\narticle number not found.\n";
                                }

                                break;

                            case 3:
                                echo "\nChoose an Article id: ";
                                $idInput = (int)trim(fgets(STDIN));
                                $index = $idInput - 1;

                                if (isset($allArti[$index])) {
                                    if ($allArti[$index]->publish()) {
                                        echo "\narticle is now Public!\n";
                                        $artiLoop = false;
                                    } else {
                                        echo "\narticle is already public.\n";
                                    }
                                } else {
                                    echo "\narticle id not found.\n";
                                }
                                break;

                            case 4:
                                echo "\nwhich article number: ";
                                $articleIndex = (int)trim(fgets(STDIN));
                                $index = $articleIndex - 1;

                                if (isset($pubArticles[$index])) {
                                    $commentLoop = true;
                                    while ($commentLoop) {
                                        printf("\n%-5s %-15s %-30s %-10s %-20s\n", "id", "Username", "Comment", "status", "Created At");

                                        echo str_repeat("=", 70) . "\n";

                                        $comments = $pubArticles[$index]->getComments('all');
                                        if (count($comments) == 0) echo "no comments yet\n";
                                        foreach ($comments as $key => $comment) {
                                            printf("%-5d %-15s %-30s %-10s %-20s\n", ($key + 1), $comment['username'], $comment['content'], $comment['status'], $comment['createdAt']);
                                        }
                                        echo "\n1. approve a comment\n";
                                        echo "2. refuse a comment\n";
                                        echo "0. Go back to menu\n";
                                        echo "\n choose a number: ";

                                        switch (trim(fgets(STDIN))) {
                                            case 0:
                                                $commentLoop = false;
                                                break;

                                            case 1:
                                                echo "\nEnter comment id to APPROVE: ";
                                                $cIndex = (int)trim(fgets(STDIN)) - 1;

                                                if ($pubArticles[$index]->approveComment($cIndex)) {
                                                    echo "\ncomment approved.\n";
                                                } else {
                                                    echo "\ncomment number not found.\n";
                                                }
                                                break;

                                            case 2:
                                                echo "\nEnter comment id to REFUSE:";
                                                $cIndex = (int)trim(fgets(STDIN)) - 1;

                                                if ($pubArticles[$index]->refuseComment($cIndex)) {
                                                    echo "\ncomment refused.\n";
                                                } else {
                                                    echo "\ncomment number not found.\n";
                                                }
                                                break;

                                            default:
                                                echo "\nPlease choose a correct number\n";
                                                break;
                                        }
                                    }
                                } else {
                                    echo "\narticle number not found.\n";
                                }
                                break;
                            default:
                                echo "please choose a coorect number\n";
                                break;
                        }
                    }

                    break;
                case 2:
                    echo "Enter title: ";
                    $newTitle = trim(fgets(STDIN));

                    echo "Enter content: ";
                    $newContent = trim(fgets(STDIN));

                    echo "Enter the Author username: ";
                    $authorName = trim(fgets(STDIN));

                    if (!$db->searchAuthor($authorName)) {
                        echo "there's no athor with this username\n\n";
                        break;
                    }

                    $db->listCategories();

                    echo "\nChoose Category ID: ";
                    $catId = (int)trim(fgets(STDIN));
                    if ($db->createArticle($newTitle, $newContent, $catId, $authorName)) {
                        echo "\narticle created successfully!\n";
                    } else {
                        echo "\ncategory ID not found and article was not saved.\n";
                    }
                    break;

                case 3:
                    $loop = true;
                    while ($loop) {
                        $db->listCategories();
                        echo "\n1. creat new main category\n";
                        echo "2. creat new sub-category\n";
                        echo "3. update a category\n";
                        echo "4. delete a category\n";
                        echo "0. Go back to menu\n";
                        echo "choose a number:";

                        switch (trim(fgets(STDIN))) {
                            case 0:
                                $loop = false;
                                break;
                            case 1:
                                echo "Enter new category name: ";
                                $name = trim(fgets(STDIN));
                                echo "Enter new category description: ";
                                $description = trim(fgets(STDIN));

                                $db->createCategory($name, $description, null);
                                echo "\n main category created.\n";
                                break;

                            case 2:
                                echo "enter parent category id: ";
                                $pid = (int)trim(fgets(STDIN));
                                echo "Enter new sub-category name: ";
                                $name = trim(fgets(STDIN));
                                echo "Enter new category description: ";
                                $description = trim(fgets(STDIN));

                                $db->createCategory($name, $description, $pid);
                                echo "\nsub-category created.\n";
                                break;

                            case 3:
                                echo "choose a category Id: ";
                                $choosednId = (int)trim(fgets(STDIN));

                                echo "choose new category name: ";
                                $choosenName = trim(fgets(STDIN));

                                if ($db->updateCategory($choosednId, $choosenName)) echo "catgeory updated successfuly!";
                                else echo "category wasn't found!\n";
                                break;
                            case 4:
                                echo "choose a category Id: ";
                                $choosednId = (int)trim(fgets(STDIN));

                                if ($db->removeCategory($choosednId)) echo "catgeory deleted successfuly!";
                                else echo "category wasn't found!\n";
                                break;

                            default:
                                echo "Please choose a correct number!";
                                break;
                        }
                    }
                    break;
                case 4:
                    if ($db->getRole() == "Admin") {
                    } else {
                        echo "You don't have the access to manage users\n\n";
                    }
                    break;
                default:
                    echo "Please choose a correct number";
                    break;
            }
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
                        echo "\n1. read comments and write one\n";
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
                    echo "Entre your username: ";
                    $username = trim(fgets(STDIN));
                    echo "Entre your password: ";
                    $password = trim(fgets(STDIN));

                    echo ($db->login($username, $password)) ? "Welcome back!\n" : "Wrong username/password!\n";
                    break;

                default:
                    echo "Please choose a correct number";
                    break;
            }
            break;
    }
}
