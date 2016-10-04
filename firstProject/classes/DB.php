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
    protected $DATABASE    = 'firstproject';
    private static $_dbIns   = null;
    private $_pdo;
    private $_query;
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
        $this->_error = false;
        if ($this->_query = $this->_pdo->prepare($sql)){
            if ($this->_query->execute()){
                $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
                $this->_count = $this->_query->rowCount();
            } else {
                $this->_error = true;
            }
        }
        return $this;
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

//-------------------------CREATE-----------------------
    public function create($table, $fields = []){

    }
    //---------------------SELECT---------------------------
    public function select(array $fields = null,array $tables,array $conditiones,array $limit = null,array $groups = null,array $havings = null,array $order = null ){

    }
    //---------------------UPDATE---------------------------
    public function update(array $tables, array $values, array $conditiones){

    }
    //---------------------DELETE---------------------------
    public function delete($table, array $conditions){
        $cond = '';
        $cind_keys = array_keys($conditions);
        $i = 1;
        foreach ($cind_keys as $key){

            $cond .= $this->convertArrayToString($conditions[$key]);
            if($i < count($conditions)){
                $cond .= ' AND ';
            }
            $i++;
        }
        echo $sql = "DELETE FROM `{$table}` WHERE ". $cond;

    }
    private function convertArrayToString($array){
        $reserved = ['=','>=','<='];
        $result = '';

        foreach ($array as $key => $value){
            $result .= ' '.$value.' ';
        }

        return $result;
    }
    //------------------------------------------------
}
