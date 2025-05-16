<?php

try{
$pdo = new PDO("mysql:host=localhost;dbname=pos_db","root","");
echo "connection Sucessfull";

}catch(PDOException $f){
    
    echo $f->getmessage();
}





session_start();
if ($_SESSION["useremail"]=="" OR $_SESSION["role"]=="user"){
    
    header("location:index.php");
    
}

$id=$_POST["userid"];
$sql="delete from tbl_user where userid=$id";
$delete=$pdo->prepare($sql);

if ($delete->execute()){
    
    
}else{
    
    
    echo"error in deleting";
}

?>