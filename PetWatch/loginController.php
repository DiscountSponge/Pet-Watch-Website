
<?php

$loginMessage ="";
require_once("Models/UserDataSet.php");
//$userDataSet = new UserDataSet();
//$view->studentsDataSet = $userDataSet->checkUser();
$view = new StdClass();
$view->pageTitle="Login";
$view->dbMessage="";
if (isset($_POST["loginButton"])) {
    $username = htmlspecialchars($_POST["username"]);
    $password = $_POST["password"]; //Cant use the protection on password in case tge symbols are used in the password



    $newDataSet = new UserDataSet();
    $newDataSet->createUsers();
    $loginMessage="";
    if($newDataSet->checkUser($username,$password)){
        header("Location:index.php"); //tells code to open index page if login is succesful
        exit();
    }

    else{
        $view->dbMessage="Wrong username or password";

    }
}


require_once("Views/login.phtml");