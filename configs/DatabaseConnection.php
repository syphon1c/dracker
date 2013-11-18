<?php
/**
* Description of DatabaseConnection.php
* version: 1.0
* package: Dracker - Track and Trace
* copyright: Copyright (C) 2013 Gareth Phillips. All rights reserved.
* license: GNU/GPL, see license.htm.
*
* This file is part of the Dracker project.
*
* Dracker is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, under version 3 of the License.
*
* Dracker is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Dracker. If not, see <http://www.gnu.org/licenses/>.
*
* @author GPhillips
**/

Class DatabaseConnection {

  const DB_HOST = '127.0.0.1';
  const DB_USERNAME = 'root';
  const DB_PASSWORD = '';
  const DB_NAME = 'dracker';
  
  private $conn;
  private $database;
  private $query;

  function DatabaseConnection(){
    $this->conn = mysql_connect(self::DB_HOST, self::DB_USERNAME, self::DB_PASSWORD) or die("No connection! Please check your settings");
    $this->database = mysql_select_db(self::DB_NAME) or die("No database!");  
  }
  
  public function getConnection(){
    return $this->conn;
  }
  
  public function getDatabase(){
    return $this->database;
  }
  
  public function executeQuery($query){
    $this->query = $query;
    return mysql_query($this->query);
  }
  
  public function closeConnection(){
    mysql_close($this->conn);
  }
  
  /**
   * Get array with keys 'username' by session id from 'loggedin' table
   * 
   * @param type $sessionid
   * @return type
   */
  public function getRole($sessionid){
    return mysql_fetch_array($this->executeQuery("SELECT username FROM loggedin WHERE session = '".$sessionid."'"));
  }
  
}
?>

