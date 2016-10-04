<?php

/**
 * Created by PhpStorm.
 * User: PC
 * Date: 04-Oct-16
 * Time: 9:19 AM
 */

class Database{
    private $HOST        = "localhost";
    private $USER        = 'root';
    private $PASSWORD    = '';
    private $DATABASE    = 'firstproject';
    private static $_dbIns   = null;
    private $_db;
    private $_query;
    private $_count;
    private $_action;
    private $_error;
    private $_results;
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
            self::$_dbIns = new Database();
        }
        return self::$_dbIns;
    }
//    ----------------------------------------------------
    public function create($table, $fields = []){
        $keys = array_keys($fields);
        $values = '';
        $i = 1;
        foreach ($fields as $field){
            $values .= '?';
            if( $i < count($fields)){
                $values .= ', ';
            }
            $i++;
        }
        $sql = "INSERT INTO {$table} (`". implode('`, `', $keys) ."`) VALUES($values)";
        echo $sql;
        if(!$this->query($sql, $fields)->error()){
            return true;
        }
        return false;
    }
    public function delete($table, $where){
        return $this->action('DELETE ', $table, $where);
    }
    //    ----------------------------------------------------
//    ----------------------------------------------------
//    ----------------------------------------------------
//    ----------------------------------------------------
    public function select($fields = [], $tables, $conditiones = [], $attributes =[]){
        $allow_attributes = ['LIMIT', 'GROUP BY', 'HAVING', 'ORDER BY'];

        if (count($attributes)){
            $attr_keys = array_keys($attributes);
            $queryParams = "";
            foreach ($attr_keys as $oper)

                if (in_array($oper, $allow_attributes)){

                if(count($attributes[$oper])){
                    $queryParams .= " {$oper} ";
                    $q = 1;
                    foreach ($attributes[$oper] as $qParams){
                        $queryParams .= "{$qParams}";
                        if ($q < count($attributes[$oper])){

                            $queryParams .= ", ";
                        }
                        $q++;

                    }

                }elseif (count($attributes[$oper]) == 2){
                    echo 'ka dy antar';
                }
//                    print_r($attributes[$oper]);
//                    echo "<br>";
                }

//            print_r($attr_keys);
//            echo "<br>";
        }
        $values = '';
        $i = 1;
        foreach ($fields as $field){
            $values .= $field;
            if( $i < count($fields)){
                $values .= ', ';
            }
            $i++;
        }
        $tbl = "FROM ";
        $t = 1;
        foreach ($tables as $table){
            if ($t < 2 ){
                $tbl .=  $table;
            }else {
                $tbl .= " INNER JOIN ". $table;
            }
            $t++;
        }
        $cond = ' ';
        foreach ($conditiones as $condition){
            $cond .= " $condition ";
        }


        echo "SELECT ".$values."<br> ".$tbl. "<br> ON ". $cond.' '.$queryParams;
        echo '<br>';
        echo "<pre>";
        echo print_r($attributes);
        echo "</pre>";
    }
    private function parsConditiones($conditiones =[]){
        $con = $conditiones;
        foreach ($conditiones as $condition){

        }
    }

//    =======================================================================
    public function query($sql, $params = []){
        $this->_error = false;
        if ($this->_query = $this->_pdo->prepare($sql)){
            $i = 1;
            if(count($params)){
                foreach ($params as $param){
                    $this->_query->bindValue($i, $param);
                    $i++;
                }
            }
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
//    public function get($table, $condition =[] ){
//        return $this->action('SELECT * ', $table, $condition);
//    }

    public function results(){
        return $this->_results;
    }
    public function update($tables = [], $values = [], $condition = []){
        return 'Afected Rows';
    }

    public function parseValues($values= []){

    }

    public function error(){
        return $this->_error;
    }
    public function getResults(){
        return $this->_results;
    }
//    --------------------------------
    public function rollBack(){

    }
    public function commitTransacttion(){

    }

    public function count(){
        return $this->_count;
    }
}