<?php
include_once 'classes/App.php';
$db = Database::getIns();

//$db->create('users', ['name' => "Valon.xh ", 'lastname' => '1234567','birthday' => '2015-05-21']);

//$db->get('users', ['lastname' => 'Haj'])->results();

//$db->delete('users', ['id', '=', 8]);
// SELECT *
# FROM shenimet_zhurnalit
# WHERE
# GROUP BY
# ORDER BY [asc|desc];
# HAVING [AVG| MIN| MAX| COUNT| SUM]
# LIMIT [1, 2]| 1
$db->select(
    ['users.name','users.lastname','users.birthday','tasks.taskName'],
    ['users','tasks'],
    ['users.id', '=','tasks.user_id'],
    ['LIMIT'=>[3,2], 'ORDER BY' =>['name','desc'], 'HAVING'=> ['ekoloni']]
);

//$db->select(
//    ['name','lastname','birthday'],
//    ['users'],
//    ['users.id', '=',3]
//);