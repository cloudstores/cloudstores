<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<body>
<?php

require 'vendor/autoload.php';

class cassandra_wrapper{
	private $cluster;
	private $keyspace;
	private $session;
	private $log;
	
	public function __construct() {
		$this->log = new Katzgrau\KLogger\Logger(__DIR__.'/logs');
		$this->log->info("Connecting to db..");
		
		$this->cluster  = Cassandra::cluster()->build();
		$this->keyspace  = 'configdbkeyspace';
		$this->session  = $this->cluster->connect($this->keyspace);
	} 
	
	public function buildQuery_insert($table, $columns, $values){
		$query = "INSERT INTO {$table}"."{$columns} VALUES {$values}";
		$this->log->debug("query:{$query}");
		return $query;
	}
	
	public function buildQuery_update($operation, $table, $update, $condition, $keylist=NULL){
		switch ($operation){
			case 'UPDATE_ROW':
				$query = "UPDATE {$table}  SET {$update}  WHERE {$condition}";
				return $query;
			case 'UPDATE_COL':
				$query = "UPDATE {$table} SET {$update} WHERE {$condition} IN {$keylist}";
				return  $query;
		}
		
	}
	
	public function buildQuery_select($table, $columns='*', $condition=NULL){
		$query = "SELECT {$columns} FROM {$table}";
		if($condition){
			$query = "SELECT {$columns} FROM {$table} WHERE {$condition} ALLOW FILTERING";
		}
		$this->log->info("{$query}");
		return $query;
	}
	
	public function execute($query){
		try{
			$statement = new Cassandra\SimpleStatement($query);
			$future    = $this->session->executeAsync($statement);
			$result    = $future->get();
			return $result;
		}catch (Exception $e){
			printf("Exception at query execution:%s \n Plz rectify the query and perform the operation again",$e);	
		}
		
	}
}

 $obj = new cassandra_wrapper();
 
 $table = "credentials";
 /*$columns = "( userid, email , firstname , gender , lastname , phone , salt )";
 $values = "( 14c532ac-f5ae-479a-9d0a-36604732e013 , 'suhas@gmail.com', 'suhas',
 'male', 'heeraskar', '95810190114','abcd')";
 $query=$obj->buildQuery_insert($table, $columns, $values);
 
 //$query = "INSERT INTO {$table}"."{$columns} VALUES {$values}";
 //$query = "SELECT * FROM credentials";
 $update = " email='raju@gmail.com', phone='7581918923' ";
 //$condition = " userid=14c532ac-f5ae-479a-9d0a-36604732e010 ";// AND $condition;
 $condition = "userid";
 $keylist = "(14c532ac-f5ae-479a-9d0a-36604732e011,
 14c532ac-f5ae-479a-9d0a-36604732e012,
 14c532ac-f5ae-479a-9d0a-36604732e013) ";
 //$query = $obj->buildQuery_insert($table, $columns, $values);
 $query = $obj->buildQuery_update('UPDATE_COL', $table, $update, $condition, $keylist);
 echo $query; */
 
?>
</body>
</html>