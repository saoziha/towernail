<?php
    /*--
    COMPANY: ezdev (former: the3ds)
    AUTHOR: Tom DINH
    PROJECT: mirana core v2
    DESCRIPTION: Domain register class
    --*/

    class Mirana_Domain {
        #property declaration
        protected static $defined;

        public function __construct(){
			$defined = []; //TODO: save this to somewhere
            return NULL;
		}

        public static function add($domainName, $appName){
            if (is_string($domainName) && is_string($appName)){
                $domainName = strtolower(trim($domainName));
                $appName    = strtolower(trim(trim(trim($appName, "\\"), "/")));
                self::$defined[$domainName] = $appName;
            }
            return NULL;
        }

        public static function getDomainApp(){
            $retVal = NULL;
            $domainName = strtolower(CommonUlti::urlStandardize($_SERVER['HTTP_HOST']));

            //ignore port
            $foundPos = strpos($domainName, ":");
            if (is_numeric($foundPos)){
                $domainName = substr_replace($domainName, "", $foundPos, strlen($domainName) - $foundPos);
            }

            //find if the domain name is matched
            if (isset(self::$defined[$domainName])){
                $retVal = self::$defined[$domainName];
            } else {
                ;
            }
            return $retVal;
        }
    }
?>
