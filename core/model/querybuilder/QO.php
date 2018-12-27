<?php
	/*-----
	COMPANY: the3ds team
	AUTHOR: Tom DINH
	PROJECT: core DA_Engine
	DESCRIPTION: --
	DATE: 12/May/2015
	Ver 1.2
	-----*/

	class QueryObject {
		protected 	$tableName;
		protected 	$vTable;
		protected	$db;

		//constructor
		public function __construct($inputDB, $table = NULL){			
			if ( get_class($inputDB) === "DB_Object" ){
				$this->db = $inputDB;
			} else {
				throw new Exception("QueryObject constructor: Incorrect database passed", 1);
			}

			if (is_string($table) || $table === NULL){
				$this->setTableName($table);
				$this->vTable = NULL;
			} else if (get_class($inputDB) === "VirtualTable") {
				$this->setTableName(NULL);
				$this->setTable($table);
			} else {
				;
			}
		}

		################################################
		################################################
		################################################
		//getTableName
		private function getTableName(){
			return $this->tableName;
		}

		//setTableName
		private function setTableName($inputVal){
			if ( is_string($inputVal) ){
				//resolve namespace
				$tableName = explode("\\",$inputVal);
				$this->tableName = end($tableName);
				// $this->propertiesPreGen($inputVal);
				return true;
			} else if ($inputVal === NULL) {
				$this->tableName = NULL;
			} else {
				throw new Exception(INCORRECT_VALUE);
			}
			return NULL;
		}

		//getTable
		public function getTable(){
			return $this->vTable!==NULL?$this->vTable:$this->getTableName();
		}

		//setTable
		public function setTable($inputVal){
			if ( get_class($inputVal) === "VirtualTable" ){
				$this->vTable = $inputVal;
				$this->setTableName(NULL);
				return true;
			} else {
				$this->setTableName($inputVal);
			}
			return NULL;
		}

		################################################
		public function eliminateSingleValue($element, $inpVal){
			$retVal = $inpVal;
			if (isset($inpVal[RESULT_SQL]) && is_string($element)){
				if (is_array($inpVal[RESULT_SQL])){
					$retVal[RESULT_SQL] = array();

					foreach($inpVal[RESULT_SQL] as $e){
						if (isset($e[$element])){
							unset($e[$element]);
						} else {
							;
						}
						$retVal[RESULT_SQL][] = $e;
					}
				}
			} else {
				;
			}
			return $retVal;
		}

		public function eliminateMultiValue($arrVal, $inpVal){
			$retVal = $inpVal;
			if (isset($inpVal[RESULT_SQL]) && is_string($array)){
				if (is_array($inpVal[RESULT_SQL])){
					$retVal[RESULT_SQL] = array();

					foreach($inpVal[RESULT_SQL] as $e){
						foreach($arrVal as $element){
							if (isset($e[$element])){
								unset($e[$element]);
							} else {
								;
							}
						}
						$retVal[RESULT_SQL][] = $e;
					}
				}
			} else {
				;
			}
			return $retVal;
		}

		################################################
		################################################
		################################################

		public function executeSQL($sqlQuery, $arrParams=NULL){
			if (!$this->db->dbPing()) $this->db->dbConnect();
			###
			$preparedSql = $this->db->getPDO()->prepare($sqlQuery);

			$execStatus = $preparedSql->execute($arrParams);

			$retVal = NULL;
			if ( $execStatus === true ){
				$retVal[RESULT_SQL] = $preparedSql->fetchAll(PDO::FETCH_ASSOC);
			} else {
				$retVal[RESULT_SQL] = false;
				$retVal[ERR_SQL_INFO] = $preparedSql->errorInfo();
			}
			$this->db->dbClose();
			return $retVal;
		}
		################################################################################
		################################################################################
		//build up limit and offset params for paging
		protected function pagingBuildUp($itemPerPage, $pageNum){
			$stringLimit[PGSQL] = "LIMIT ALL";
			$stringLimit[MYSQL] = "LIMIT 18446744073709551615"; // what the fuck MYSQL??? WHY DONT YOU JUST PUT "ALL" LIKE PG?????
			#############################
			$strLimit = NULL;
			if ($itemPerPage == "ALL"){
				$strLimit = $stringLimit[ $this->db->getDbType() ];
			} else if (is_numeric($itemPerPage)){
				if ($itemPerPage < 0){
					$strLimit = $stringLimit[ $this->db->getDbType() ];
				} else {
					$strLimit = "LIMIT $itemPerPage";
				}
			} else {
				throw new Exception(INCORRECT_VALUE);
			}

			$strOffset = NULL;
			if (is_numeric($pageNum)){
				if (is_numeric($itemPerPage)){
					$pageNum = $itemPerPage*$pageNum;
				}
				$strOffset = "OFFSET $pageNum";
			} else {
				throw new Exception(INCORRECT_VALUE);
			}

			return array(
				"sqlLimit" => $strLimit,
				"sqlOffset" => $strOffset
			);
		}

		//build WHERE string sql
		protected function strkeysBuilder($props = [], $dateTimeValues = [], $caseSensitive = false, $buildType = "and"){
			$retVal = "";
			$acceptBuildType = array("and", "or"); //TODO: range
			if (!in_array(strtolower(trim($buildType)), $acceptBuildType)){
				throw new Exception(INCORRECT_VALUE." buildType.");
			}

			$arrVarKeys = array();
			if ( is_array($props) && count($props)>0 ){
				//build arrays for looking up
				foreach ($props as $key_ => $v){
					$slqKey = str_replace(".", "", $key_);//is it the use of virtual table?

					if (is_string($v) && !in_array($key_, $dateTimeValues)){
						if ($caseSensitive){
							$arrVarKeys[] = "$key_ LIKE :$slqKey";
						} else {
							$arrVarKeys[] = "LOWER($key_) LIKE LOWER(:$slqKey)";
						}
					} else if ($v === NULL) {
						$arrVarKeys[] = "$key_ IS NULL";
					} else if (is_array($v)) {
						//key in SET
						$t = array();
						foreach ($v as $i => $e){
							if (is_numeric($i) || is_string($e)){
								$t[] = ":prop_$slqKey$i";
							} else {
								// skip NULL or other types
								;
							}
						}
						$arrVarKeys[] = "$key_ IN (".implode(", ", $t).")";
					} else {
						$arrVarKeys[] = "$key_ = :$slqKey";
					}
				}
			} else {
				;
			}

			if (count($arrVarKeys)===0){
				$retVal = "";
			} else if (count($arrVarKeys)===1){
				$retVal = strval(end($arrVarKeys));
				$retVal = "($retVal)";
			} else if ($buildType === "and"){
				$retVal = implode( " AND ", $arrVarKeys );
				$retVal = "($retVal)";
			} else if ($buildType === "or") {
				$retVal = implode( " OR ", $arrVarKeys );
				$retVal = "($retVal)";
			} else {
				;
			}
			return $retVal;
		}

		//build params for WHERE string
		protected function paramsBuilder($props = [], $dateTimeValues = [], $exactString = false) {
			$retVal = $arrParams = [];

			if ( is_array($props) && count($props)>0 ){
				//build arrays for looking up
				foreach ($props as $key_ => $v){
					$slqKey = str_replace(".", "", $key_); //is it the use of virtual table?

					if (in_array($key_, $dateTimeValues)){
						//standardize date time
						$v = MainObject::formatDateTime($v, $this->db->getDbType());
					}

					if (is_string($v)){
						if ($exactString){
							$arrParams[":$slqKey"] =  $v;
						} else {
							$arrParams[":$slqKey"] =  "%$v%";
						}
					} else if (is_array($v)) {
						//key in SET
						foreach ($v as $i => $e){
							if (is_numeric($i) || is_string($e)){
								$arrParams[":prop_$slqKey$i"] =  $e;
							} else {
								// skip NULL or other types
								;
							}
						};
					} else {
						$arrParams[":$slqKey"] =  $v;
					}
				}
			} else {
				;
			}
			$retVal = $arrParams;

			return $retVal;
		}

		//standardize return value
		public function processReturn($dbType, $query, $arrParams, $pdo){
			$retVal = new Mirana_Protocol();
			for($i = 0; $i < count($query[ $dbType ]); $i++){
				$sqlQuery = $query[ $this->db->getDbType() ][ $i ] ;

				$preparedSql = $pdo->prepare($sqlQuery);
				$execStatus = $preparedSql->execute($arrParams);
				if ($execStatus === true){
					$retVal->setStatus(true);
					switch ($i){
						case 0: $retVal->setCount(intval($preparedSql->fetchAll(PDO::FETCH_ASSOC)[0]['count'])); break;
						case 1: $retVal->setValue($preparedSql->fetchAll(PDO::FETCH_ASSOC)); break;
						default: break;
					}
				} else {
					$retVal->setStatus(false);
					$retVal->setMessage($preparedSql->errorInfo());
					break;
				}
			}
			return $retVal;
		}
		################################################################################
		################################################################################
		//free lookup sql builder
		public function lookup_sqlbuilder($values, $props, $exactString, $orderBy, $itemPerPage, $pageNum, $descOrder){
			$retVal = NULL;

			if ( is_array($values) && is_array($props) && is_bool($exactString)){
				//paging
				$paging = $this->pagingBuildUp($itemPerPage, $pageNum);
				$strLimit = strval($paging["sqlLimit"]);
				$strOffset = strval($paging["sqlOffset"]);

				$strWhere = "";
				$dateTimeValues = [];
				$values_SqlString = $this->strkeysBuilder($values, $dateTimeValues, false, "or"); //case sensitive = false
				$props_SqlString = $this->strkeysBuilder($props, $dateTimeValues, true, "and"); //case sensitive = true

				if (strlen($values_SqlString)===0 && strlen($props_SqlString)===0){
					$strWhere = "";
				} else if (strlen($values_SqlString)>0 && strlen($props_SqlString)>0){
					$strWhere = "WHERE $values_SqlString AND $props_SqlString";
				} else {
					$strWhere = "";
					if (strlen($values_SqlString)>0){
						$strWhere = "WHERE $values_SqlString";
					}
					if (strlen($props_SqlString)>0){
						$strWhere = "WHERE $props_SqlString";
					}
				}

				$arrParams = array();
				$values_arrParams = $this->paramsBuilder($values, $dateTimeValues, $exactString);
				$props_arrParams = $this->paramsBuilder($props, $dateTimeValues, $exactString);
				$arrParams = $props_arrParams + $values_arrParams;
				$retVal["arrParams"] = $arrParams;
				#################################################################
				//setup order values
				$strOrderBy = "";
				if ( $orderBy === NULL){
					;
				} else if ( is_string($orderBy) ){
					if (is_bool($descOrder)){
						$dsc = $descOrder?"DESC":"";
					} else if (is_array($descOrder) && implode("",$descOrder)===$orderBy){
						$dsc = "DESC";
					} else if (is_string($descOrder) && strtolower(trim($descOrder))===strtolower(trim($orderBy))){
						$dsc = "DESC";
					} else {
						$dsc = "";
					}
					$strOrderBy = "ORDER BY $orderBy $dsc";
				} else if ( is_array($orderBy) ) {
					$stringMaker = array();
					foreach ($orderBy as $e){
						if (is_bool($descOrder)){
							$stringMaker[] = $e.$descOrder?"DESC":"";
						} else if (is_array($descOrder) && in_array($e, $descOrder)){
							$stringMaker[] = $e." DESC";
						} else if (is_string($descOrder) && strtolower(trim($e))===strtolower(trim($descOrder))) {
							$stringMaker[] = $e." DESC";
						} else {
							$stringMaker[] = $e;
						}
					}
					$strOrderBy = "ORDER BY ".implode( ", ", $stringMaker );
				} else {
					throw new Exception("Incorrect value for orderBy");
				}

				######DB SELECT########
				$query = array();
				if ($this->tableName !== NULL)	{
					$queryTable = $this->tableName;

					$query[PGSQL][0] = "SELECT COUNT(*) AS count FROM \"$queryTable\" $strWhere";
					$query[PGSQL][1] = "SELECT * FROM \"$queryTable\"
										$strWhere $strOrderBy $strLimit $strOffset";

					$query[MYSQL][0] =	"SELECT COUNT(*) AS count FROM $queryTable	$strWhere";
					$query[MYSQL][1] =	"SELECT * FROM $queryTable
										$strWhere $strOrderBy $strLimit $strOffset";
				} else if ($this->vTable !== NULL) {
					$queryTable = $this->vTable->build();
					$dataList = $this->vTable->getDataList();

					$query[PGSQL][0] =
					$query[MYSQL][0] = 	"SELECT COUNT(*) AS count FROM $queryTable $strWhere";

					$query[PGSQL][1] =
					$query[MYSQL][1] = 	"SELECT $dataList FROM $queryTable
										$strWhere $strOrderBy $strLimit $strOffset";
				} else {
					;
				}
				$retVal["querry"] = $query;
			} else {
				throw new Exception("Incorrect values for either values, props or exactString");
			}
			return $retVal;
		}
		//free look up
		public function lookup($inputData = []){
			//terminate if neither table name nor virtual table is not defined
			if ($this->tableName === NULL && $this->vTable === NULL) return NULL;
			//values that user might passed in
			$listVar = ["values", "props", "orderBy", "itemPerPage", "pageNum", "exactString", "descOrder"];
			$defaultValues = [
				"values" => [],
				"props" => [],
				"orderBy" => NULL,
				"itemPerPage" => -1,
				"pageNum" => 0,
				"exactString" => true,
				"descOrder" => NULL
			];
			foreach ($inputData as $u=>$v){
				if (!in_array($u, $listVar)) {
					unset($inputData[$u]);
				} else {
					;
				}
			}
			foreach ($listVar as $e){
				if (!isset($inputData[$e])){
					$inputData[$e] = $defaultValues[$e];
				} else {
					;
				}
			}
			$sql 	= $this->lookup_sqlbuilder(
						$inputData["values"], $inputData["props"],
						$inputData["exactString"], $inputData["orderBy"],
						$inputData["itemPerPage"], $inputData["pageNum"], $inputData["descOrder"]
					);
			//setup new db connection
			if (!$this->db->dbPing()) $this->db->dbConnect();
			$retVal = $this->processReturn($this->db->getDbType(), $sql["querry"], $sql["arrParams"], $this->db->getPDO());
			$this->db->dbClose();
			return $retVal;
		}

		public function freeLookup($values = array(), $props = array(), $orderBy=NULL, $itemPerPage = -1,
			$pageNum = 0, $exactString=true, $descOrder=NULL){
			return $this->lookup(
				[
				   "values" => $values,
				   "props" => $props,
				   "orderBy" => $orderBy,
				   "itemPerPage" => $itemPerPage,
				   "pageNum" => $pageNum,
				   "exactString" => $exactString,
				   "descOrder" => $descOrder
			   ]
			);
		}
		################################################################################
		//find record(s) in range
		public function lookupRange($varName, $minVal, $maxVal, $props = NULL, $itemPerPage = -1, $pageNum = 0, $descOrder = false){
			//terminate if table name is not defined
			if ($this->tableName === NULL) return NULL;

			if (is_numeric($minVal) && is_numeric($maxVal) && ($props===NULL||is_array($props))){
				if (!$this->db->dbPing()) $this->db->dbConnect();
				###
				$paging = $this->pagingBuildUp($itemPerPage, $pageNum);
				$strLimit = $paging["sqlLimit"];
				$strOffset = $paging["sqlOffset"];

				$arrParams = NULL;
				$arrParams[":minVal"] = $minVal;
				$arrParams[":maxVal"] = $maxVal;

				$dsc = $descOrder?"DESC":"";
				//setting up props
				$strWhere = "";
				$dateTimeValues = array();

				$strProps = $this->strkeysBuilder($props, $dateTimeValues, true, true, "and");
				$props_arrParams = $this->paramsBuilder($props, $dateTimeValues, true);
				$arrParams = $props_arrParams + $arrParams;

				######DB SELECT########
				$query = array();
				if ($this->tableName !== NULL)	{
					$queryTable = $this->tableName;

					$query[PGSQL][0] = "SELECT COUNT(*) AS count FROM \"$queryTable\"
												WHERE ($varName>=:minVal AND $varName<=:maxVal) $strProps";
					$query[PGSQL][1] = "SELECT * FROM \"$queryTable\"
												WHERE ($varName>=:minVal AND $varName<=:maxVal) $strProps
												ORDER BY $varName $dsc $strLimit $strOffset";
					$query[MYSQL][0] = "SELECT COUNT(*) AS count FROM $queryTable
												WHERE ($varName>=:minVal AND $varName<=:maxVal) $strProps";
					$query[MYSQL][1] = "SELECT * FROM $queryTable
												WHERE ($varName>=:minVal AND $varName<=:maxVal) $strProps
												ORDER BY $varName $dsc $strLimit $strOffset";
				} else if ($this->vTable !== NULL) {
					$queryTable = $this->vTable->build();
					$dataList = $this->vTable->getDataList();

					$query[PGSQL][0] = $query[MYSQL][0] = "SELECT COUNT(*) AS count FROM $queryTable
												WHERE ($varName>=:minVal AND $varName<=:maxVal) $strProps";
					$query[PGSQL][1] = $query[MYSQL][1] = "SELECT $dataList FROM $queryTable
												WHERE ($varName>=:minVal AND $varName<=:maxVal) $strProps
												ORDER BY $varName $dsc $strLimit $strOffset";
				} else {
					;
				}
				#####################
				$retVal = $this->processReturn($this->db->getDbType(), $query, $arrParams, $this->db->getPDO());
				$this->db->dbClose();
				return $retVal;
			} else {
				throw new Exception(INCORRECT_VALUE);
			}
		}

		//find record(s) given time as needle
		public function lookupSingleDate($varName, $varValue, $props = NULL, $itemPerPage = -1, $pageNum = 0, $descOrder = false){
			//terminate if table name is not defined
			if ($this->tableName === NULL) return NULL;

			$varValue = $this->validateDate($varValue);
			if ($varValue !== false && ($props===NULL||is_array($props))){
				if (!$this->db->dbPing()) $this->db->dbConnect();
				###
				$paging = $this->pagingBuildUp($itemPerPage, $pageNum);
				$strLimit = $paging["sqlLimit"];
				$strOffset = $paging["sqlOffset"];

				$arrParams = NULL;
				$arrParams[":varValue"] = MainObject::formatDateTime($varValue, $this->db->getDbType());

				$dsc = $descOrder?"DESC":"";
				//setting up props
				$strWhere = "";
				$dateTimeValues = array();

				$strProps = $this->strkeysBuilder($props, $dateTimeValues, true, true, "and");
				$props_arrParams = $this->paramsBuilder($props, $dateTimeValues, true);
				$arrParams = $props_arrParams + $arrParams;

				######DB SELECT########
				$query = array();
				if ($this->tableName !== NULL)	{
					$queryTable = $this->tableName;

					$query[PGSQL][0] = "SELECT COUNT(*) AS count FROM \"$queryTable\"
												WHERE $varName=:varValue $strProps";
					$query[PGSQL][1] = "SELECT * FROM \"$queryTable\"
												WHERE $varName=:varValue $strProps
												ORDER BY $varName $dsc $strLimit $strOffset";
					$query[MYSQL][0] = "SELECT COUNT(*) AS count FROM $queryTable
												WHERE $varName=:varValue $strProps";
					$query[MYSQL][1] = "SELECT * FROM $queryTable
												WHERE $varName=:varValue $strProps
												ORDER BY $varName $dsc $strLimit $strOffset";
				} else if ($this->vTable !== NULL) {
					$queryTable = $this->vTable->build();
					$dataList = $this->vTable->getDataList();

					$query[PGSQL][0] = $query[MYSQL][0] = "SELECT COUNT(*) AS count FROM $queryTable
												WHERE $varName=:varValue $strProps";
					$query[PGSQL][1] = $query[MYSQL][1] = "SELECT $dataList FROM $queryTable
												WHERE $varName=:varValue $strProps
												ORDER BY $varName $dsc $strLimit $strOffset";
				} else {
					;
				}
				#####################
				$retVal = $this->processReturn($this->db->getDbType(), $query, $arrParams, $this->db->getPDO());
				$this->db->dbClose();
				return $retVal;
			} else {
				throw new Exception(INCORRECT_VALUE);
			}
		}

		//find record(s) given with time in range
		public function lookupDateRange($varName, $minVal, $maxVal, $props = NULL, $itemPerPage = -1, $pageNum = 0, $descOrder = false){
			//terminate if table name is not defined
			if ($this->tableName === NULL) return NULL;

			$minVal =$this->validateDate($minVal);
			$maxVal =$this->validateDate($maxVal);
			if ($minVal!==false && $maxVal!==false && ($props===NULL||is_array($props))){
				if (!$this->db->dbPing()) $this->db->dbConnect();
				###
				$paging = $this->pagingBuildUp($itemPerPage, $pageNum);
				$strLimit = $paging["sqlLimit"];
				$strOffset = $paging["sqlOffset"];

				$arrParams = NULL;
				$arrParams[":minVal"] = MainObject::formatDateTime($minVal, $this->db->getDbType());
				$arrParams[":maxVal"] = MainObject::formatDateTime($maxVal, $this->db->getDbType());

				$dsc = $descOrder?"DESC":"";
				//setting up props
				$strWhere = "";
				$dateTimeValues = array();

				$strProps = $this->strkeysBuilder($props, $dateTimeValues, true, true, "and");
				$props_arrParams = $this->paramsBuilder($props, $dateTimeValues, true);
				$arrParams = $props_arrParams + $arrParams;

				######DB SELECT########
				$query = array();
				if ($this->tableName !== NULL)	{
					$queryTable = $this->tableName;

					$query[PGSQL][0] = "SELECT COUNT(*) AS count FROM \"$queryTable\"
												WHERE ($varName>=:minVal AND $varName<=:maxVal) $strProps";
					$query[PGSQL][1] = "SELECT * FROM \"$queryTable\"
												WHERE ($varName>=:minVal AND $varName<=:maxVal) $strProps
												ORDER BY $dsc $varName $strLimit $strOffset";
					$query[MYSQL][0] = "SELECT COUNT(*) AS count FROM $queryTable
												WHERE ($varName>=:minVal AND $varName<=:maxVal) $strProps";
					$query[MYSQL][1] = "SELECT * FROM $queryTable
												WHERE ($varName>=:minVal AND $varName<=:maxVal) $strProps
												ORDER BY $dsc $varName $strLimit $strOffset";
				} else if ($this->vTable !== NULL) {
					$queryTable = $this->vTable->build();
					$dataList = $this->vTable->getDataList();

					$query[PGSQL][0] = $query[MYSQL][0] = "SELECT COUNT(*) AS count FROM $queryTable
												WHERE ($varName>=:minVal AND $varName<=:maxVal) $strProps";
					$query[PGSQL][1] = $query[MYSQL][1] = "SELECT $dataList FROM $queryTable
												WHERE ($varName>=:minVal AND $varName<=:maxVal) $strProps
												ORDER BY $dsc $varName $strLimit $strOffset";
				} else {
					;
				}
				#####################
				$retVal = $this->processReturn($this->db->getDbType(), $query, $arrParams, $this->db->getPDO());
				$this->db->dbClose();
				return $retVal;
			} else {
				throw new Exception(INCORRECT_VALUE);
			}
		}
	}
?>
