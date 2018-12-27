<?php
	/*-----
	COMPANY: the3ds team
	AUTHOR: Tom DINH
	PROJECT: core DA_Engine
	DESCRIPTION: --
	DATE: 12/May/2015
	Ver 1.2
	-----*/
	abstract class ValueObject implements VO_interface{
		private 	$tableName;
		private		$db;
		private 	$primaryKey = array();
		private		$typeOf = NULL;
		private 	$blacklistVars = array(
						"tableName",
						"db",
						"primaryKey",
						"typeOf",
						"blacklistVars"
					);

		//constructor
		public function __construct($inputDB){			
			if ( get_class($inputDB) === "DB_Object" ){
				$this->db = $inputDB;
			} else {
				throw new Exception("ValueObject constructor: Incorrect database passed", 1);
			}
			//resolve namespace
			$tableName = explode("\\",get_class($this));
			$this->tableName = end($tableName); //this is why class name must be identical with tablename
			// $this->propertiesPreGen($this->tableName);
			return NULL;
		}

		//check if a varName is valid

		//get a specific property given property name
		public function getValue($prop){
			if (property_exists($this, $prop)){
				return $this->$prop;
			} else {
				return NULL;
			}
		}

		//set a specific property given property name and value
		public function setValue($prop, $inputValue){
			try {
				$this->$prop = $inputValue;
				return true;
			} catch (Exception $e){
				return false;
			}
		}

		//parse the whole object to array
		public function toArray(){
			$retArray = array();
			foreach ($this as $key_ => $value_) {
				if ( !in_array($key_, $this->blacklistVars) ) {
					$retArray[$key_] =  $value_;
				}
			}
			return $retArray;
		}

		//parse the whole object to JSON
		public function toJSON(){
			return json_encode($this->toArray());
		}
		################################################################################################

		public function setTypeOf($arrName, $arrType = NULL){
			if ($arrType === NULL){
				if (issset($this->typeOf[$arrName]))
					unset($this->typeOf[$arrName]);
			} else if (strcmp(strtolower(trim($arrType)), "int") === 0){
				$this->typeOf[$arrName] = "int";
			} else if (strcmp(strtolower(trim($arrType)), "text") === 0){
				$this->typeOf[$arrName] = "text";
			} else {
				return false;
			}
			return true;
		}

		private function execBindValues($preparedSql, $arrParams = array()){
			if (is_array($arrParams)){
				$t_sql = $preparedSql;
				$loop_p = $arrParams;
				foreach($loop_p as $u => $v){
					$t = trim($u, ":");
					if (isset($this->typeOf[$t])){
						switch ($this->typeOf[$t]){
							case "int":
								$t_sql->bindParam($u, $loop_p[$u], PDO::PARAM_INT);
								break;
							case "text":
								$t_sql->bindParam($u, $loop_p[$u], PDO::PARAM_STR);
								break;
							default:
								$t_sql->bindParam($u, $loop_p[$u], PDO::PARAM_STR);
								break;
						}
					} else {
						$t_sql->bindParam($u, $loop_p[$u], PDO::PARAM_STR);
					}
				}
				return $t_sql;
			} else {
				return NULL;
			}
		}
		//eliminate value
		private function eliminateField($field){
			if (property_exists($this, $field)){
				unset($this->$field);
			} else {
				;
			}
			return NULL;
		}
		//setPK
		public function setPK(){
			$this->primaryKey = array();
			$arrListParams = func_get_args();
			if ( count($arrListParams) > 0){
				foreach ($arrListParams as $e){
					if (is_string($e)){
						$this->primaryKey[] = $e;
						if (!property_exists($this, $e)) $this->$e = NULL;
					} else {
						throw new Exception(INCORRECT_VALUE);
					}
				}
				return true;
			}
			return false;
		}

		//setPK passing values by arrays
		public function setPKArr($inputArr){
			if ( is_array( $inputArr) && count($inputArr) > 0){
				$this->primaryKey = array();
				foreach ($inputArr as $key_ => $value_){
					if ( is_string($key_) ){
						$this->primaryKey[] = $key_;
						$this->$key_ = $value_;
					} else {
						throw new Exception(INCORRECT_VALUE);
					}
				}
				return true;
			} else {
				throw new Exception(INCORRECT_VALUE);
			}
		}
		################################################
		################################################
		################################################
		//Version 1.6
		public function read($pk){
			$retVal = true;
			//setPK
			$this->primaryKey = array();
			$arrListParams = func_get_args();
			if (count($pk) > 0 && is_array($pk)){
				foreach ($pk as $key_ => $value_){
					if ( is_string($key_) ){
						$this->primaryKey[] = $key_;
						$this->$key_ = $value_;
					} else {
						$retVal = false;
						throw new Exception(INCORRECT_VALUE);//TODO: error message required
					}
				}
				$retVal = $this->retrieve();
			} else if (is_int($pk)){
				$retVal = $this->idRetrieve($pk);
			} else {
				$retVal = false;
			}

			return $retVal;
		}

		public function create(){
			$retVal = NULL;
			if (property_exists($this, "id")){
				$retVal = $this->insertDB_returning();
			} else {
				$retVal = $this->insertDB();
			}
			return $retVal;
		}

		//update existing record on db (update row)
		public function update($data = NULL, $where = NULL){
			$retVal = new Mirana_Protocol();
			$arrVarKeys = array();
			$arrWhereKeys = array();
			$arrParams = NULL;
			########################################################
			#BUILD UPDATED VALUES
			########################################################
			if ($data === NULL){
				foreach ($this as $u => $v) {
					if (!in_array($u, $this->blacklistVars)	&& !in_array($u, $this->primaryKey)
						&& ($where===NULL || !isset($where[$u]) && $v!==NULL))
					{
							$arrVarKeys[] = $u;
							$arrParams[":$u"] = $v;
					} else {
						;
					}
				}
			} else if (is_array($data) && count($data)>0){
				foreach ($data as $u => $v) {
					if (!in_array($u, $this->blacklistVars)){
						if ((!in_array($u, $this->primaryKey) && $where===NULL) || !isset($where[$u])){
							if ($v!==NULL){
								$arrVarKeys[] = $u;
								$arrParams[":$u"] = $v;
							}
						}
					} else {
						$errMessage = "Database field in table $this->tableName is violated constant";
						throw new Exception("<p>$errMessage</p>", 1);
					}
				}
			} else {
				;
			}
			$strVars = implode(",",$arrVarKeys);
			$strValues = ":".implode(",:",$arrVarKeys);

			//MYSQL builder for values
			$mySQL_SET = array();
			foreach ($arrParams as $key_ => $value_){
				$mySQL_SET[] =str_replace(":","",$key_)."=$key_";
			}
			$mySQL_SET = implode($mySQL_SET, ",");
			//actually execute SQL
			########################################################
			#BUILD UPDATE CONDITION
			########################################################
			if ($where === NULL){
				if ( count($this->primaryKey) > 0){
					foreach ($this->primaryKey as $e){
						if ( $this->$e!==NULL ){
							$arrWhereKeys[] = "$e = :$e";
							$arrParams[":$e"] =  $this->$e;
						}
					}
				} else {
					;
				}
			} else if (is_array($where) && count($where)>0){
				foreach ($where as $u=>$v){
					$arrWhereKeys[] = "$u = :$u";
					$arrParams[":$u"] =  $v;
				}
			} else {
				;
			}
			$strWhere = implode( " AND ", $arrWhereKeys );

			######DB SELECT########
			$query = array();
			$query[PGSQL] = "UPDATE \"$this->tableName\" SET ($strVars) = ($strValues) WHERE $strWhere";
			$query[MYSQL] = "UPDATE $this->tableName SET $mySQL_SET WHERE $strWhere";
			#####################
			if (!$this->db->dbPing()) $this->db->dbConnect();
			$sqlQuery = $query[ $this->db->getDbType() ] ;
			$preparedSql = $this->db->getPDO()->prepare($sqlQuery);
			$preparedSql = $this->execBindValues($preparedSql, $arrParams); //add this
			$execStatus = $preparedSql->execute();
			//$execStatus = $preparedSql->execute($arrParams);
			$this->db->dbClose();

			//calculate returned value
			if ($execStatus === true){
				$retVal->setStatus(true);
				$retVal->setCount(1);
				$retVal->setValue(NULL);
			} else {
				$retVal->setStatus(false);
				$retVal->setValue(NULL);
				$retVal->setMessage($preparedSql->errorInfo());
			}
			###
			return $retVal;
		}

		//delete if all properties matched
		//USE WITH CARE
		public function delete($inputArr){
			$retVal = new Mirana_Protocol();
			//again!!!
			if ( is_array($inputArr) && count($inputArr) > 0 ){
				if (!$this->db->dbPing()) $this->db->dbConnect();
				###
				$arrWhereKeys = array();
				$arrParams = NULL;
				##################################################
				foreach ($inputArr as $key_ => $value_ ){
					if ( $value_!==NULL ){
						$arrWhereKeys[] = "$key_ = :$key_";
						$arrParams[":$key_"] =  $value_;
					}
				}
				$strWhere = implode( " AND ", $arrWhereKeys );
				######DB SELECT########
				$query = array();
				$query[PGSQL] = "DELETE FROM \"$this->tableName\" WHERE $strWhere";
				$query[MYSQL] = "DELETE FROM $this->tableName WHERE $strWhere";
				#####################
				$sqlQuery = $query[ $this->db->getDbType() ] ;

				$preparedSql = $this->db->getPDO()->prepare($sqlQuery);
				$preparedSql = $this->execBindValues($preparedSql, $arrParams); //add this
				$execStatus = $preparedSql->execute();
				//$execStatus = $preparedSql->execute($arrParams);
				$this->db->dbClose();

				if ($execStatus === true){
					$retVal->setStatus(true);
					$retVal->setValue(NULL);
				} else {
					$retVal->setStatus(false);
					$retVal->setValue(NULL);
					$retVal->setMessage($preparedSql->errorInfo());
				}
				###
			} else {
				$retVal->setStatus(false);
				$retVal->setValue(NULL);
				$retVal->setMessage("No matching key(s)");
			}
			return $retVal;
		}

		################################################
		################################################

		//retrieve all information for a given ID
		public function idRetrieve($id){
			if ( is_numeric($id) && count($this->primaryKey)==0 ){
				$this->setPKArr( array("id"=>$id) );
				return $this->retrieve();
			} else {
				throw new Exception(INCORRECT_VALUE);
			}
		}

		//retrieve all information for given customKeys by an array
		public function retrieve(){
			$retVal = new Mirana_Protocol();
			if ( count($this->primaryKey) > 0){
				if (!$this->db->dbPing()) $this->db->dbConnect();
				####
				$arrVarKeys = array();
				$arrParams = NULL;
				##################################################
				foreach ($this->primaryKey as $e){
					$arrVarKeys[] = "$e = :$e";
					$arrParams[":$e"] =  $this->$e;
				}

				##################################################
				$strWhere = implode( " AND ", $arrVarKeys );
				######DB SELECT########
				$query = array();
				$query[PGSQL] = "SELECT * FROM \"$this->tableName\"
										WHERE $strWhere
										";
				$query[MYSQL] = "SELECT * FROM $this->tableName
										WHERE $strWhere
										";
				#####################
				$sqlQuery =	$query[ $this->db->getDbType() ];

				$preparedSql = $this->db->getPDO()->prepare($sqlQuery);
				$preparedSql = $this->execBindValues($preparedSql, $arrParams); //add this
				$execStatus = $preparedSql->execute();
				//$execStatus = $preparedSql->execute($arrParams);
				$this->db->dbClose();

				if ( $execStatus === true ){
					$retVal->setStatus(true);
					$retVal->setCount(1);

					$result = $preparedSql->fetchAll(PDO::FETCH_ASSOC);
					if (count($result) > 0){
						$result = $result[0]; //get first element as $id is primaryKey

						foreach ($result as $key_ => $value_){
							//populated the object
							if ( !in_array($key_, $this->blacklistVars) ){
								$this->$key_ = $value_;
							} else {
								$errMessage = "Database field in table $this->tableName is violated";
								throw new Exception("<p>$errMessage</p>", 1);
							}
						}
						$retVal->setValue($this);
					} else {
						$retVal->setValue(NULL);
					}
				} else {
					$retVal->setStatus(false);
					$retVal->setValue(NULL);
					$retVal->setMessage($preparedSql->errorInfo());
				}
				####
				return $retVal;
			}
			return false;
		}

		//insert new row record into db
		public function insertDB_returning(){
			$retVal = new Mirana_Protocol();
			if ( count($this->primaryKey) > 0){
				if (!$this->db->dbPing()) $this->db->dbConnect();
				//prepare SQL
				$arrVarKeys = array();
				$arrParams = NULL;
				##################################################
				foreach ($this as $key_ => $value_) {
					if ( !in_array($key_, $this->blacklistVars) && $value_!==NULL ) {
						$arrVarKeys[] = $key_;
						$arrParams[":$key_"] =  $value_;
					}
				}
				$strVars = implode(",",$arrVarKeys);
				$strValues = ":".implode(",:",$arrVarKeys);

				//actually execute into db
				if (property_exists($this, "id") && $this->id === NULL){
					$primaryKey = "id";
					######DB SELECT########
					$query = array();
					$query[PGSQL][] = "INSERT INTO \"$this->tableName\" ($strVars) VALUES ($strValues) RETURNING $primaryKey";

					$query[MYSQL][] = "INSERT INTO $this->tableName ($strVars) VALUES ($strValues)";
					$query[MYSQL][] = "SELECT LAST_INSERT_ID() $primaryKey";
					#####################

					foreach( $query[ $this->db->getDbType() ] as $e ){
					//loop through query array
						$sqlQuery = $e;
						$preparedSql = $this->db->getPDO()->prepare($sqlQuery);
						$preparedSql = $this->execBindValues($preparedSql, $arrParams); //add this
						$result = $preparedSql->execute();
						//$execStatus = $preparedSql->execute($arrParams);
						if ( $result === false ){
							$retVal->setStatus(false);
							$retVal->setValue(NULL);
							$retVal->setMessage($preparedSql->errorInfo());
							return $retVal;
						}
						$result = $preparedSql->fetch(PDO::FETCH_ASSOC);
						$this->$primaryKey = isset($result[$primaryKey])?$result[$primaryKey]:null;
					}
					//return id of new record
					$retVal->setStatus(true);
					$retVal->setValue($this->$primaryKey);
					###
					$this->db->dbClose();
				} else {
					//no ID field
				}
			} else {
				//no PK
			}
			return $retVal;
		}

		//insert new row (record) into db
		public function insertDB(){
			$retVal = new Mirana_Protocol();
			if ( count($this->primaryKey) > 0){
				if (!$this->db->dbPing()) $this->db->dbConnect();
				//prepare SQL
				$arrVarKeys = array();
				$arrParams = NULL;
				##################################################
				foreach ($this as $key_ => $value_) {
					if ( !in_array($key_, $this->blacklistVars) && $value_!==NULL ) {
						$arrVarKeys[] = $key_;
						$arrParams[":$key_"] =  $value_;
					}
				}
				$strVars = implode(",",$arrVarKeys);
				$strValues = ":".implode(",:",$arrVarKeys);

				######DB SELECT########
				$query = array();
				$query[PGSQL] = "INSERT INTO \"$this->tableName\" ($strVars) VALUES ($strValues)";
				$query[MYSQL] = "INSERT INTO $this->tableName ($strVars) VALUES ($strValues)";
				#####################

				//actually execute into db
				$sqlQuery = $query[ $this->db->getDbType() ] ;

				$preparedSql = $this->db->getPDO()->prepare($sqlQuery);
				$preparedSql = $this->execBindValues($preparedSql, $arrParams); //add this
				$execStatus = $preparedSql->execute();
				//$execStatus = $preparedSql->execute($arrParams);
				$this->db->dbClose();

				if ($execStatus === true){
					$retVal->setStatus(true);
					$retVal->setValue(NULL);
				} else {
					$retVal->setStatus(false);
					$retVal->setValue(NULL);
					$retVal->setMessage($preparedSql->errorInfo());
				}
				###
			} else {
				//no PK
			}
			return $retVal;
		}
	}
?>
