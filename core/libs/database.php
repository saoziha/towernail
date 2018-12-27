<?php
    /*--
    COMPANY: ezdev (former: the3ds)
    AUTHOR: Tom DINH
    PROJECT: mirana core v2
    DESCRIPTION: model class
    DATE: Oct-2016
    --*/

    #Declare database object for DAO using
    //using for returned value in DB_CFGFILENAME
    class Mirana_Database {
        private static $dbList = [];

        public static function add($dbKey, $dbProperties){
            if (is_string($dbKey)){
                if (!isset(self::$dbList[$dbKey])){
                    $db = new DB_Object($dbProperties);
                    self::$dbList[$dbKey] =  $db;
                } else {
                    $errMessage = "Mirana_database add: duplicate key $dbKey for Mirana database pool.";
                    throw new Exception("<p>$errMessage</p>", 1);
                }
            } else {
                $errMessage = "Mirana_database add: \$dbKey must be string.";
                throw new Exception("<p>$errMessage</p>", 1);
            }
        }

        public static function getDataBaseList(){
            return self::$dbList;
        }

        public static function getDatabase($dbKey = NULL){
            $retVal = NULL;
            if (is_string($dbKey)){
                if ( isset(self::$dbList[$dbKey]) ){
                    $retVal = self::$dbList[$dbKey];
                } else {
                    ;
                }
            } else {
                $errMessage = "Mirana_database get: \$dbKey must be string.";
                throw new Exception("<p>$errMessage</p>", 1);
            }
            return $retVal;
        }

        public static function setDatabase($dbKey, $dbProperties){
            $db = new DB_Object($dbProperties);
            self::$dbList[$dbKey] =  $db;

            return NULL;
        }

        public static function clearDatabase(){
            self::$dbList = [];
            return NULL;
        }
	}
?>
