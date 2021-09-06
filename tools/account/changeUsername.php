<?php
include "../../incl/lib/connection.php";
require "../../incl/lib/generatePass.php";
require_once "../../incl/lib/exploitPatch.php";
//here im getting all the data
$userName = ExploitPatch::remove($_POST["userName"]);
$newusr = ExploitPatch::remove($_POST["newusr"]);
$password = ExploitPatch::remove($_POST["password"]);
if($userName != "" AND $newusr != "" AND $password != ""){
	$pass = GeneratePass::isValidUsrname($userName, $password);
	if ($pass == 1) {
		$query = $db->prepare("UPDATE accounts SET username=:newusr WHERE userName=:userName");	
		$query->execute([':newusr' => $newusr, ':userName' => $userName]);
		if($query->rowCount()==0){
			echo "Invalid password or nonexistant account. <a href='changeUsername.php'>Try again</a>";
		}else{
			echo "Username changed. <a href='accountManagement.php'>Go back to account management</a>";
		}
	}else{
		echo "Invalid password or nonexistant account. <a href='changeUsername.php'>Try again</a>";
	}
}else{
	echo '<form action="changeUsername.php" method="post">Old username: <input type="text" name="userName"><br>New username: <input type="text" name="newusr"><br>Password: <input type="password" name="password"><br><input type="submit" value="Change"></form>';
}
?>