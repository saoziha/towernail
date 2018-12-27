<?php
    /*--
    COMPANY: ezdev (former: the3ds)
    AUTHOR: Tom DINH
    PROJECT: mirana core v2
    DESCRIPTION: Package management class
    --*/

    class Mirana_Package {
        #property declaration
        protected static $defined = [];
        protected static $namespace = NULL;
        protected static $ws_map;

        public function __construct(){
            $ws_map = [];
            return NULL;
		}

        public static function add($appName, $packageName){
            $packageName = CommonUlti::locationStandardize($packageName);
            if (is_string($appName) && is_string($packageName) &&
                (
                    is_dir(CommonUlti::locationStandardize(Mirana_Folder::getPackageFolder($appName).$packageName))
                    || is_file(CommonUlti::locationStandardize(Mirana_Folder::getPackageFolder($appName).$packageName))
                )
            ){
                self::$defined[$appName][] = $packageName;
            }
            return NULL;
        }

        public static function autoload(){
            ob_start();
            $appName = Mirana_Routing::getApp();
            if (isset(self::$defined[$appName])){
                foreach (self::$defined[$appName] as $package){
                    Mirana_Loader::loadPackage($appName, $package);
                }
            } else {
                ;
            }
            $message = ob_get_clean();
            return $message;
        }

        public static function manualLoad($packages, $recursive = true){
            //load all package needed manually
            ob_start();
            $appName = Mirana_Routing::getApp();
            if (is_array($packages)){
                foreach ($packages as $package){
                    if (is_string($package)){
                        if (is_bool($recursive)){
                            Mirana_Loader::loadPackage($appName, $package, $recursive);
                        } else {
                            throw new Exception("manualLoad function's second parameter must be boolean", 1);
                        }
                    } else {
                        throw new Exception("manualLoad function's first parameter must be array of strings", 1);
                    }
                }
            } else if (is_string($packages)){
                Mirana_Loader::loadPackage($appName, $packages, $recursive);
            } else {
                throw new Exception("manualLoad function's first parameter must be single array", 1);
            }
            $message = ob_get_clean();
            return $message;
        }

        public static function setPackage($packageName){
            if (is_string($packageName)){
                $appName = Mirana_Routing::getApp();
                self::$namespace = NS.CommonUlti::namespaceStandardize($appName.NS.$packageName); //absolute
            } else {
                ;
            }
            return NULL;
        }

        public static function clearPackage(){
            self::$namespace = NULL;
            return NULL;
        }

        public static function newInstance($className, $packageName = NULL){
            //packageName is subNamespace while appName is the Global namespace for app
            $retVal = NULL;
            if (is_string($className) && (is_string($packageName) || $packageName===NULL)){
                $appName = Mirana_Routing::getApp();
                if ($packageName === NULL){
                    if (self::$namespace!==NULL){
                        $namespace = self::$namespace;
                    } else {
                        throw new Exception("
                            PackageName is not set. You might need to manually set it at setNamespace(className, packageName) or setPackage().
                            Mirana_Package::newInstance($className, $packageName).
                        ", 1);
                    }
                } else {
                    $namespace = $appName.NS.$packageName;
                }

                $class = NS.CommonUlti::namespaceStandardize($namespace.NS.$className);
                if (class_exists($class)){
                    $retVal = new $class();
                } else {
                    throw new Exception("
                        Class $class is not existed. You might need to load the package contained the class.
                    ", 1);
                }
            } else {
                throw new Exception("Function Mirana_Package::newInstance() requires string parameter(s).");
            }
            return $retVal!==NULL?$retVal:$message;
        }
        #################################################
        #################################################
        public static function includeWs($appName, $ws_name, $packageName){
            $packageName = CommonUlti::locationStandardize($packageName);
            if (is_string($appName) && is_string($packageName) && is_string($ws_name) &&
                (
                    is_dir(CommonUlti::locationStandardize(Mirana_Folder::getPackageFolder($appName).$packageName))
                    || is_file(CommonUlti::locationStandardize(Mirana_Folder::getPackageFolder($appName).$packageName))
                )
            ){
                if (!isset(self::$ws_map[$appName])) self::$ws_map[$appName]=[];
                if (!isset(self::$ws_map[$appName][$ws_name])){
                    self::$ws_map[$appName][$ws_name] = $packageName;
                } else {
                    throw new Exception("Webservice is already defined", 1);
                }
            }
            return NULL;
        }

        public static function loadWs($ws_name){
            $appName = Mirana_Routing::getApp();
            if (isset(self::$ws_map[$appName][$ws_name])){
                require_once self::$ws_map[$appName][$ws_name];
            } else {
                ;
            }
            return NULL;
        }
    }
?>
