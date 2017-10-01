<?php

class Tweet
{
    private $id;
    private $userId;
    private $user;
    private $text;
    private $creationDate;

    public function __construct()
    {
        $this->id = 0;
        $this->userId = "";
        $this->text = "";
        $this->creationDate = (new \DateTime());
    }

   
    public function getId()
    {
        return $this->id;
    }
    public function getUserId()
    {
        return $this->userId;
    }
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
    public function getText()
    {
        return $this->text;
    }
    public function setText($text)
    {
        $this->text = $text;
    }
    public function getUser()
    {
        return $this->user;
    }
    public function setUser(User $user)
    {
        $this->user = $user;
        $this->setUserId($user->getId());
    }
    public function getCreatedAt()
    {
        return $this->creationDate;
    }
    public function setCreatedAt($createdAt)
    {
        if($createdAt instanceof \DateTime) {
            $this->creationDate = $createdAt;
        } else {
            $this->creationDate = new \DateTime($createdAt);
        }
    }
    
    public function getCreatedAtAsText()
    {
        return $this->getCreatedAt()->format('Y-m-d H:i:s');
    }
    
    public function saveToDB(PDO $conn) {
        if(!$this->id) {
            $stmt = $conn->prepare("INSERT INTO Tweet (userId, text, creationDate) VALUES (:userId, :text, :creationDate)");
            $result = $stmt->execute([
                'userId'=>$this->getUserId(),
                'text'=>$this->getText(),
                'creationDate'=>$this->getCreatedAtAsText(),
            ]);
            if($result) {
                $this->id = $conn->lastInsertId();
            }
            } else {
            $stmt = $conn->prepare("UPDATE Tweet 
            SET text = :text, userId = :userId, creationDate = :creationDate
            WHERE id = :id;");
            $result = $stmt->execute([
                'text'=>$this->getText(),
                'userId'=>$this->getUserId(),
                'creationDate'=>$this->getCreatedAtAsText(),
            ]);
        }
        return (bool) $result;
    }
    
    public function delete(PDO $conn) {
        if($this->getId()) {
            $stmt = $conn->prepare("DELETE FROM Tweet WHERE id=:id");
            $res = $stmt->execute([
                'id'=>$this->getId()
            ]);
            return (bool) $res;
        }
        return false;
    }
    
    static public function getAllTweetsByUserId(PDO $conn, int $id){
        $stmt  = $conn->prepare("SELECT * FROM Tweet WHERE userId=:id");
        $result = $stmt->execute(['id'=>$id]);
        if($result == true && $stmt->rowCount() > 0){
            $tweets = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $array) {
                $tweet = new Tweet();
                $tweet->id = $array["id"];
                $tweet->setUserId($array["userId"]);
                $tweet->setText($array["text"]);
                $tweet->setCreatedAt($array["creationDate"]);
                $tweets[] = $tweet;
            }
            return $tweets;
        }
        return null;
    }
    
    static public function getAllTweets(PDO $conn){
        $allTweets = [];
        $result = $conn->query("SELECT Tweet.* FROM Tweet JOIN Users ON Tweet.userId = Users.id ORDER BY Tweet.creationDate ASC");

        if($result == true && $result->rowCount() > 0){

            foreach ($result as $array){
                $tweet = new Tweet();
                $tweet->id = $array["id"];
                $tweet->setUserId($array["userId"]);
                $tweet->setText($array["text"]);
                $tweet->setCreatedAt($array["creationDate"]);
                $allTweets[] = $tweet;
            }
            return $allTweets;
        }
        return null;   
    }
    
    static public function getTweetById(PDO $conn, int $id){
        $stmt  = $conn->prepare("SELECT * FROM Tweet WHERE id=:id");
        $result = $stmt->execute(['id'=>$id]);
        if($result == true && $stmt->rowCount() > 0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $tweet = new Tweet();
            $tweet->id = $row["id"];
            $tweet->setUserId($row["userId"]);
            $tweet->setText($row["text"]);
            $tweet->setCreatedAt($row["creationDate"]);
       
            return $tweet;
        }
        return null;
    }
        
}

