<?php
class Database
{
    private $hostname = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname;
    private $dblink; // veza sa bazom
    private $result; // Holds the MySQL query result
    private $records; // Holds the total number of records returned
    private $affected; // Holds the total number of affected rows
    function __construct($dbname)
    {
        $this->dbname = $dbname;
        $this->Connect();
    }
    /*
function __destruct()
{
$this->dblink->close();
//echo "Konekcija prekinuta";
}
*/
    public function getResult()
    {
        return $this->result;
    }
    //konekcija sa bazom
    function Connect()
    {
        $this->dblink = new mysqli($this->hostname, $this->username, $this->password, $this->dbname);
        if ($this->dblink->connect_errno) {
            printf("Konekcija neuspešna: %s\n", $this->dblink->connect_error);
            exit();
        }
        $this->dblink->set_charset("utf8");
        //echo "Uspesna konekcija";
    }
    //select funkcija
    function select($table = "itemi", $rows = 'itemi.id, itemi.content, itemi.status, itemi.list_id, liste.title', $join_table = "liste", $join_key1 = "list_id", $join_key2 = "id", $where = null, $order = null)
    {
        $q = 'SELECT ' . $rows . ' FROM ' . $table;
        if ($join_table != null)
            $q .= ' JOIN ' . $join_table . ' ON ' . $table . '.' . $join_key1 . ' = ' . $join_table . '.' . $join_key2;
        if ($where != null)
            $q .= ' WHERE ' . $where;
        if ($order != null)
            $q .= ' ORDER BY ' . $order;
        $this->ExecuteQuery($q);
        //print_r($this->getResult()->fetch_object());
    }

    function insert($table = "itemi", $rows = "content, status", $values)
    {
        $query_values = implode(',', $values);
        $insert = 'INSERT INTO ' . $table;
        if ($rows != null) {
            $insert .= ' (' . $rows . ')';
        }
        $insert .= ' VALUES (' . $query_values . ')';
       // echo $insert;
        if ($this->ExecuteQuery($insert))
            return true;
        else return false;
    }
    function update($table = "itemi", $id, $keys, $values)
    {
        $set_query = array();
        for ($i = 0; $i < sizeof($keys); $i++) {
            $set_query[] = $keys[$i] . " = '" . $values[$i] . "'";
        }
        $set_query_string = implode(',', $set_query);


        $update = "UPDATE " . $table . " SET " . $set_query_string . " WHERE id=" . $id;
        if (($this->ExecuteQuery($update)) && ($this->affected > 0))
            return true;
        else return false;
    }
    function delete($table = "itemi",  $keys, $values)
    {
        $delete = "DELETE FROM " . $table . " WHERE " . $keys[0] . " = '" . $values[0] . "'";
        //echo $delete;
        if ($this->ExecuteQuery($delete))
            return true;
        else return false;
    }

    //funkcija za izvrsavanje upita
    function ExecuteQuery($query)
    {
        if ($this->result = $this->dblink->query($query)) {
            if (isset($this->result->num_rows)) $this->records = $this->result->num_rows;
            if (isset($this->dblink->affected_rows)) $this->affected = $this->dblink->affected_rows;
            // echo "Uspesno izvrsen upit";
            return true;
        } else {
            return false;
        }
    }

    function CleanData()
    {
        //mysql_string_escape () uputiti ih na skriptu vezanu za bezbednost i sigurnost u php aplikacijama!!
    }
}
