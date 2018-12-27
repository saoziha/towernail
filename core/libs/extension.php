<?php
    /*--
    COMPANY: ezdev (former: the3ds)
    AUTHOR: Tom DINH
    PROJECT: mirana core v2
    DESCRIPTION: Domain register class
    --*/

    class Mirana_Extension {
        #property declaration
        protected static $defined;
        protected static $listFile;

        public function __construct(){
			$defined = []; //TODO: save this to somewhere
            $listFile = [];
            return NULL;
		}

        public static function add($extensionName){
            if (is_string($extensionName)){
                $extensionName = strtolower(trim($extensionName));
                $location   = CommonUlti::locationStandardize(EXT_FOLDER.$extensionName);
                $fileReg    = CommonUlti::locationStandardize($location.DS.EXT_FILE_REGISTER);

                if (is_dir($location) && is_file($fileReg)){
                    require_once $fileReg;
                    $defined[] = $extensionName;
                    $extList = ["css", "js"];
                    foreach ($extList as $e) $listFile[$extensionName][$e] = [];
                } else {
                    ;
                }
            } else {
                throw new Exception("Extension name in Mirana Register must be string.", 1);
            }
            return NULL;
        }

        private static function registerExt($fileExt, $extensionName, $location){
            //recursive through any level for folder
            if (is_string($fileExt) && is_string($extensionName) && is_string($location)){
                $extensionName = strtolower(trim(CommonUlti::locationStandardize($extensionName)));
                $location = CommonUlti::locationStandardize(EXT_FOLDER.$extensionName.DS.$location);

                if (is_file($location)){
                    $ext = strtolower(trim(pathinfo($location, PATHINFO_EXTENSION)));
                    if ($ext === $fileExt){
                        self::$listFile[$extensionName][$fileExt][] = $location;
                    } else {
                        ;
                    }
                } else if (is_dir($location)) {
                    $list = Mirana_Loader::listFile($location, [$fileExt], true);
                    foreach ($list as $e) if (is_file($e)) {
                        self::$listFile[$extensionName][$fileExt][] =  $e;
                    }
                } else {
                    ;
                }
            } else {
                throw new Exception("Extension's name and location must be string", 1);
            }
            return NULL;
        }

        public static function registerCss($extensionName, $location){
            self::registerExt("css", $extensionName, $location);
            return NULL;
        }

        public static function registerJs($extensionName, $location){
            self::registerExt("js", $extensionName, $location);
            return NULL;
        }

        private static function getExtension($extFile, $extensionName){
            $message = NULL;
            if (is_string($extensionName)){
                $extensionName = trim($extensionName);
                if (isset(self::$listFile[$extensionName])){
                    $extList = ["css", "js"];
                    foreach($extList as $ext){
                        if ($ext === $extFile){
                            if (isset(self::$listFile[$extensionName][$ext])
                                && is_array(self::$listFile[$extensionName][$ext]))
                            {
                                foreach(self::$listFile[$extensionName][$ext] as $e){
                                    $e = CommonUlti::urlStandardize($e); //relative path
                                    if (is_file($e)){
                                        switch ($extFile){
                                            case "css":
                                                // $fileContent = file_get_contents($e);
                                                // echo "<style>\n$fileContent\n</style>\n";
                                                $e = str_replace(EXT_FOLDER, EXT_FRONTEND_PATH.FS, $e);
                                                echo "<link rel=\"stylesheet\" href=\"$e\" />\n";
                                                break;
                                            case "js":
                                                // $fileContent = file_get_contents($e);
                                                // echo "<script type=\"text/javascript\">\n$fileContent\n</script>\n";
                                                $e = str_replace(EXT_FOLDER, EXT_FRONTEND_PATH.FS, $e);
                                                echo "<script src=\"$e\"></script>\n";
                                                break;
                                            default:
                                                break;
                                        }
                                    } else {
                                        echo "Extension is not found.";
                                    }
                                }
                            }  else {
                                echo "Extension is not found.";
                            }
                        } else {
                            ;
                        }
                    }
                } else {
                    echo "Extension is not found.";
                }
            } else {
                throw new Exception("Extension name in Mirana Register must be string.", 1);
            }
            return $message;
        }

        public static function getCss($extensionName){
            self::getExtension("css", $extensionName);
            return NULL;
        }

        public static function getJs($extensionName){
            self::getExtension("js", $extensionName);
            return NULL;
        }
    }
?>
