<?php
    class Mirana_Loader{
        public function __construct(){
            ;
        }

        public static function listFile( $path = "", $limitExt = [], $recursive = false ){
            //identical with commom_ulti function
			$pageList = [];
            $path = trim(trim($path,'/'),'\\');
			if ( is_dir($path) ){
				$allItem = scandir( $path );

				foreach ($allItem as $element){
					$e = $path.DS.$element;
					if ($element !== "." && $element !== ".."){
						if ( is_file($e) ){
							if (is_array($limitExt)){
								if (count($limitExt) <= 0){
									$pageList[] = $path.DS.$element;
								} else {
									foreach ($limitExt as $extension){
										if (is_string($extension)){
											$extension = strtolower(trim(strval($extension)));
											$fileExt = strtolower(trim(pathinfo($e, PATHINFO_EXTENSION)));
											if (  $extension === $fileExt ){
												$pageList[] = $path.DS.$element;
												break;
											} else {
												;
											}
										} else {
											throw new Exception("Extension list must be array of string(s)", 1);
										}
									}
								}
							} else {
								throw new Exception("Extension list must be array", 1);
							}
						} else if (is_dir($e)){
							if ($recursive === true){
								$t = self::listFile($e, $limitExt, true);
								foreach ($t as $r) $pageList[] = $r;
							} else if (!is_bool($recursive)){
								throw new Exception("Recursive flag must be boolean", 1);
							} else {
								;
							}
						} else {
							;
						}
					}
				}
			}
			return $pageList;
        }

        public static function loadFolder( $path = "", $limitExt = "php", $recursive = false ){
            if (is_string($limitExt)){
                $list = self::listFile($path, [$limitExt], $recursive);
            } else if (is_array($limitExt)){
                $list = self::listFile($path, $limitExt, $recursive);
            }

            foreach ($list as $e) if (is_file($e)) {
                require_once $e;
            }

            return NULL;
        }
        ########################################################################
        ########################################################################
        ########################################################################
        public static function loadMirana(){
            $folderList = [
                "core/model/db",
                "core/libs",
                "core/security",
                "core/controller",
                "core/model/querybuilder",
                "core/view",
                "core/mirana",
                "cms",
                "reg"
            ];

            foreach ($folderList as $e){
                $fileList =self::listFile($e,["php"],true);
                foreach ($fileList as $f){
                    if (is_file($f)) require_once($f);
                }
            }

            return null;
        }
        ########################################################################
        ########################################################################
        ########################################################################
        public static function loadClient($location, $fileExtLimit = ["css", "js"]){
            $fileContentList = [];
            if (is_string($location)){
                $location = trim($location);
                if ( is_dir($location) ){
                    $list = self::listFile($location, $fileExtLimit, false);

                    foreach ($list as $e) if (is_file($e)) {
                        //read file content into array
                        $fileContent = file_get_contents($e);
                        $fileContentList[] = $fileContent;
                    }
                } else if (is_file($location)){
                    $e = $location;
                    $fileExt = strtolower(trim(pathinfo($e, PATHINFO_EXTENSION)));
                    if (in_array($fileExt, $fileExtLimit)){
                        $fileContent = file_get_contents($e);
                        $fileContentList[] = $fileContent;
                    }
                } else {
                    ;
                }
            } else {
                throw new Exception("Location must be string", 1);
            }
            return $fileContentList;
        }
        ########################################################################
        public static function loadPackage($appName, $packageName, $recursive = true){
            if (is_string($packageName)){
                $packageName = CommonUlti::locationStandardize(
                    Mirana_Folder::getPackageFolder($appName).trim($packageName)
                );

                if (is_dir($packageName) && is_bool($recursive)){
                    //load folder
                    $list = self::listFile($packageName,["php"], $recursive);
                    foreach ($list as $e) if (is_file($e)) {
                        require_once $e;
                    }
                } else if (is_file($packageName)){
                    //load single file
                    require_once $packageName;
                } else {
                    ;
                }
            } else {
                throw new Exception("Package name must be string", 1);
            }
            return null;
        }
    }
?>
