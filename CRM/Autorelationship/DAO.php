<?php

/* 
 * This class extends the core DAO so that there is a function to retrieve the last insert ID
 */

class CRM_Autorelationship_DAO extends CRM_Core_DAO {
  
  public function getInsterId() {
    global $_DB_DATAOBJECT;
    $options = &$_DB_DATAOBJECT['CONFIG'];
    
    $insertId = false;
    
    $connection = $this->getDatabaseConnection();
    if (!$connection) {
      return false;
    }
    $dbtype    = $connection->dsn["phptype"];

    switch ($dbtype) {
      case 'mysql':
      case 'mysqli':
        $method = "{$dbtype}_insert_id";
        
        $insertId = $method(
            $connection->connection
        );
        break;

      case 'mssql':
        // note this is not really thread safe - you should wrapp it with
        // transactions = eg.
        // $db->query('BEGIN');
        // $db->insert();
        // $db->query('COMMIT');
        $db_driver = empty($options['db_driver']) ? 'DB' : $options['db_driver'];
        $method = ($db_driver == 'DB') ? 'getOne' : 'queryOne';
        $mssql_key = $connection->$method("SELECT @@IDENTITY");
        if (PEAR::isError($mssql_key)) {
          $this->raiseError($mssql_key);
          return false;
        }
        $insertId = $mssql_key;
        break;

      case 'pgsql':
        if (!$seq) {
          $seq = $connection->getSequenceName(strtolower($this->__table));
        }
        $db_driver = empty($options['db_driver']) ? 'DB' : $options['db_driver'];
        $method = ($db_driver == 'DB') ? 'getOne' : 'queryOne';
        $pgsql_key = $connection->$method("SELECT currval('" . $seq . "')");


        if (PEAR::isError($pgsql_key)) {
          $this->raiseError($pgsql_key);
          return false;
        }
        $insertId = $pgsql_key;
        break;

      case 'ifx':
        $insertId = array_shift(
            ifx_fetch_row(
                ifx_query(
                    "select DBINFO('sqlca.sqlerrd1') FROM systables where tabid=1", $connection->connection, IFX_SCROLL
                ), "FIRST"
            )
        );
        break;
    }
    return $insertId;
  }
  
}

