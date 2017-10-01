<?php

class Comment{
    private $id;
    private $userId;
    private $postId;
    private $text;
    private $creationDate;
    
    public function __construct(){
        $this->id = 0;
        $this->userId = "";
        $this->postId = "";
        $this->text = "";
        $this->creationDate = (new \DateTime());
    }
    
    public function getId() {
        return $this->id;
    }
    public function getUserId() {
        return $this->userId;
    }
    public function setUserId($userId) {
        $this->userId = $userId;
    }
    public function getPostId() {
        return $this->postId;
    }
    public function setPostId($postId) {
        $this->postId = $postId;
    }
    public function getText() {
        return $this->text;
    }
    public function setText($text) {
        $this->text = $text;
    }
    public function getCreatedAt() {
        return $this->creationDate;
    }
    public function setCreatedAt($createdAt) {
        if($createdAt instanceof \DateTime) {
            $this->creationDate = $createdAt;
        } else {
            $this->creationDate = new \DateTime($createdAt);
        }
    }
    
    public function getCreatedAtAsText() {
        return $this->getCreatedAt()->format('Y-m-d H:i:s');
    }
    
    public function saveToDB(PDO $conn) {
        if(!$this->id) {
            $stmt = $conn->prepare("INSERT INTO Comment (userId, postId, text, creationDate) VALUES (:userId, :postId, :text, :creationDate)");
            $result = $stmt->execute([
                'userId'=>$this->getUserId(),
                'postId'=>$this->getPostId(),
                'text'=>$this->getText(),
                'creationDate'=>$this->getCreatedAtAsText(),
            ]);
            if($result) {
                $this->id = $conn->lastInsertId();
            }
            } else {
            $stmt = $conn->prepare("UPDATE Comment 
            SET text = :text, userId = :userId, postId= :postId, creationDate = :creationDate
            WHERE id = :id;");
            $result = $stmt->execute([
                'text'=>$this->getText(),
                'userId'=>$this->getUserId(),
                'postId'=>$this->getPostId(),
                'creationDate'=>$this->getCreatedAtAsText(),
            ]);
        }
        return (bool) $result;
    }
    
    public function delete(PDO $conn) {
        if($this->getId()) {
            $stmt = $conn->prepare("DELETE FROM Comment WHERE id=:id");
            $res = $stmt->execute([
                'id'=>$this->getId()
            ]);
            return (bool) $res;
        }
        return false;
    }
    
    static public function getAllCommentsByPostId(PDO $conn, int $id){
        $stmt  = $conn->prepare("SELECT Comment.* FROM Comment JOIN Tweet ON Comment.postId = Tweet.id WHERE Comment.postId =:id ORDER BY Comment.creationDate ASC");
        $result = $stmt->execute(['id'=>$id]);
        if($result == true && $stmt->rowCount() > 0){
            $comments = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $array) {
                $comment = new Comment();
                $comment->id = $array["id"];
                $comment->setUserId($array["userId"]);
                $comment->getPostId($array["postId"]);
                $comment->setText($array["text"]);
                $comment->setCreatedAt($array["creationDate"]);
                $comments[] = $comment;
            }
            return $comments;
        }
        return null;
    }

}

