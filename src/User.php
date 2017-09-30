<?php

class User {
    private $id;
    private $userName;
    private $userEmail;
    private $userHashedPassword;
    
    public function __construct() {
        
        $this->id = 0;
        $this->userName = '';
        $this->userEmail = '';
        $this->userHashedPassword = '';
        
    }
       
    public function getId() {
        return $this->id;
    }
    
    public function setUserName($userName) {
        $this->userName = $userName;
    }
    
    public function getUserName() {
        return $this->userName;
    }
    
    public function setUserEmail($userEmail) {
        $this->userEmail = $userEmail;
    }
    
    public function getUserEmail() {
        return $this->userEmail;
    }
    
    public function setUserPassword($userPassword) {
        $hashPassword = password_hash($userPassword, PASSWORD_BCRYPT);
        $this->userHashedPassword = $hashPassword;
    }
    
    public function getUserPassword() {
        return $this->userHashedPassword;
    }
    
    public function saveToDB(PDO $conn) {
        if($this->id == 0) {
            $stmt = $conn->prepare("INSERT INTO Users(userName, userEmail, userHashedPassword)
                                              VALUES (:userName, :userEmail, :userPassword)");
            $result = $stmt->execute(['userName' => $this->userName,
                                      'userEmail' => $this->userEmail,
                                      'userPassword' => $this->userHashedPassword
                                    ]);
        
            if($result !== false) {
                $this->id = $conn->lastInsertId();
                return true;
            }
            return false;
        }
        else {
            $stmt = $conn->prepare("UPDATE Users SET userName=:userName, userHashedPassword=:userPassword, userEmail=:userEmail WHERE id=:id");
            $result = $stmt->execute([
                                       'userName' => $this->userName,
                                       'userEmail' => $this->userEmail,
                                       'userHashedPassword' => $this->userHashedPassword,                                     
                                       'id' => $this->id
                                    ]);
            if($result === true) {
                return true;
            }
        }
        return false;
    }
    
    public function delete (PDO $conn){
        if ($this->id != 0){
            $stmt = $conn->prepare('DELETE FROM Users WHERE id=:id');
            $result = $stmt->execute(['id' => $this->id]);
            if ($result === true) {
                $this->id = 0;
                return true;
            }
            return false;
        }
        return true;
    }
    
    static public function getUserById(PDO $conn, $id){
        $stmt = $conn->prepare('SELECT * FROM Users WHERE id=:id');
        $result = $stmt->execute(['id' => $id]);
        if ($result === true && $stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $loadedUser = new User();
            $loadedUser->id = $row['id'];
            $loadedUser->userName = $row['userName'];
            $loadedUser->userEmail = $row['userEmail'];
            $loadedUser->userHashedPassword = $row['userHashedPassword'];
            
            return $loadedUser;
        }
        return	null;
    }
    
    static public function getAllUsers(PDO $conn) {
        $ret = [];
        $sql = "SELECT * FROM Users";
        $result = $conn->query($sql);
        if ($result !== false && $result->rowCount() != 0) {
            foreach ($result as $row) {
                $loadedUser = new User();
                $loadedUser->id	= $row['id'];
                $loadedUser->userName = $row['userName'];
                $loadedUser->userEmail = $row['userEmail'];
                $loadedUser->userHashedPassword = $row['userHashedPassword'];
                $ret[] = $loadedUser;
            }
        }
        return	$ret;
    }
        
}

