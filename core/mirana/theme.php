<?php
    /*--
    COMPANY: ezdev (former: the3ds)
    AUTHOR: Tom DINH
    PROJECT: mirana core v2
    DESCRIPTION: Routing management class
    --*/

    class Mirana_Theme {
        #property declaration
        protected static $definedTheme = [];
        protected static $definedPage = [];
        protected static $definedFunctionFolder = [];
        protected static $savedRegisteredAppName = NULL;
        ##routing static vars
        protected static $theme = NULL;
        protected static $pagePackage = NULL;

        public function __construct(){
			self::$definedTheme = []; //TODO: save this to somewhere
            self::$definedPage = []; //TODO: save this to somewhere
            self::$definedFunctionFolder = []; //TODO: save this to somewhere
            self::$savedRegisteredAppName = NULL;
            #################################
            self::$theme = NULL;
            self::$queryString = NULL;
            return NULL;
		}
        ########################################################################
        ################################### API ################################
        ########################################################################
        public static function getTheme(){
            return isset(self::$theme)?self::$theme:NULL;
        }

        public static function getThemeName(){
            return isset(self::$theme->themeName)?self::$theme->themeName:NULL;
        }

        public static function getPagePackage(){
            return isset(self::$pagePackage)?self::$pagePackage:NULL;
        }

        public static function themeUrlStandardize($url){
            return FS.CommonUlti::urlStandardize($url);
        }
        ########################################################################
        ########################SHORT HAND ROUTING##############################
        public static function registerTheme($appName, $definedFunction){
            self::$savedRegisteredAppName = strtolower(trim($appName));

            if (is_callable($definedFunction)){
                $definedFunction();
            } else {
                $errMessage = "Theme register-function is not defined, for application: $appName. "
                . "Please check ".THEME_REGISTERFILE." file.";
                throw new Exception("<p>$errMessage</p>", 1);
            }

            self::$savedRegisteredAppName = NULL;

            return NULL;
        }

        public static function registerPage($appName, $definedFunction){
            self::$savedRegisteredAppName = strtolower(trim($appName));

            if (is_callable($definedFunction)){
                $definedFunction();
            } else {
                $errMessage = "Routing function is not defined, for application: $appName. "
                . "Please check ".THEME_ROUTEFILE." file.";
                throw new Exception("<p>$errMessage</p>", 1);
            }

            self::$savedRegisteredAppName = NULL;

            return NULL;
        }
        ########################################################################
        ########################################################################
        public static function addTheme($url, $themeName, $themeType = "normal"){
            $appName = self::$savedRegisteredAppName;

            $url = self::themeUrlStandardize($url);
            $themeName = strtolower(trim($themeName));

            //allocation memory for $appName
            if (!isset(self::$definedTheme[$appName])){
                self::$definedTheme[$appName] = [];
            }

            //add url to theme
            if (isset(self::$definedTheme[$appName][$url])){
                $errMessage = "Duplicated theme url: $url , for application: $appName. "
                . "Please check ".THEME_REGISTERFILE." file.";
                throw new Exception("<p>$errMessage</p>", 1);
            } else {
                $t = new stdClass();
                $t->themeName = $themeName;
                if (strtolower(trim($themeType)) === "angularjs"){
                    $t->themeType = "angularjs";
                } else {
                    $t->themeType = "normal";
                }

                self::$definedTheme[$appName][$url] = json_encode($t);

                //clear memory for page routing
                if (isset(self::$definedPage[$appName])){
                    unset(self::$definedPage[$appName]);
                } else {
                    ;
                }
            }

            return NULL;
        }

        public static function addFunctionFolder($folder){
            $appName = self::$savedRegisteredAppName;

            $folder = strtolower(trim(CommonUlti::locationStandardize($folder)));

            //allocation memory for $appName
            if (!isset(self::$definedFunctionFolder[$appName])){
                self::$definedFunctionFolder[$appName] = [];
            }

            //add url to theme
            if (!in_array($folder, (self::$definedFunctionFolder[$appName]))){
                self::$definedFunctionFolder[$appName][] = $folder;
            } else {
                ;
            }

            return NULL;
        }
        ########################################################################
        ########################################################################
        //static pages
        public static function addPage($url, $layout, $page){
            $appName    = self::$savedRegisteredAppName;

            $url = self::themeUrlStandardize($url);
            $layout = strtolower(trim($layout));
            $page = strtolower(trim($page));

            //allocation memory for $appName
            if (!isset(self::$definedPage[$appName])){
                self::$definedPage[$appName] = [];
            }
            //add url to page (contains layout and page)
            if (isset(self::$definedPage[$appName][$url])){
                $errMessage = "Duplicated page url: $url , for application: $appName. "
                            . "Please check ".APP_ROUTEFILE." file.";
                throw new Exception("<p>$errMessage</p>", 1);
            } else {
                $t = new stdClass();
                $t->layout = $layout;
                $t->page = $page;
                self::$definedPage[$appName][$url] = json_encode($t);
            }

            return NULL;
        }

        //dynamic pages
        public static function addUrl(){
            //TODO: allow call function once the routing meets
            ;
        }
        ########################################################################
        ########################################################################
        public static function findTheme($appName, $url){
            $themeDefinedUrl = NULL;
            //find theme
            $maxLen = -1;
            foreach (self::$definedTheme[$appName] as $u => $v){
                if (strpos($url, $u) === 0 && strlen($u) > $maxLen){
                    $themeDefinedUrl = CommonUlti::urlStandardize($u);
                    self::$theme = json_decode($v);
                    $maxLen = strlen($u);
                } else {
                    continue;
                }
            }

            return $themeDefinedUrl;
        }


        ########################################################################
        ################### LOAD REQUIRED FILES AND ULTIS ######################
        ########################################################################
        public static function loadThemeRegister($appName){
            $retVal = true;
            //check folder exists
            if (is_string($appName)){
                $appFolder = Mirana_Folder::getAppFolder($appName);
                if (is_dir($appFolder)){
                    //calculate theme register file
                    $themeRegFile = $appFolder.THEME_REGISTERFILE;

                    if (is_file($themeRegFile)) {
                        require_once $themeRegFile;
                    } else {
                        $retVal = false;
                    }
                } else {
                    $errMessage = "loadThemeRegister function in loader: file $themeRegFile does not exist";
                    throw new Exception("<p>$errMessage</p>", 1);
                }
            } else {
                $errMessage = "loadThemeRegister function in loader: \$appName must be string";
                throw new Exception("<p>$errMessage</p>", 1);
            }
            return $retVal;
        }

        public static function loadRoutingRegister($appName, $themeName){
            $retVal = true;
            $appName = trim($appName);
            //check folder exists
            if (is_string($appName)){
                $appFolder = Mirana_Folder::getAppFolder($appName);
                if (is_dir($appFolder)){
                    if (is_string($themeName)){
                        //calculate routing register file
                        $routingRegFile = CommonUlti::locationStandardize($appFolder.APP_VIEW_FOLDER.$themeName)
                                            .DS.THEME_ROUTEFILE;

                        if (is_file($routingRegFile)){
                            //load all the routing
                            require_once $routingRegFile;
                        } else{
                            $errMessage = "loadRoutingRegister function in loader: file $routingRegFile does not exist";
                            throw new Exception("<p>$errMessage</p>", 1);
                        }
                    } else {
                        $errMessage = "loadRoutingRegister function in loader: \$themeName must be string";
                        throw new Exception("<p>$errMessage</p>", 1);
                    }
                } else {
                    $errMessage = "loadRoutingRegister function in loader: application $appName does not exist";
                    throw new Exception("<p>$errMessage</p>", 1);
                }
            } else {
                $errMessage = "loadRoutingRegister function in loader: \$appName must be string";
                throw new Exception("<p>$errMessage</p>", 1);
            }
            return $retVal;
        }
        ########################################################################
        ########################################################################
        ########################################################################

        public static function themePassthru($appName, $theme, $url){
            $retVal = $url; //saved to return

            if (strlen($url) === 1){
                $url = "index.html";
            }
            //calculate file path
            $path = APP_FOLDER.$appName.DS.APP_VIEW_FOLDER.$theme->themeName.DS.$url;

            if (is_file($path)){
                $fp = fopen($path, 'rb');
                fpassthru($fp);
            } else {
                $errMessage = "File not found, please check this file again: $url.";
                throw new Exception("<p>$errMessage</p>", 404);
            }

            return $retVal;
        }

        private static function loadThemeFunction($appName, $themeName){
            if (isset(self::$definedFunctionFolder[$appName])){
                foreach (self::$definedFunctionFolder[$appName] as $u){
                    if (is_string($u) && is_string($themeName)){
                        $dirPath = APP_FOLDER.$appName.DS.APP_VIEW_FOLDER
                                .$themeName.DS.$u;
                        if (is_dir($dirPath)){
                            Mirana_Loader::loadFolder($dirPath);
                        }
                    } else {
                        ;
                    }
                }
            }
        }

        public static function themeStarfall($appName, $theme, $url){
            $retVal = NULL;
            //load the page register of theme
            self::loadRoutingRegister($appName, $theme->themeName);
            //load theme function folder
            self::loadThemeFunction($appName, $theme->themeName);
            //load theme
            if (isset(self::$definedPage[$appName])){
                //find page url
                $maxLen = -1;
                $pageDefinedUrl = $pagePackage = NULL;
                foreach (self::$definedPage[$appName] as $u => $v){
                    if (strpos($url, $u) === 0 && strlen($u) > $maxLen){
                        $pageDefinedUrl = CommonUlti::urlStandardize($u);
                        $pagePackage = json_decode($v);
                        $maxLen = strlen($u);
                    } else {
                        continue;
                    }
                }
                //finalize
                if ($pagePackage !== NULL){
                    self::$pagePackage = $pagePackage;
                    $retVal = $pageDefinedUrl;
                } else {
                    $errMessage = "Oops, look like you didn't declare routing for: $url";
                    throw new Exception("<p>$errMessage</p>", 404);
                }
            } else {
                $errMessage = "Routing register for application: $appName  not found.";
                throw new Exception("<p>$errMessage</p>", 403);
            }

            return $retVal;
        }

        public static function renderLayout($theme, $pagePackage, $definedFunction = NULL){
            if ($definedFunction !== NULL){
                if (is_callable($definedFunction)){
                    $definedFunction();
                } else {
                    $errMessage = "The variable passed for rendering layout is not a function.";
                    throw new Exception("<p>$errMessage</p>", 1);
                }
            }

            if (is_object($theme) && is_object($pagePackage)){
                $themeName = $theme->themeName;
                $layout = $pagePackage->layout;
                $page = $pagePackage->page;

                $render = new Mirana_Layout($themeName, $layout, $page);
            } else {
                $errMessage = "The theme passed for rendering layout is not an object.";
                throw new Exception("<p>$errMessage</p>", 1);
            }

            $render->render();
            return NULL;
        }
        ########################################################################
    }
?>
