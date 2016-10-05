<?php
include_once 'classes/App.php';
$db = DB::getIns();


echo "<br>====================== The SELECT QUERY ============================<br>";
//$select = $db->select(['users.id','tasks.id','users.firstName', 'users.lastName', 'users.age', 'tasks.taskName'],["users", 'inner join' => 'tasks'],[['users.id','=','tasks.user_id'],['users.firstName','=','valon']],[10],['users.id', 'tasks.id'], null,['users.id' => 'desc', 'tasks.id'=> 'asc']);
//foreach ($select as $key => $rows){
//    if (is_integer($key)){
//        echo "<pre>";
//        print_r($rows);
//        echo "</pre>";
//    }
//}
echo "OK";
echo "<br>===================================================================<br>";
echo "<br>====================== The CREATE QUERY============================<br>";
//$db->create(
//'users',
// ['firstName' => 'Ekoloni','lastName' => 'Ekopro', 'age'=> '111', 'phone' => '+445241521']
//);
echo "OK";
echo "<br>===================================================================<br>";
echo "<br>====================== The DELETE QUERY============================<br>";
//$db->delete(
//'users',
// ['id', '>', 4 ]
//);
echo "OK";
echo "<br>===================================================================<br>";





















