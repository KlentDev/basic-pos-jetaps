<?php

try{
$pdo = new PDO("mysql:host=localhost;dbname=pos_db","root","");
echo "connection Sucessfull";

}catch(PDOException $f){
    
    echo $f->getmessage();
}




//include_once"conectdb.php";
session_start();
if ($_SESSION["useremail"]=="" OR $_SESSION["role"]=="user"){
    
    header("location:index.php");
    
}

$id=$_POST["user_id"];
$sql="delete from tbl_login_history where user_id=$id";
$delete=$pdo->prepare($sql);

if ($delete->execute()){
    
    
}else{
    
    
    echo"error in deleting";
}

?>