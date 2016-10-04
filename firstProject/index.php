<?php
include_once 'classes/App.php';
$db = DB::getIns();

//$db->delete('users', [['age' ,'=', 24]]);
echo "<br>";
//  $db->create('users',['firstName' => 'Ilir', 'lastName' => 'Gojani', 'age' => 24]);
//var_dump($db->_inserted_id);


$select = $db->select(['users.firstName','users.lastName'], ['users', 'tasks'],[['user.id','=','tasks.user_id']]);
