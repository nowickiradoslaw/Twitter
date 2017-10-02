<?php

require __DIR__.'/../src/Controller.php';

$uri = $_SERVER["REQUEST_URI"];
$controller = new Controller();
$res = '';
if($uri === "/Twitter/web/") {
    if($_SERVER["REQUEST_METHOD"] === "POST"){
        
        $userEmail = isset($_POST['userEmail']) ? trim($_POST['userEmail']) : null;
        $userPassword = isset($_POST['userPassword']) ? trim($_POST['userPassword']) : null;
        
        $loggedUserId = DB::logUserToDB(DB::$conn, $userEmail, $userPassword);
        
        if($loggedUserId) {
            
            session_start();
            $_SESSION['loggedUserId'] = $loggedUserId;
                        
            $res = $controller->showAllTweets();
            
        }
        else { 
            $res = $controller->showMainPage();   
        }
    }
    else {
        $res = $controller->showMainPage();
    }
}
elseif($uri === "/Twitter/web/?tweets") {
    session_start();
    if($_SESSION["loggedUserId"]){
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            
            if(!isset($_POST["text"]) OR !strlen($_POST["text"])) {
                $res = $controller->showAllTweets(["error"=>"Formularz jest pusty!"]);
            }
            else{
                $tweet = new Tweet();
                $tweet->setText($_POST["text"]);
                $tweet->setUserId($_SESSION["loggedUserId"]);
                $tweet->saveToDB(DB::$conn);
                $res = $controller->showAllTweets();
            }
                        
        } else {
            $res = $controller->showAllTweets();
        }
    }
    else {
        $res = $controller->showMainPage();
    }
    
    } elseif ($uri === "/Twitter/web/?register"){
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            
            session_start();
            
            if(!isset($_POST["userName"]) || !strlen($_POST["userName"])) {
                $res = $controller->register(["error"=>"Podaj imię"]);
            }
            elseif(!isset($_POST["userEmail"]) || !strlen($_POST["userEmail"])) {
                $res = $controller->register(["error"=>"Podaj e-mail!"]);
            }

            elseif(!isset($_POST["userPassword"]) || !strlen($_POST["userPassword"])) {
                $res = $controller->register(["error"=>"Podaj hasło!"]);
            }
            else{

                $user = new User();
                $user->setUserName($_POST["userName"]);
                $user->setUserEmail($_POST["userEmail"]);
                $user->setUserPassword($_POST["userPassword"]);
                $user->saveToDB(DB::$conn);
                $_SESSION['loggedUserId'] = $user->getId();
                $res = $controller->showAllTweets();
                
            }

        } else {
            $res = $controller->register();
        }
    
   
} 

elseif (preg_match('/\/\?tweet=\d+/',$uri)) {

    session_start();
    if($_SESSION["loggedUserId"]){
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            
            if(!isset($_POST["text"]) OR !strlen($_POST["text"])) {
                $res = $controller->showTweet($_GET['tweet']);
            }
            else{
                $comment = new Comment();
                $comment->setText($_POST["text"]);
                $comment->setUserId($_SESSION["loggedUserId"]);
                $comment->setPostId($_GET['tweet']);
                $comment->saveToDB(DB::$conn);
                $res = $controller->showTweet($_GET['tweet']);
            }
                        
        } else {
            $res = $controller->showTweet($_GET['tweet']);
        }
    }
    else {
        
        $res = $controller->showTweet($_GET['tweet']);
        
    }
}


elseif (preg_match('/\/\?user=\d+/',$uri)) {

    session_start();
    if($_SESSION["loggedUserId"]){

        if($_SERVER["REQUEST_METHOD"] === "GET") {
                       
            if(isset($_GET["user"])) {
                $res = $controller->showUserTweets($_GET['user']);
            }
        }
        else {

            $res = $controller->showAllTweets();

        }
    }
    else {
        $res = $controller->register ();
    }
}

echo $res;

