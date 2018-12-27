<?php
	/*-----
	COMPANY: the3ds team
	AUTHOR: Tom DINH
	PROJECT: core DA_Engine
	DESCRIPTION: --
	DATE: 09/Sep/2016
	Ver 1.6
	-----*/

	class Mirana_Protocol {
        private static	$value 		= NULL;
		private static	$message 	= NULL;
		private static	$status 	= NULL;
		private static	$count 		= NULL;

        ########################################################################
        ########################################################################

        public static function setStatus($inpVal){
            $retVal = false;
            if (is_bool($inpVal)){
                self::$status = $inpVal;
                $retVal = true;
            }
            return $retVal;
        }

        public static function setValue($inpVal = NULL){
            $retVal = false;
            if ($inpVal !== NULL){
                self::$value = $inpVal;
                $retVal = true;
            }
            return $retVal;
        }

        public static function setCount($inpVal){
            $retVal = false;
            if (is_integer($inpVal)){
                self::$count = $inpVal;
                $retVal = true;
            }
            return $retVal;
        }

        public static function setMessage($inpVal){
            $retVal = false;
            if (is_string($inpVal)){
                self::$message = $inpVal;
                $retVal = true;
            }
            return $retVal;
        }
		################################################
        public static function parseArray(){
            $retVal = array();
            $retVal[STATUS_SQL] = self::$status;
            $retVal[COUNT_SQL] = self::$count;
            $retVal[VALUE_SQL] = self::$value;
            $retVal[MESSAGE_SQL] = self::$message;

            return $retVal;
        }

		public static function getStatus(){
            $retVal = self::$status;
            return $retVal;
        }

        public static function getValue(){
			$retVal = self::$value;
            return $retVal;
        }

        public static function getCount(){
			$retVal = self::$count;
            return $retVal;
        }

        public static function getMessage(){
			$retVal = self::$message;
            return $retVal;
        }
		################################################
		################################################

		public static function handleWebservice($mirana_query_string){
			//format: app/webservice/package[/.../location]/className/functionName
			//post+get values as inputData
			$retVal = NULL;

			$app = Mirana_Routing::getApp();
			$url = CommonUlti::urlStandardize($mirana_query_string);
			//remove the "webservice/" from url
			$t = explode(FS, $url);
			array_splice($t,0,1);
			$url = implode(FS, $t);
			//get inputData
			$inputData = self::parseInputdata();

			$result = self::wsCall($url, $inputData);
			//encode by JSON for webservice
			$retVal = json_encode($result);
			return $retVal;
		}

		public static function wsCall($url, $inputData = []){
			$retVal = NULL;
			$app = Mirana_Routing::getApp();

			if ($securityApp=true){ //TODO: write security
				$t = explode(FS, $url);
				//function calculation
				if (count($t)>1){
					$function = array_pop($t);
				} else {
					$function = NULL;
				}
				//class calculation
				if (count($t)>1){
					$class = array_pop($t);
				} else {
					$class = NULL;
				}
				//packageName calculation & load required
				$packageName = implode(FS, $t);
				Mirana_Package::manualLoad($packageName);
				//namespace calculation
				array_unshift($t, $app);
				$namespace = NS.implode(NS, $t);
				//register Database
				$dbRegFile = Mirana_Folder::getAppFolder($app).DS.APP_DBFILE;
				if (is_file($dbRegFile)) require_once $dbRegFile;
				//load required package
				Mirana_Package::manualLoad($packageName);

				if (is_string($namespace) && is_string($class) && is_string($function) && is_array($inputData)){
					$retVal = self::execute_func($namespace, $class, $function, $inputData);
				} else {
					;
				}
			} else {
				$retVal = json_encode("fail security");
			}
			return $retVal;
		}
		##########################################################################
		##########################################################################
		##########################################################################
		public static function execute_func($namespace, $class, $function, $inputData=[]){
            //execute user defined class's method
            $retVal = NULL;
            $namespace = NS.CommonUlti::namespaceStandardize($namespace);
			if( is_string($namespace) && is_string($class) && is_string($function) && is_array($inputData)){
				//create class instance
				$class = "$namespace\\$class";
				if (class_exists($class)){
					$object_ = new $class();
                    //TODO: check class accessControl, function accessControl (secure data)
					if (method_exists($object_, $function)){
						//create reflection method of function
						$ref = new ReflectionMethod($class, $function);
						$requiredParamCount = intval( $ref->getNumberOfRequiredParameters() );
						$paramList = array();
						foreach ( $ref->getParameters() as $param){
							$paramList[] = $param->name;
						}

						$paramValues = array();
						foreach($paramList as $e){
							if ( isset($inputData[$e]) ){
								//filling up paramValues
								$paramValues[] = $inputData[$e];
							}
						}

						$objectArr = array($object_, trim($function));
						//get return value of the method
						if ( count($paramValues) >= $requiredParamCount ){
							try {
								$retVal = call_user_func_array( $objectArr, $paramValues );
							} catch (Exception $e){
                                $errMessage = "In class: $class, function: $function.<br>".$e->getMessage();
                                $errMessage = "<p>$errMessage</p>";

                                throw new Exception($errMessage, 1);
							}
						} else {
                            $errMessage = ERR_REQUIRED_VAL." Class=$class, Function=$function.";
                            $errMessage = "<p>$errMessage</p>";
                            throw new Exception($errMessage, 1);
						}
					} else {
                        $errMessage = ERR_NO_FUNCTION." Class=$class, Function=$function.";
                        $errMessage = "<p>$errMessage</p>";
                        throw new Exception($errMessage, 1);
					}
				} else {
                    $errMessage = ERR_NO_CLASS." Class=$class.";
                    $errMessage = "<p>$errMessage</p>";
                    throw new Exception($errMessage, 1);
				}
			} else {
                $errMessage = ERR_DATATYPE." Check parameter(s)' datatype for Mirana_C::execute_func()";
                $errMessage = "<p>$errMessage</p>";
                throw new Exception($errMessage, 1);
            }
			return $retVal;
		}

		public static function parseInputdata(){
			$retVal = [];
			//parse POST DATA
			if (isset($_POST) && is_array($_POST)){
				// for each value in POST, allow them to go directly into modelling phase
				foreach ( $_POST as $key => $value ){
					//accept all values
					if (sizeof($value) <= POST_REQUEST_INPUT_LIMIT){
						$retVal[$key] = $value;
					} else {
						;
					}
				}
			}
			/**********************************************************/
			//parse GET DATA;
			if (isset($_GET) && is_array($_GET)){
				foreach ( $_GET as $key => $value ){
					if (!isset($retVal[$key])){
						//if there's more than 1 value(s), $_POST value will be in priority
						if (sizeof($value) <= GET_REQUEST_INPUT_LIMIT){
							if (is_string($value)){
								$retVal[$key] = $value; //only accepts String(s) for GET
							} else {
								;
							}
						} else {
							;
						}
					} else {
						;
					}
				}
			}

			return $retVal;
		}

		###########################################################################
		###########################################################################

		public static function getUrlSeparatedValues(){
            $getVal = isset($_GET[GET_REQUEST])?strval($_GET[GET_REQUEST]):"";
            $getVal = explode("/", $getVal);

            if (sizeof($getVal)<1) $getVal[] = "";
            return $getVal;
        }
	}
?>
