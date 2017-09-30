<?php

class DB{
    
    public static $conn;
    
    public function __construct()
    {
        if(! self::$conn instanceof \PDO) {
            self::$conn = new PDO("mysql:host=localhost;dbname=twitter;charset=utf8",'root','coderslab',[
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        }
    }
    public function __destruct()
    {
        self::$conn = null;
    }
    
    public static function logUserToDB(PDO $conn, $userEmail, $userPassword) {
        $stmt = $conn->prepare("SELECT * FROM Users WHERE userEmail=:userEmail ");
        $result = $stmt->execute(['userEmail' => $userEmail]);
        if($result == true && $stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if(password_verify($userPassword, $user['userHashedPassword'])) {
                    return $user['id'];
                }
                else {
                    return false;
                }
        }
        else {
            return false;
        }
    }
    
}

