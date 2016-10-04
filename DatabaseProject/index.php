<?php include "Database.php";

echo "Database module";
$db = Database::Inst();

$usr = $db->get('users',['id','','6'])->data();


print_r($usr);