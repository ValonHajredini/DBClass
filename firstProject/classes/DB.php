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

                } else {
                    $this->_query->execute();
                }
                $this->_pdo->commit();
                $this->_inserted_id = $this->_pdo->lastInsertId();
                $this->_inserted_id = $this->_inserted_id;
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
    public function action($action, $table, $where = []){
        if (count($where) === 3){
            $operators = ['=', '>', '<', '>=', '<='];
            $field      = $where[0];
            $operator   = $where[1];
            $value      = $where[2];

            if(in_array($operator, $operators)){
                $sql = "{$action} FROM {$table} WHERE {$field}  {$operator}?";
                if(!$this->query($sql, [$value])->error()){
                    return $this;
                }
            }
        }
        return false;
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

        try{
            $this->query($sql, $fields );
        } catch (PDOException $e){
            throw new Exception('The create query is not ok[ '.$sql.' ]');
            die($e->getMessage());
        }
        print_r($this->results());
    }
    //---------------------SELECT---------------------------
    //  SELECT users.name, users.lastname, tasks.name from users u  join tasks t on u.id t.user_id
    public function select(array $fields = null,array $tables,array $conditiones,array $limit = null,array $groups = null,array $havings = null,array $order = null ){
        if(!isset($fields)){
            $fields = ['*'];
        }
        $fields = implode(', ', $fields);
        $sql    = "SELECT {$fields} ";
        $sql   .= "FROM ";
        $tables = implode(', ', $tables);
        $sql   .= $tables.' ';
        $sql   .= "WHERE ";
        $conditiones = $this->prepareConditionArray($conditiones);
        $sql   .= $conditiones;
        echo "{$sql}<br>";
        if(isset($fields)){
            echo "Fields<br>";
        }
        if(isset($tables)){
            echo "Tables<br>";
        }
        if(isset($conditiones)){
            echo "Condition<br>";
        }
        if(isset($limit)){
            echo "Limit<br>";
        }
        if(isset($groups)){
            echo "Groups<br>";
        }
        if(isset($havings)){
            echo "Havings<br>";
        }
         if(isset($order)){
            echo "Orders<br>";
        }

    }
    private function prepareConditionArray(array $array){
        $reserved = ['=','>=','<=', '>', '<'];
        return "The array";
    }
    //---------------------UPDATE---------------------------
    public function update(array $tables, array $values, array $conditiones){

    }
    //---------------------DELETE---------------------------OK all
    public function delete($table, array $conditions){
        $cond = '';
        $cond_keys = array_keys($conditions);
        $i = 1;
        foreach ($cond_keys as $key){
            if(is_array($conditions[$key])) {
                $cond .= $this->convertDeleteArrayToString($conditions[$key]);
                if ($i < count($conditions)) {
                    $cond .= ' AND ';
                }
            }else {
                $cond .= $conditions[$key];
            }
            $i++;
        }
        echo  $sql = "DELETE FROM `{$table}` WHERE ". $cond;
        try{
            $this->query($sql);
        } catch (PDOException $e){
            throw new Exception('The DELETE query is not ok [ '.$sql.' ]');
            die($e->getMessage());
        }

    }
    private function convertDeleteArrayToString($array){
        $reserved = ['=','>=','<=', '>', '<'];
        $result = '';
        $i = 1;
        foreach ($array as $key => $value){
            if (in_array($value, $reserved)){
                $result .= ' '.$value.' ';
            }else if (is_integer($value) ){
            $result .= " ".$value." " ;
            }else if($value == $array[0]){
                $result .= " ".$value." " ;
            }else {
                $result .= " '".$value."' " ;
            }
        }

        return $result;
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
//    public function lastInsertedRow
    //------------------------------------------------
}
