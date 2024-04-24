<?php


$connect = mysqli_connect('localhost', 'root', '');
if(!$connect){
    echo "<h1>Database Connection Not Stable.</h1>";
    exit();
}

$db = mysqli_select_db($connect, 'ollyo_task');

session_start();


//$_SESSION['type'];
//$_SESSION['user_id'];



// Register session variables (deprecated)
//session_register('type');
//session_register('user_id');
?>
