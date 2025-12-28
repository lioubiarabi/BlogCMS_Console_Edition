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

    public function addComment($username, $content)
    {
        $this->comments[] = ['username' => $username, 'content' =>  $content, 'status' => 'pending', 'createdAt' => (new DateTime())->format('Y-m-d H:i')];
    }

    public function approveComment($index)
    {
        if (isset($this->comments[$index])) {
            $this->comments[$index]['status'] = 'approved';
            return true;
        }
        return false;
    }

    public function refuseComment($index)
    {
        if (isset($this->comments[$index])) {
            $this->comments[$index]['status'] = 'refused';
            return true;
        }
        return false;
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
    public function getAuthor()
    {
        return $this->author;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function getComments($condition = 'all')
    {
        if ($condition == 'all') return $this->comments;
        return array_filter($this->comments, function ($comment) use ($condition) {
            return $comment['status'] === $condition;
        });
    }
    public function getCreatedAt()
    {
        return  $this->createdAt->format('Y-m-d H:i');
    }
    public function getPublishedAt()
    {
        if ($this->publishedAt === null) {
            return "Not Published";
        }
        return $this->publishedAt->format('Y-m-d H:i');
    }
    public function getUpdatedAt()
    {
        return $this->updatedAt->format('Y-m-d H:i');
    }
}

class Category
{
    private int $id;
    private string $name;
    private string $description;
    private ?int $parentId;
    private array $articles = [];
    private DateTime $createdAt;

    public function __construct($id, $name, $description, $parentId = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->parentId = $parentId;
        $this->createdAt = new DateTime();
    }


    public function addArticle(Article $article)
    {
        $this->articles[] = $article;
    }

    public function removeArticle($articleId)
    {
        foreach ($this->articles as $key => $article) {
            if ($article->getId() == $articleId) {
                unset($this->articles[$key]);
                $this->articles = array_values($this->articles);
                return true;
            }
        }
        return false;
    }

    public function getArticles()
    {
        return $this->articles;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getParentId()
    {
        return $this->parentId;
    }
}
