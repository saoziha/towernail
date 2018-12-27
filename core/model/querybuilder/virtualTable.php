<?php
	/*-----
	COMPANY: the3ds team
	AUTHOR: Tom DINH
	PROJECT: core DA_Engine
	DESCRIPTION: --
	DATE: 12/May/2015
	Ver 1.2
	-----*/
    //TODO: version for PGSQL

    class VirtualTable{
		private $dbType;
		private $dataList;
        private $tableList;
        private $method;
        private $condition;
        private $acceptMethod;

        public function __construct ($dbType = "", $dataList = NULL, $tableList = NULL, $method = "", $condition = NULL){
            $this->acceptMethod = ["", "left", "right", "inner", "outer"];

            $this->setDatabase($dbType);
			$this->setDataList($dataList);
            $this->setTableList($tableList);
            $this->setMethod($method);
            $this->setCondition($condition);
			$this->tableList = NULL;
        }

        public function setDatabase($inputVal){
            if ($inputVal === MYSQL){
                $this->dbType = $inputVal;
            } else if ($inputVal === MYSQL){
                $this->dbType = $inputVal;
            } else {
                $this->dbType = $inputVal;
            }
            return NULL;
        }

        public function setTableList($inputVal){
            if (is_array($inputVal)) {
				$temp = array();
				foreach ($inputVal as $table){
					if (is_string($table)){
						if ($this->dbType === PGSQL) ; #$this->src = "\"$inputVal\"";
						else if ($this->dbType === MYSQL) $temp[] = $table;
						else ;
					} else {
						throw new Exception(INCORRECT_VALUE);
					}
				}

				$this->tableList = $temp;
            }
            else if (is_string($inputVal)){
				$this->tableList = array($inputVal);
			}
			else if ($inputVal === NULL){
				;
			}
			else throw new Exception(INCORRECT_VALUE);
            return NULL;
        }

		public function getTableList(){
			$retVal = NULL;
            if ($this->tableList !== NULL && is_array($this->tableList)){
				$retVal = $this->tableList;
			} else {
				;
			}
            return $retVal;
        }

		public function setDataList($inputVal){
            if (is_array($inputVal)) {
				$temp = array();
				foreach ($inputVal as $data){
					if (is_string($data)){
						if ($this->dbType === PGSQL) ; #$this->src = "\"$inputVal\"";
						else if ($this->dbType === MYSQL) $temp[] = $data;
						else ;
					} else {
						throw new Exception(INCORRECT_VALUE);
					}
				}

				$this->dataList = $temp;
            }
			else if ($inputVal === NULL){
				;
			}
			else throw new Exception(INCORRECT_VALUE);
            return NULL;
        }

		public function getDataList(){
			$retVal = "";
			if ($this->dataList === NULL || count($this->dataList) <= 0){
				$retVal = "*";
			} else {
				$retVal = implode(", ", $this->dataList);
			}
			return $retVal;
		}

        public function setMethod($inputVal=""){
			$inputVal = strtolower($inputVal);
            if (is_string($inputVal) && in_array($inputVal, $this->acceptMethod))
                $this->method = $inputVal;
            else
                throw new Exception(INCORRECT_VALUE);
            return NULL;
        }

        public function setCondition($inputVal){
            if (is_array($inputVal) || $this->condition === NULL) $this->condition = $inputVal;
            else throw new Exception(INCORRECT_VALUE);
        }

        public function build(){
			$retVal = false;
			if ($this->tableList === NULL || count($this->tableList) <= 0){
				;
			} else if (count($this->tableList) === 1){
				if ($this->dbType === PGSQL) ; #$this->src = "\"$inputVal\"";
				else if ($this->dbType === MYSQL) $retVal = $this->tableList[0];
				else ;
			} else {
				#build join
				$sql = "";

				$t = array();
				foreach ($this->tableList as $e){
					$t[] = "($e)";
				}
				$sql .= implode(" $this->method join ", $t);
				$sql .= " on (";

	            $t = array();
	            if (is_array($this->condition)){
	                foreach ($this->condition as $u=>$v){
	                    if ($this->dbType === PGSQL)
	                        ; #$t[] = "(\"$this->src\".$u = \"$this->dst\".$v)";
	                    else if ($this->dbType === MYSQL)
	                        $t[] = "($u = $v)";
	                    else
	                        ;
	                }
	                $t = implode(" and ", $t);
	            }
	            $sql.= $t.")";

				$retVal = $sql;
			}
            return $retVal;
        }
    }
?>
