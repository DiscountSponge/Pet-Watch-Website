
<?php
$view = new StdClass();
$view->pageTitle="Password";
require_once("Views/password.phtml");
require_once("Models/PasswordDataSet.php");
//$userDataSet = new UserDataSet();
//$view->studentsDataSet = $userDataSet->checkUser();
if (isset($_POST["loginButton"])) {
    $username = $_POST["userID"];
    $password = $_POST["password"];



    $newDataSet = new PasswordDataSet();
    $newDataSet->createUsers();
    $loginMessage="";
    if($newDataSet->checkUser($username,$password)){
        $newDataSet->hashPassword();
        header("Location:index.php"); //tells code to open index page if login is succesful

        exit();
    }

    else{
        $loginMessage="Wrong username or password";
        echo $username;
        echo $password;
    }
}


