<?php

require_once __DIR__."/../src/DB.php";
require_once __DIR__."/../src/User.php";

$conn = new PDO("mysql:host=localhost",'root','coderslab',[
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$conn->query(file_get_contents(__DIR__."/../sql/main.sql"));

//$db = new DB();
/*$user = new User();
$user->setUserName("Radoslaw");
$user->setUserEmail("nowickiradoslaw@gmail.com");
$user->setUserPassword("1111");

$user->saveToDB(DB::$conn);

var_dump(User::loadUserById(DB::$conn, 2));*/

