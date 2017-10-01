<?php

require_once __DIR__.'/User.php';
require_once __DIR__.'/Tweet.php';
require_once __DIR__.'/Comment.php';
require_once __DIR__.'/DB.php';


class Controller
{
    private $db;
    static $conn;
    public function __construct()
    {
        $this->db = new DB();
        if(! self::$conn instanceof \PDO) {
            self::$conn = new PDO("mysql:host=localhost;dbname=twitter;charset=utf8",'root','coderslab',[
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        }
    }
    
    private function render($template,$data)
    {
        $html = file_get_contents(__DIR__."/../template/".$template.".html");
        foreach ($data as $key => $value) {
            $html = str_replace('{{'.$key.'}}',$value,$html);
        }
        return $html;
    }
    
    public function showMainPage()
    {
        return file_get_contents(__DIR__."/../template/homepage.html");
    }
    
    public function showAllTweets($data = ["error"=>""]) {
        $tweets = Tweet::getAllTweets(DB::$conn);
        $html = '';
        foreach ($tweets as $tweet) {
            if($tweet instanceof Tweet) {
                $html .= $this->render('tweet', [
                    'id' => $tweet->getId(),
                    'text' =>$tweet->getText(),
                    'creationDate' =>$tweet->getCreatedAtAsText(),
                    'userName' => User::getUserById(DB::$conn, $tweet->getUserId())->getUserName()
                ]);
            }
        }
        
        return $this->render('tweets',array_merge(
            ['content' => $html],
            $data
        ));
    }
    
    public function register($data = ["error"=>""]){
        $html = '';
        return $this->render('register',$data);
    }
    
    public function showTweet(int $id){
        
        $tweet = Tweet::getTweetById(DB::$conn, $id);
        $comments = Comment::getAllCommentsByPostId(DB::$conn, $id);
               
/*        $htmlTweet = $this->render('singleTweet', [
            'id' => $tweet->getId(),
            'text' => $tweet->getText(),
            'creationDate' => $tweet->getCreatedAtAsText(),
            'userName' => User::getUserById(DB::$conn, $tweet->getUserId())->getUserName()
        ]);*/
        
        $html = '';
        foreach ($comments as $comment) {
            if($comment instanceof Comment) {
                $html .= $this->render('comment', [
                    'id' => $comment->getId(),
                    'text' => $comment->getText(),
                    'creationDate' => $comment->getCreatedAtAsText(),
                    'userName' => User::getUserById(DB::$conn, $comment->getUserId())->getUserName()
                ]);
            }
        }
  
        return $this->render('singleTweet', array_merge(
                ['content' => $html],
                ['id' => $tweet->getId(),
                'text' => $tweet->getText(),
                'creationDate' => $tweet->getCreatedAtAsText(),
                'userName' => User::getUserById(DB::$conn, $tweet->getUserId())->getUserName()
                ]
                ));       
    }
    
    
}
