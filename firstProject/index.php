<?php
include_once 'classes/App.php';
echo "<br>====================== The INSTANCE OF DATABASE ===================<br>";
$db = DB::getIns();
echo "<br>===================================================================<br>";
echo "<br>====================== The SELECT QUERY ===========================<br>";
$select = $db->select(
    [
        'users.id',                 //  Fields
        'tasks.id',                 //  For
        'users.firstName',          //  Selecting
        'users.lastName',           //  of
        'users.age',                //  Tables
        'tasks.taskName'            //
    ],
    [
        "users",                    //  Tables
        'inner join' => 'tasks'     //  To Select
    ],
    [
       "WHERE"=> [ 'users.id', '=','tasks.user_id']#, ","=> ['users.firstName','=','valon']
    ],
    [10],                           //  The limit of rows
    [
        'users.id',                 //  Group by
        'tasks.id'
    ],                //
    null,
    [
        'users.id' => 'desc',       //  ORders
        'tasks.id'=> 'asc'          //  OF rows
    ]
);
echo "<br>===================================================================<br>";
echo "<br>====================== The CREATE QUERY ===========================<br>";
$db->create(
'users',
 [
     'firstName' => 'Ilir',  // Values of
     'lastName' => 'Gojani',    //  Inserted
     'age'=> '24',
     'phone' => '0745587412'//  Query
 ]
);
echo "<br>===================================================================<br>";
echo "<br>====================== The DELETE QUERY ===========================<br>";
$db->delete(
'users',            // Selected table for finding the word
 [['firstName' , '=', 'valoni1'],'OR'=>  ['firstName', '=', 'Ilir']]    // Condition to delete rows
);
echo "<br>===================================================================<br>";
echo "<br>====================== The UPDATE QUERY ===========================<br>";
$db->update(
    ['users'],
    [
        'firstName' => 'Is updated',        // qValyes of
        'lastName' => 'Last name Updated'   // UPDATE query
    ],
    [
        ['id', '=', 4],                     // The Condition
        ['firstName','=','valon']           // for selecting rows
    ]
);
echo "<br>===================================================================<br>";
echo "<br>====================== The RESULTS QUERY ==========================<br>";
// For select Query
//foreach ($select as $key => $rows){
//    if (is_integer($key)){
//        echo "<pre>";
//        print_r($rows);
//        echo "</pre>";
//    }
//}

//---------------------------------------------------------------------------
echo "<br>===================================================================<br>";













