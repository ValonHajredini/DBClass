<?php

/**
 * Created by PhpStorm.
 * User: PC
 * Date: 04-Oct-16
 * Time: 3:53 PM
 */
class DB
{
    private $HOST        = "localhost";
    private $USER        = 'root';
    private $PASSWORD    = '';
    protected $DATABASE    = 'mydb';
    private static $_dbIns   = null;
    private $_pdo;
    private $_query;
    private $_inserted_id;
    private $_results;
    private $_count;
    private function __construct(){
        try {
            $this->_pdo = new PDO('mysql:host=' . $this->HOST . ';dbname=' . $this->DATABASE . '', $this->USER, $this->PASSWORD);
        } catch (PDOException $e){
            throw new Exception('something is wrong with database connection');
            die($e->getMessage());

        }
    }
    public static function getIns(){
        if (!isset(self::$_dbIns)){
            self::$_dbIns = new DB();
        }
        return self::$_dbIns;
    }
    //    =======================================================================
    public function query($sql, $params = []){
        try {
            $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->_query = $this->_pdo->prepare($sql);
            $x = 1;
            if(count($params)){
                foreach ($params as $param){
                    $this->_query->bindValue($x, $param);
                    $x++;
                }
            }
            try {
                $this->_pdo->beginTransaction();
                if ($this->_query->execute()){
                    $this->_results = $this->_query;
                    if ($this->_pdo->commit()){
                        $this->_inserted_id = $this->_pdo->lastInsertId();
                    }

                }
            } catch(PDOExecption $e) {
                throw new Exception('Error on COMMIT');
                $this->_pdo->rollback();

            }catch(PDOExecption $e) {
                $this->_pdo->rollback();
                throw new Exception('Error in ROLLBACK');
                die($e->getMessage());
            }


        } catch( PDOExecption $e ) {

            throw new Exception('Errorwith PREPARING the query');
            die($e->getMessage());
        }
        return true;
    }
//    =======================================================================

//-------------------------CREATE----------------------- OK exept returning the id
    public function create($table, array $fields){
        $sql = "INSERT INTO `{$table}` (";
        $fieldKeys = array_keys($fields);
        $k = 1;
        foreach ($fieldKeys as $key){
            $sql .= $key;
            if($k < count($fieldKeys)){
                $sql .= ', ';
            }else if($k == count($fieldKeys)){
                $sql .= ') ';
            }
            $k++;
        }
        $sql .= " VALUES( ";
        $v = 1;
        foreach ($fields as $key => $value){
                $sql .=  '?' ;

            if ($v < count($fields)){
                $sql .= ', ';
            }
            $v++;
        }
        $sql .= ')';
//        $fields = implode(', ', $fields);
        echo $sql;
//        print_r($fields);
        try{
            $this->query($sql, $fields );
        } catch (PDOException $e){
            throw new Exception('The create query is not ok[ '.$sql.' ]');
            die($e->getMessage());
        }
//        print_r($this->results());
//        echo  $this->lastInsertedRow();
    }
    //---------------------SELECT---------------------------
    public function select(array $fields = null,array $tables,array $conditiones = null, array $limit = null,array $groups = null,array $havings = null,array $order = null){
        if(!isset($fields)){
            $fields = ['*'];
        }

        if ($order != null){
            $order = $this->orderBy($order);
        }else {
            $order = "";
        }
        if ($limit != null){
            $limit = "LIMIT ". implode(', ',$limit);
        }else {
            $limit = " ";
        }
        if ($groups != null){
            $groups = $this->groupBy($groups);
        }else {
            $groups = "";
        }
        if ($conditiones != null){
            $conditiones = $this->parsingCondition($conditiones, $tables);
        }
        else {
            $conditiones  = '';
        }
        $fields = implode(', ', $fields);
        $sql    = "SELECT {$fields} ";
        $tables = $this->selectedTables($tables);
        $sql .= " ".$tables." ";
        $sql   .= $conditiones.' ';
        $sql .= " ".$groups." ";
        $sql .= " ".$order." ";
        $sql .= ' '.$limit.' ';
        echo "{$sql}";
        try{
            $this->query($sql);
//            return $this->results();
        }catch (PDOException $e){
            throw new Exception('something is wrong with SELECT QUERY');
            die($e->getMessage());

        }
    }
    private function groupBy(array $groupes){
        $return = " GROUP BY ";
        $i = 1;
        foreach($groupes as $group){
            $return .= " ".$group." ";
            if ($i< count($groupes)){
                $return .= ", ";
            }
            $i++;
        }
        return $return;
    }
    private function selectedTables(array $tables){
        if (!empty($tables)){
            $reserved = ['ON', 'INNER JOIN', 'JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'FROM'];
            $result = " ";
            $keysOfTables = array_keys($tables);
            if (array_key_exists('FROM', $tables) or array_key_exists('from', $tables)){
                $result .= "";
                }else {
                $result .= " FROM";

            }
            $arr = null;
            $i = 1;
            foreach ($tables as $key => $value){
                $key = strtoupper($key);
                if(in_array($key, $reserved )){
                  $arr = array_search($key, $reserved);
                    $result .= " {$reserved[$arr]} {$value} ";
                } else {
                    $result .= " {$value} ";
                }
            }
            return $result;


        }
    }
    private function orderBy(array $order){
        $result = "ORDER BY ";
        $i = 1;
        foreach ( $order as $key => $value){
            $result .= ' '.$key.' '. $value.' ';
            if ($i < count($order)){
                $result .= ', ';
            }
            $i++;
        }
        return $result;
    }
    private function countSelected(array $count){
        $return     = ", COUNT( ";
        $return     .= " {$count['BY']} ) ";
        $return     .= "AS  {$count["AS"]}";
        return $return;
    }
    //---------------------UPDATE---------------------------
    public function update(array $tables, array $values, array $conditiones){

    }
    //---------------------DELETE---------------------------OK all
    public function delete($table, array $conditions){
        $cond = '';
        $cond .= parsingCondition($conditions);

        echo  $sql = "DELETE FROM `{$table}` WHERE ". $cond;
        try{
            $this->query($sql);
        } catch (PDOException $e){
            throw new Exception('The DELETE query is not ok [ '.$sql.' ]');
            die($e->getMessage());
        }

    }
    public function lastInsertedRow(){
        return $this->_inserted_id;
    }
    public function results(){
        return $this->_results;
    }
    public function count(){
        return $this->_count;
    }
    //------------------------------------------------
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
}
