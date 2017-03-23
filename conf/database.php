<?php

class Database {

    private static $objInstance;
    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $dbname = 'detention_time_calc';
    private $dbh;
    private $error;
    # @object, PDO statement object
    private $sQuery;

    # @array,  The database settings
    private $settings;

    # @object, Object for logging exceptions	
    private $log;

    # @array, The parameters of the SQL query
    private $parameters;

    public function __construct() {

        $this->log = new Log();
        $this->parameters = array();
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        $options = array(
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        //Instantiate PDO 
        try {

            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
            if (empty($this->dbh)) {
                throw new Exception('Database connectivity problem.Please refresh your browsers or contact to site management team');
            }

            //var_dump($this->dbh);
        } catch (PDOExcption $e) {
            # Write into log and display Exception
            $this->ExceptionLog($e->getMessage());
            echo $e->getMessage();
            die();
        }
    }

    /*
     * 
     *
     *
     */

    public function Init($query, $parameters = "") {

        try {
            $this->sQuery = $this->dbh->prepare($query);
            # Add parameters to the parameter array	
            $this->bindMore($parameters);

            # Bind parameters
            if (!empty($this->parameters)) {
                foreach ($this->parameters as $param) {
                    $parameters = explode("\x7F", $param);
                    $this->sQuery->bindParam($parameters[0], $parameters[1]);
                }
            }

            # Execute SQL 
            $this->succes = $this->sQuery->execute();
        } catch (PDOException $e) {
            # Write into log and display Exception
            echo $this->ExceptionLog($e->getMessage(), $query);
            die();
        }

        # Reset the parameters
        $this->parameters = array();
    }

    /**
     * 	@void 
     *
     * 	Add the parameter to the parameter array
     * 	@param string $para  
     * 	@param string $value 
     */
    public function bind($para, $value) {
        $this->parameters[sizeof($this->parameters)] = ":" . $para . "\x7F" . utf8_encode($value);
    }

    /**
     * 	@void
     * 	
     * 	Add more parameters to the parameter array
     * 	@param array $parray
     */
    public function bindMore($parray) {
        if (empty($this->parameters) && is_array($parray)) {
            $columns = array_keys($parray);
            foreach ($columns as $i => &$column) {
                $this->bind($column, $parray[$column]);
            }
        }
    }

    /**
     *   	If the SQL query  contains a SELECT or SHOW statement it returns an array containing all of the result set row
     * 	If the SQL statement is a DELETE, INSERT, or UPDATE statement it returns the number of affected rows
     *
     *   	@param  string $query
     * 	@param  array  $params
     * 	@param  int    $fetchmode
     * 	@return mixed
     */
    public function query($query, $params = null, $fetchmode = PDO::FETCH_ASSOC) {
        $query = trim($query);

        $this->Init($query, $params);

        $rawStatement = explode(" ", $query);

        # Which SQL statement is used 
        $statement = strtolower($rawStatement[0]);

        if ($statement === 'select' || $statement === 'show') {
            return $this->sQuery->fetchAll($fetchmode);
        } elseif ($statement === 'insert' || $statement === 'update' || $statement === 'delete') {
            return $this->sQuery->rowCount();
        } else {
            return NULL;
        }
    }

    /**
     *  Returns the last inserted id.
     *  @return string
     */
    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }

    /**
     * 	Returns an array which represents a column from the result set 
     *
     * 	@param  string $query
     * 	@param  array  $params
     * 	@return array
     */
    public function column($query, $params = null) {
        $this->Init($query, $params);
        $Columns = $this->sQuery->fetchAll(PDO::FETCH_NUM);

        $column = null;

        foreach ($Columns as $cells) {
            $column[] = $cells[0];
        }

        return $column;
    }

    /**
     * 	Returns an array which represents a row from the result set 
     *
     * 	@param  string $query
     * 	@param  array  $params
     *   	@param  int    $fetchmode
     * 	@return array
     */
    public function row($query, $params = null, $fetchmode = PDO::FETCH_ASSOC) {
        $this->Init($query, $params);
        return $this->sQuery->fetch($fetchmode);
    }

    /**
     * 	Returns the value of one single field/column
     *
     * 	@param  string $query
     * 	@param  array  $params
     * 	@return string
     */
    public function single($query, $params = null) {
        $this->Init($query, $params);
        return $this->sQuery->fetchColumn();
    }

    /** 	
     * Writes the log and returns the exception
     *
     * @param  string $message
     * @param  string $sql
     * @return string
     */

    /** 	
     * Writes the log and returns the exception
     *
     * @param  string $message
     * @param  string $sql
     * @return string
     */
    private function ExceptionLog($message, $sql = "") {
        $exception = 'Unhandled Exception. <br />';
        $exception .= $message;
        $exception .= "<br /> You can find the error back in the log.";

        if (!empty($sql)) {
            # Add the Raw SQL to the Log
            $message .= "\r\nRaw SQL : " . $sql;
        }
        # Write into log
        $this->log->write($message);

        return $exception;
    }

}
?>


