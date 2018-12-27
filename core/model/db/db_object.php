<?php
    /*--
    COMPANY: ezdev (former: the3ds)
    AUTHOR: Tom DINH
    PROJECT: mirana core v2
    DESCRIPTION: model class
    DATE: Oct-2016
    --*/

	class DB_Object {
		private	    $db_type;
		private 	$db_url;
		private 	$db_port;
		private 	$db_name;
		private 	$db_username;
		private 	$db_password;
		private     $listVars = array("db_type","db_url","db_port","db_name","db_username","db_password");

		private   	$db_connection;
		private   	$db_pdo;
		private 	$db_accepedType;

		//constructor
		public function __construct($db_properties = []){
			$this->db_connection = NULL;
			$this->db_pdo = NULL;
			$this->db_accepedType = array(MYSQL, PGSQL);

            if (in_array($db_properties[DB_TYPE],$this->db_accepedType) && is_array($db_properties)){
                foreach ($this->listVars as $prop){
                    if (isset($db_properties[$prop]) && is_string($db_properties[$prop])){
                        $this->$prop = $db_properties[$prop];
                    } else {
                        $errMessage = "Not enough database parameters";
                        throw new Exception("<p>$errMessage</p>", 1);
                    }
                }
            }
		}

		// dbconnect
		public function dbConnect(){
			switch ($this->db_type){
				case PGSQL: return $this->dbPgConnect(); break;
				case MYSQL: return $this->dbMyConnect(); break;
				default: return NULL; break;
			}
		}

		//dbClose
		public function dbClose(){
			switch ($this->db_type){
				case PGSQL: return $this->dbPgClose(); break;
				case MYSQL: return $this->dbMyClose(); break;
				default: return NULL; break;
			}
		}

		//dbPing
		public function dbPing(){
			switch ($this->db_type){
				case PGSQL: return $this->db_connection===NULL?FALSE:pg_ping($this->db_connection); break;
				case MYSQL: return $this->db_connection===NULL?FALSE:mysqli_ping($this->db_connection); break;
				default: return null; break;
			}
		}

		//get current connection
		public function getConnection(){
			return $this->db_connection;
		}

		//get current PDO
		public function getPDO(){
			return $this->db_pdo;
		}

		//get current dbname
		public function getDbName(){
			return $this->db_name;
		}

		//get current DB Type
		public function getDbType(){
			return $this->db_type;
		}

		//check if root db or not
		public function checkRootDb(){
			return $this->is_root;
		}

		//reset current connection
		public function resetConnection(){
			$this->db_connection = NULL;
			$this->db_pdo = NULL;
		}

		###################################################
		#POSTGRESQL
		###################################################

		//connect to database - PostgreSQL
		private function dbPgConnect(){
			foreach ($this as $key_ => $value_){
				if (!is_string($value_) && in_array($key_, $this->listVars)){
					throw new Exception(ALL_STRING);
				}
			}

			$conString = 	"host=$this->db_url
							port=$this->db_port
							dbname=$this->db_name
							user=$this->db_username
							password=$this->db_password";

			if ( !function_exists("exception_error_handler") ){
				function exception_error_handler($errno, $errstr, $errfile, $errline ) {
					throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
				}
			}
			set_error_handler("exception_error_handler");

			try {
				$this->db_connection = pg_connect($conString);
			} catch (Exception $e) {
				die($e->getMessage());
			}

			try {
				$this->db_pdo = new PDO("pgsql:$conString");
			} catch (Exception $e) {
				die($e->getMessage());
			}
			return true;
		}

		//close the current connection
		private function dbPgClose(){
			if ($this->getConnection() != false){
				pg_close($this->getConnection());
				$this->resetConnection();
			} else {
				throw new Exception(NO_AVAI_CON);
			}
		}


		###################################################
		#MySQL
		###################################################

		//connect to database - MySQL
		private function dbMyConnect(){
			foreach ($this as $key_ => $value_){
				if (!is_string($value_) && in_array($key_, $this->listVars)){
					throw new Exception(ALL_STRING);
				}
			}

			$this->db_connection  = mysqli_connect(
										$this->db_url,
										$this->db_username,
										$this->db_password,
										$this->db_name,
										$this->db_port
									);

			// Check connection
			if ( !$this->db_connection ){
				die( mysqli_connect_errno() );
			}

			try {
				$this->db_pdo = new PDO("mysql:host=$this->db_url;port=$this->db_port;dbname=$this->db_name",
										$this->db_username,
										$this->db_password
								);
			} catch (Exception $e) {
				die($e->getMessage());
			}
		}

		//close the current connection
		private function dbMyClose(){
			if ($this->getConnection() != false){
				mysqli_close($this->getConnection());
				$this->resetConnection();
			} else {
				throw new Exception(NO_AVAI_CON);
			}
		}
    }
?>
