<?php
include_once 'classes/App.php';
echo "<br>======================[ The INSTANCE OF DATABASE ]===================<br>";
$db = DB::getIns();
//$db = new DB();
echo "<br>===================================================================<br>";
echo "<br>======================[ The SELECT QUERY ]===========================<br>";
$db->select(
    [
        "users.id",                 //  Fields
        "tasks.id",                 //  For
        "users.firstName",          //  Selecting
        "users.lastName",           //  of
        "users.age",                //  Tables
        "tasks.taskName"            //
    ],

    [
        "users",
        'tasks'//  To Select
    ],
    [
        ['INNER JOIN'],
        [ "users.id", "=", 2]#, ","=> ['users.firstName','=','valon']
//        "AND" => [ "users.id", "=", 2]#, ","=> ['users.firstName','=','valon']
    ],
    [10],                           //  The limit of rows
//    [
//        "users.id",                 //  Group by
//        "tasks.id"
//    ],                //
    null,
    null,
    [
        "users.id"    => "desc",       //  ORders
//        "tasks.id"    => "asc"          //  OF rows
    ]
);
echo "<br>===================================================================<br>";
echo "<br>======================[ The CREATE QUERY ]===========================<br>";
$db->create(
'users',
 [
     'firstName'  => 'Filan ',  // Values of
     'lastName'   => 'Fisteku',    //  Inserted
     'age'        => '24',
     'phone'      => '0745587412'//  Query
 ]
);
echo "<br>===================================================================<br>";
echo "<br>======================[ The DELETE QUERY ]===========================<br>";
$db->delete(
'users',            // Selected table for finding the word
 [['id' , '>', 4]]    // Condition to delete rows
);
echo "<br>===================================================================<br>";
echo "<br>======================[ The UPDATE QUERY ]===========================<br>";
$db->update(
    ['users','tasks'],
    [
        'users.firstName' => 'tasks.taskDescription',         // qValyes of
        'users.lastName'  => 'Hajredini',
        'users.age'       => 28,                 // UPDATE query
        'tasks.taskDescription' => '1234567890'
    ],
    [
        ['WHERE'],
        ['users.id', '=', 'tasks.user_id']
//        ['tasks.id', '=', 1]
//        "AND"   => ['users.id', '<',10]// The Condition
        // for selecting rows
    ]
);
echo "<br>===================================================================<br>";
