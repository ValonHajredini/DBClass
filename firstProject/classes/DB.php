<?php

/**
 * Created by PhpStorm.
 * User: PC
 * Date: 04-Oct-16
 * Time: 3:53 PM
 */
class DB
{
//    ==============================[ CORE VARIABLES ]==================================

    private $HOST        = "localhost";
    private $USER        = 'root';
    private $PASSWORD    = '';
    protected $DATABASE    = 'testdb';
    private static $_dbIns   = null;
    private $_pdo;
    private $_query;
    private $_inserted_id;
    private $_results;
    private $_count;
    private $_affected_row;


    //==============================[ CORE METHODS ]====================================
    public function __construct(){
        try {
            if(!isset($this->_pdo )){
                $this->_pdo = new PDO('mysql:host=' . $this->HOST . ';dbname=' . $this->DATABASE . '', $this->USER, $this->PASSWORD);
            }
        } catch (PDOException $e){
            throw new Exception('something is wrong with database connection');
            die($e->getMessage());

        }
    }
    public static function getIns(){
        if (!isset(self::$_dbIns)){
            echo "IS instanciated";
            self::$_dbIns = new DB();
        }
        return self::$_dbIns;
    }
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
                    $this->_affected_row = $this->_query->rowCount();
                    $this->_inserted_id = $this->_pdo->lastInsertId();;
                    $this->_results = $this->_query;
                    if ($this->_pdo->commit()){
//                        $this->_inserted_id = $this->_pdo->
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


    //==============================[ MAIN METHODS ]====================================
    //-------------------------------CREATE----------------------- OK exept returning the id
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
        try{
            $this->query($sql, $fields );
            echo "From the insert query: [<b>{$sql}</b>] The id: <b>{$this->lastInsertedRow()}</b>  of inserted row ";
            return  $this->lastInsertedRow();
        } catch (PDOException $e){
            throw new Exception('The create query is not ok[ '.$sql.' ]');
            die($e->getMessage());
        }
//        print_r($this->results());
//        echo  $this->lastInsertedRow();
    }
    //-------------------------------SELECT---------------------------
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
            $cond = $conditiones;
            $ptbl = $tables;
            $conditiones = $this->parsingCondition('select', $conditiones, $tables)[0];
        }else {
            $conditiones  = '';
        }
        $fields = implode(', ', $fields);
        $sql    = "SELECT {$fields} ";
        $sql .= "  FROM ".$this->parsingCondition('select', $cond, $ptbl)[1]." ";
        $sql   .= $conditiones.' ';
        $sql .= " ".$groups." ";
        $sql .= " ".$order." ";
        $sql .= ' '.$limit.' ';

//        echo $this->parsingCondition('select', $cond, $ptbl)[1];
        try{
            $this->query($sql);
            echo "From the query: [<b> {$sql} </b>] <b>{$this->affectedRows()}</b> rows are selected";
            return $this->results();
        }catch (PDOException $e){
            throw new Exception('something is wrong with SELECT QUERY');
            die($e->getMessage());
        }
    }
    //---------------------UPDATE---------------------------
    public function update(array $tables, array $values, array $conditiones){
        $sql = "UPDATE ";
        $t = 1;
        foreach ($tables as $table){
            $sql .= " `".$table."` ";
            if($t < count($tables)){
                $sql .= ", ";
            }
            $t ++;
        }
        $sql .= ' SET';
        $i = 1;
        foreach ($values as  $key => $value){
            $exValues = explode('.', $value);
            if (is_integer($value)){
                $sql .= " ".$key." = ". $value." ";
            }else if (in_array($exValues[0], $tables)){
                $sql .= " ".$key." = ". $value." ";
            }else {
                $sql .= " ".$key." = '". $value."' ";
            }
            if ($i < count($values)){
                $sql .= ', ';
            }
            $i++;
        }
        $conditiones = $this->parsingCondition('update',$conditiones, $tables);
        $sql .= $conditiones.' ';
        try{
            $this->query($sql);
            echo"From the query[<b>{$sql}</b>] are <b>".$this->affectedRows().'</b> Affected Rows  ';
            return $this->affectedRows();
        } catch (PDOException $e){
            throw new Exception('The UPDATE query is not ok [ '.$sql.' ]');
            die($e->getMessage());
        }
    }
    //---------------------DELETE---------------------------OK all
    public function delete($table, array $conditions){
        $cond = '';
        $cond .= $this->parsingCondition('delete',$conditions);

        $sql = "DELETE FROM `{$table}` WHERE ". $cond;
        try{
            $this->query($sql);
            echo "<br>From the query: [<b>{$sql}</b>],  <b>".$this->affectedRows().'</b> Where deleted';
            return $this->affectedRows();
        } catch (PDOException $e){
            throw new Exception('The DELETE query is not ok [ '.$sql.' ]');
            die($e->getMessage());
        }

    }
    //------------------------------------------------


    //==============================[ Helpers ]========================================
    private function parsingCondition($action, $arrays, array $table = []){
        $tblJoins = ['ON', 'INNER JOIN', 'JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'FROM'];
        $result = '';
        $tbl = null;
        if (count($table) > 1){
//            $result .= "INNER JOIN";
        }else if (count($table) == 1){
            $result .= "WHERE ";
        }
        $reserved = ['=','>=','<=', '>', '<', '!='];
        if($table == null) {
            $table = ["FROM"];
        }
        foreach ($arrays as $key =>  $array){
//            print_r($array);
            if (in_array($array[0], $tblJoins)){
                $tn = 1;
                $new_table = [];
                $arrayCond = $array[0]. ' (';
                foreach ($table as $tbl){
                    if ($tn == 1) {
                        $new_table[] = $tbl;


                    } else if (count($table) >= 3){
                        $new_table[] = $arrayCond ;
                        $new_table[] = $tbl;
                        if ($tn < count($table)){
                            $arrayCond = ', ';
                        } else {
                            $new_table[] = ' ) ';
                        }
                    }else {
                        $new_table[] = $array[0];
                        $new_table[] = $tbl;
                    }

                    $tn++;
                }
                $tbl = implode(' ', $new_table);
//                $tbl .= $tbl. ' ON';
            }else {
                if(in_array($arrays[0][0], $tblJoins) ){
                    $tbl .= ' ';

                } else {
                    $tbl  = $table[0];
                }
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
        }
        if($action === 'select'){


            if (in_array($arrays[0][0], $tblJoins) &&  count($table) > 1){
                $tbl .= ' ON';
            }


            return [$result, $tbl];

        }else {
            return $result;
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


    //==============================[ Geters ]=========================================
    public function affectedRows(){
        return $this->_affected_row;
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
    //================================================================================
}