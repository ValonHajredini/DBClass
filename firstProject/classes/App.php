<?php
include_once 'DB.php';

//$array1  =["users.a",'=','b'];
//$array2  =["a",'=','b'];
//$array3  =["a",'=',11];
//$array3  =["users.a",'=',11];
//$table = ['users', 'tasks'];
//
// function parsingCondition($array, array $table = []){
//     $reserved = ['=','>=','<=', '>', '<', '!='];
//    $result = '';
//    $i = 1;
//     if($table == null) {
//         $table = [];
//     }
//    foreach ($array as $key => $value){
//        $valueArray = explode('.',$value);
//        if (in_array($value, $reserved)){
//            $result .= ' '.$value.' ';
//        }else if (is_integer($value) ){
//            $result .= " ".$value." " ;
//        }else if($value == $array[0]){
//            $result .= " ".$value." " ;
//        } else if (in_array($valueArray[0], $table)){
//            $result .= " ".$value." " ;
//        } else{
//            $result .= " '".$value."' " ;
//        }
//    }
//    return $result;
//}
// function parseValue($value, array $table = []){
//    $reserved = ['=','>=','<=', '>', '<', '!='];
//    $valueArray = explode('.',$value);
//    if (in_array($valueArray[0], $table)){
//        return $value;
//    }else if(in_array($valueArray[0], $reserved)){
//        return $value;
//    }else if(is_numeric($valueArray[0])){
//        return $value;
//    }else {
//        return " '".$value."' ";
//    }
//}
//$array1  =["users.a",'=','b'];
//$array2  =["a",'=','b'];
//$array3  =["a",'=',11];
//$array4  =["users.a",'=',11];
//$array5  =["users.a",'=','tasks.a'];
$table = ['users', 'tasks'];
//echo "For the delete <br>";
//echo "Array 1<br>";
//echo convertDeleteArrayToString($array1);
//echo "<br>";
//echo "Array 2<br>";
//echo convertDeleteArrayToString($array2);
//echo "<br>";
//echo "Array 3<br>";
//echo convertDeleteArrayToString($array3);
//echo "<br>";
//echo "Array 4<br>";
//echo convertDeleteArrayToString($array4);
//echo "<br>";
//echo "Array 5<br>";
//echo convertDeleteArrayToString($array5);
//echo "<br>";
//echo "For the select <br>";
//echo "Array 1<br>";
//echo convertDeleteArrayToString($array1, $table);
//echo "<br>";
//echo "Array 2<br>";
//echo convertDeleteArrayToString($array2, $table);
//echo "<br>";
//echo "Array 3<br>";
//echo convertDeleteArrayToString($array3, $table);
//echo "<br>";
//echo "Array 4<br>";
//echo convertDeleteArrayToString($array4, $table);
//echo "<br>";
//echo "Array 5<br>";
//echo convertDeleteArrayToString($array5, $table);
//echo "<br>";
function parsingCondition($arrays, array $table = []){
    $reserved = ['=','>=','<=', '>', '<', '!='];
    $result = '';
    $i = 1;
    if($table == null) {
        $table = [];
    }
    foreach ($arrays as $key =>  $array){
        if(is_string($key)){
            $result .= " {$key} ";
        }
        foreach ($array as $value){
            $valueArray = explode('.',$value);
            if (in_array($value, $reserved)){
                $result .= ' '.$value.' ';
            }else if (is_integer($value) ){
                $result .= " ".$value." " ;
            }else if($value == $array[0]){
                $result .= " ".$value." " ;
            } else if (in_array($valueArray[0], $table)){
                $result .= " ".$value." " ;
            } else{
                $result .= " '".$value."' " ;
            }
        }
    }

    return $result;
}
$array1  =[["users.a",'=','b'],"AND" => ["a",'=','b'],"OR" => ["a",'=',11],"XOR"=>["users.a",'=',11],"ON" => ["users.a",'=','tasks.a']];
echo parsingCondition($array1, $table);