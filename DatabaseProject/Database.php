<?php

/**
 * Created by PhpStorm.
 * User: hajre
 * Date: 9/28/2016
 * Time: 1:30 PM
 */
class Database{
//    This variable is used for instantiating the class DB and
//     creating a connection on the database
    private $HOST        = "localhost";
    private $USER        = 'root';
    private $PASSWORD    = '';
    private $DATABASE    = 'firstproject';
    private static $_instance   = null;

    private  $_pdo ;
        //      _query contains the formatted query
    private    $_query;
        //       _error determines when is an error, when yes then make _errors value = true
    private    $_error     = false;
        //        _result returns the result value of the query
    private    $_results;
        //        _count counts when is any result or not
    private   $_count     = 0;
    //      This private constructor creates the PDO object when the Class DB is called
    private function __construct(){
        try{
            $this->_pdo = new PDO('mysql:host=' . $this->HOST . ';dbname=' . $this->DATABASE . '', $this->USER, $this->PASSWORD);
        }catch (PDOException $e){
            throw new Exception('something is wrong with database connection');
            die($e->getMessage());
        }
    }
    //    Returns the instance of DB class
    public static function Inst(){
            if (!isset(self::$_instance)){
                self::$_instance = new Database();
            }
            return self::$_instance;
    }
    //    Executing the query and returning the attributes as an object
    public function query($sql, $params = []){
        $this->_error = false;
        if ($this->_query = $this->_pdo->prepare($sql)){
            $x = 1;
            if(count($params)){
                foreach ($params as $param){
                    $this->_query->bindValue($x, $param);
                    $x++;
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
    //    Returning the error
    public function error(){
        return $this->_error;
    }
    //    This method helps exicutind a query depens on the $action ['inset', 'update', 'delete', 'select']
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
    //    Using this method get(), returns records from database using Action() method,
    //      but is just for returning records $table = 'users', $where = ['username', '=', 'userns_username']
    public function get($table, $where){
        return $this->action('SELECT * ', $table, $where);
    }
    //    This method is for deleting a specific record $where = ['id', '=','100']
    public function delete($table, $where){
        return $this->action('DELETE ', $table, $where);
    }
    //    This method insert() is for inserting data on a specific table "$table" $fields = ['name' => 'First name', 'last_name' => 'last Name']
    public function insert($table, $fields = []){
       $keys = array_keys($fields);
        $values = '';
        $x = 1;
        foreach ($fields as $field){
            $values .= '?';
            if( $x < count($fields)){
                $values .= ', ';
            }
            $x++;
        }
        $sql = "INSERT INTO {$table} (`". implode('`, `', $keys) ."`) VALUES($values)";
        if(!$this->query($sql, $fields)->error()){
            return true;
        }
        return false;
    }
    //    This method update() is for updating a record on a specific table "$table" and specify $id $fields = ['name' => 'First name', 'last_name' => 'last Name']
    public function update($table, $id, $fields = []){
        $set = '';
        $x = 1;
        foreach($fields as $name => $value){
            $set .= "`{$name}` = ?";
            if ($x < count($fields)){
                $set .= ', ';
            }
            $x++;
        }

        $sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";
        if(!$this->query($sql, $fields)->error()){

            return true;
        }
        return false;
    }
    //        Returns the private variable results
    public function results(){
        return $this->_results;
    }
    //    Selects all rows form a specific table $table = users => SELECT * FROM 'users'
    public function showAll($table){
        return $this->query("SELECT * FROM {$table}");
    }
    //    Returns the first record form the result method [0] first index
    public function first(){
            return $this->results()[0];
    }
    //    Returns the second record form the result method [1] second index
    public function second(){

            return $this->results()[1];

    }
    //    Returns the third record form the result method [2] third index
    public function third(){
            return $this->results()[2];
    }
    //    Returns the forth record form the result method [3] forth index
    public function forth(){
        return $this->results()[3];
    }
    //    Returns the fifth record form the result method [4] fifth index
    public function fifth(){
        return $this->results()[4];

    }
    //    Returns the _count private variable
    public function count(){
        return $this->_count;
    }
}