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
    class Mirana_Datasource {
        private static $dbList = [];

        public static function add($appName, $dbKey){
            if (is_string($appName) && is_string($dbKey)){
                $db = Mirana_Database::getDatabase($dbKey);
                if (!isset(self::$dbList[$appName][$dbKey])){
                    self::$dbList[$appName][$dbKey] = $db;


                } else {
                    $errMessage = "Datasource add: duplicate key $dbKey for $appName.";
                    throw new Exception("<p>$errMessage</p>", 1);
                }
            } else {
                $errMessage = "Datasource add: \$appName and \$dbKey must be string.";
                throw new Exception("<p>$errMessage</p>", 1);
            }
        }

        public static function getDataSourceList($appName){
            if (is_string($appName)){
                return self::$dbList[$appName];
            } else {
                $errMessage = "Datasource get list: \$appName must be string.";
                throw new Exception("<p>$errMessage</p>", 1);
            }
        }

        public static function getDbObject($dbKey){
            $appName = Mirana_Routing::getApp();

            $retVal = NULL;
            if (is_string($appName) && is_string($dbKey)){
                if (isset(self::$dbList[$appName][$dbKey])){
                    $retVal = self::$dbList[$appName][$dbKey];
                } else {
                    ;
                }
            } else {
                $errMessage = "Datasource get: \$appName and \$dbKey must be string.";
                throw new Exception("<p>$errMessage</p>", 1);
            }
            return $retVal;
        }

        public static function getDatabase($dbKey){
            //back compatible with previous release
            return self::getDbObject($dbKey);
        }
        //TODO: can not delete database as the class is already dedfined

        private static function declarationClass($appName, $dbKey, $tableName){
            $retVal = true;
            if (is_string($appName) && is_string($dbKey) && is_string($tableName)){
                if ( Mirana_Datasource::getDbObject($dbKey)!==NULL ){
                    try {
                        $DB_NAMESPACE = DB_NAMESPACE;
                        eval("
                            namespace $appName\\$DB_NAMESPACE\\$dbKey;
                            use ValueObject, Mirana_Datasource;

                            class $tableName extends ValueObject {
                                public function __construct(){
                                    parent::__construct(Mirana_Datasource::getDbObject(\"$dbKey\"));
                                }
                            }
                        ");
                    } catch (Exception $e){
                        $retVal = false;
                    }
                } else {
                    $errMessage = "Function Mirana_Datasource::declarationClass(): database=\$dbKey not found.";
                    throw new Exception("<p>$errMessage</p>", 1);
                }
            } else {
                $errMessage = "Function Mirana_Datasource::declarationClass() requires string parameter(s).";
                throw new Exception("<p>$errMessage</p>", 1);
            }
            return $retVal;
        }

        public static function newTableInstance($dbKey, $tableName){
            $retVal = NULL;
            if (is_string($tableName) && is_string($dbKey)){
                $appName = Mirana_Routing::getApp();
                $namespace = $appName.NS.DB_NAMESPACE.NS.$dbKey;

                $class = NS.CommonUlti::namespaceStandardize($namespace).NS.$tableName;
                if (class_exists($class)){
                    $retVal = new $class();
                } else {
                    if (self::declarationClass($appName, $dbKey, $tableName)){
                        $retVal = new $class();
                    } else {
                        $errMessage = "Error happens while declaring tableInstance of table: $tableName in database: $dbKey.";
                        throw new Exception("<p>$errMessage</p>", 1);
                    }
                }
            } else {
                $errMessage = "Function Mirana_Datasource::newTableInstance() requires string parameter(s).";
                throw new Exception("<p>$errMessage</p>", 1);
            }
            return $retVal;
        }
	}
?>
