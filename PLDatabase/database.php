<?php
class Database {
  public function __construct() {
    die('Init function error');
  }

  public static function dbConnect() {
	  $mysqli = null;
	//try connecting to your database
    require_once('/home/amoumad/DBmoumad.php');
    try {
      $mysqli = new PDO('mysql:host='.DBHOST.';dbname='.DBNAME, USERNAME, PASSWORD);
    }
 
	//catch a potential error, if unable to connect
    catch (PDOException $e)  {
      echo "Error!: ". $e->getMessage()."<br />";
      die ("Could not connect<br />");
    }  
    
      
 
    return $mysqli;
  }

  public static function dbDisconnect() {
    $mysqli = null;
  }
}
?>
