<?php
    /*--
    COMPANY: ezdev (former: the3ds)
    AUTHOR: Tom DINH
    PROJECT: mirana core v2
    DESCRIPTION: Routing management class
    --*/

    class Mirana_Routing {
        #property declaration
        protected static $definedWebService = [];
        ##routing static vars
        protected static $appName = NULL;
        protected static $queryString = NULL;

        public function __construct(){
			self::$definedWebService = []; //TODO: save this to somewhere
            #################################
            self::$appName = NULL;
            self::$queryString = NULL;
            return NULL;
		}
        ########################################################################
        ########################################################################
        #########################ROUTE CLIENT VIEW##############################
        private static function routeToApp($url){
            //if there is only app name but not having trailing slash
            $mirana_selector = explode(FS, $url);
            if (count($mirana_selector)===1 && strlen($mirana_selector[0]) > 0
                && !self::checkTrailingSlash()
                && Mirana_Domain::getDomainApp() === NULL
            ){
                // die(trim(trim($url),FS).FS);
                header("Location: ".trim(trim($url),FS).FS);
            }
            // 1. set value to self::$appName
            // 2. return string which is querystring after cut-off application
            $retVal = "";
            if (self::$appName === NULL){
                $domainApp = Mirana_Domain::getDomainApp();
                if ($domainApp !== NULL){
                    self::$appName = $domainApp;
                    $retVal = CommonUlti::urlStandardize($url);
                } else {
                    $urlElement = self::getUrlSeparatedValues($url);

                    if (count($urlElement)>0 && strlen(trim($urlElement[0]))>0){
                        $appName = trim($urlElement[0]);
                        self::$appName = $appName;

                        //remove first occurence
                        array_shift($urlElement); //remove first element
                        $retVal = CommonUlti::urlStandardize(implode(FS, $urlElement));
                    } else {
                        ;
                    }
                }
            } else {
                ;
            }

            //load app before exit
            self::loadApp(self::$appName);
            //load theme-file before exit
            Mirana_Theme::loadThemeRegister(self::$appName);

            return $retVal;
        }

        private static function routeToTheme($url){
            // 1. set value to self::$theme
            // 2. return string which is querystring after cut-off application amd theme
            $retVal = "";
            $appName = self::getApp();

            $themeDefinedUrl = Mirana_Theme::findTheme($appName, $url);
            $theme = Mirana_Theme::getTheme();

            if (is_string($themeDefinedUrl) && is_object($theme)){
                $themeDefinedUrlLength = intval(strlen($themeDefinedUrl));
                //add trailing slash if not exist and route to null string
                if ($themeDefinedUrlLength + 1 === strlen($url) && $themeDefinedUrlLength>0 && !self::checkTrailingSlash()){
                    header("Location: ".trim(trim($url),FS).FS);
                }

                //remove first occurence
                //extra 1 character for the beginning slash
                $retVal = substr_replace($url, "", 0, $themeDefinedUrlLength + 1);
                $retVal = CommonUlti::urlStandardize($retVal);
            } else {
                $errMessage = "In application: $appName, there is no active theme match the url: $url.";
                throw new Exception("<p>$errMessage</p>", 1);
            }

            return $retVal;
        }

        private static function routeToPage($url){
            // load the static content
            $appName = self::getApp();
            $theme = Mirana_Theme::getTheme();
            $url = Mirana_Theme::themeUrlStandardize($url);

            //load theme
            if ($theme->themeType === "angularjs"){
                #for angularjs, pass through the theme-folder
                $queryString = Mirana_Theme::themePassthru($appName, $theme, $url);
            } else {
                #load starfall theme
                $pageDefinedUrl = Mirana_Theme::themeStarfall($appName, $theme, $url);
                //calculate $queryString
                $pageDefinedUrlLength = intval(strlen($pageDefinedUrl));
                //remove first occurence
                $queryString = substr_replace($url, "", 0, $pageDefinedUrlLength + 1);
                //render page
                $pagePackage = Mirana_Theme::getPagePackage();
                Mirana_Theme::renderLayout($theme, $pagePackage);
            }
            //set query string
            self::$queryString = CommonUlti::urlStandardize($queryString);

            return NULL;
        }
        ########################################################################
        ########################################################################

        public static function route(){
            $url = self::getUrlPlain();
            $url = self::routeToApp($url);
            $url = Mirana_Theme::themeUrlStandardize($url);
            $url = self::routeToTheme($url);
            self::$queryString = $url;

            $mirana_selector = self::getMiranaSelector();
            switch ( strtolower($mirana_selector) ){
                case APP_WEBSERVICE:{
                    //NOT compatible with 1.6
                    //format: appname/webservice/...
                    //or only: webservice/... in case domain sticks with app
                    self::handleWebService();
                    break;
                }
                case APP_FRONTEND_PUBLIC:{
                    //format: appname/public/...
                    self::handlePublic();
                    break;
                }
                case EXT_FRONTEND_PATH:{
                    //format: appname/extension/...
                    self::handleExtension();
                    break;
                }
                default: {
                    self::handleApplication();
                    break;
                }
            }

            return NULL;
        }

        public static function routeServer(){

        }

        ########################################################################
        ################### LOAD REQUIRED FILES AND ULTIS ######################
        ########################################################################
        public static function loadApp($appName){
            $retVal = true;
            //check folder exists
            if (is_string($appName)){
                $appFolder = Mirana_Folder::getAppFolder($appName);
                if (is_dir($appFolder)){
                    //list all required files in BIN folder
                    $requiredList = [ APP_DBFILE, APP_PKGFILE];

                    foreach($requiredList as $file){
                        $fileName = $appFolder.DS.$file;
                        if (is_file($fileName)) {
                            require_once $fileName;
                        } else {
                            ;
                        }
                    }
                } else {
                    $errMessage = "The application: <strong>$appName</strong> is not available";
                    throw new Exception("<p>$errMessage</p>", 1);
                }
            } else {
                $errMessage = "loadApp function in routing: \$appName must be string";
                throw new Exception("<p>$errMessage</p>", 1);
            }
            return $retVal;
        }

        ########################################################################
        ################################### API ################################
        public static function getApp(){
            return self::$appName;
        }

        public static function getMiranaQueryString(){
            return self::$queryString;
        }

        public static function getUrlPlain(){
            $mirana_query_string = isset($_GET[GET_REQUEST])?trim(strval($_GET[GET_REQUEST])):"";
            return $mirana_query_string;
        }

        private static function getUrlSeparatedValues (){
            $getVal = isset($_GET[GET_REQUEST])?strval($_GET[GET_REQUEST]):"";
            $getVal = explode(FS, CommonUlti::urlStandardize($getVal));

            if (sizeof($getVal)<1) $getVal[] = "";
            return $getVal;
        }

        private static function checkTrailingSlash(){
            $url = self::getUrlPlain();
            return substr(trim($url),-1) === FS;
        }

        public static function getMiranaSelector(){
            $t = explode(FS, self::getMiranaQueryString());
            $mirana_selector = isset($t[0])?$t[0]:NULL;
            return $mirana_selector;
        }

        public static function parseGetRequest(){
            $retVal = $_GET;
            if (isset($retVal[GET_REQUEST])) unset($retVal[GET_REQUEST]);
            return $retVal;
        }

        ########################################################################
        ########################################################################

        private static function handleApplication(){
            $mirana_query_string = self::getMiranaQueryString();

            if ($securityApp=true){ //TODO: write security
                ob_start(); //buffer every output from now on
                ##############################################
                ##### start execution part of application ####
                if (EXEC_TIME) {
                    $time_start = microtime(true);
                    //bench execution time
                }

                //do client routing
                self::routeToPage($mirana_query_string);

                if (EXEC_TIME){
                    $time_end = microtime(true);
                    $time = round($time_end - $time_start,5);
                    echo "<p> $time μs </p>";
                }
                ##############################################
                ##### end execution part of application ####
                ##############################################
                ##############################################
                $view_render = ob_get_clean(); //get the content

                //set header for rendering page
                header('Vary: User-Agent');
                header('Vary: Accept-Encoding');
        		//get a unique hash of this file (etag)
        		$etagFile = md5($view_render);
        		//set etag-header
        		header("Etag: $etagFile");
        		//make sure caching is turned on
        		header('Cache-Control: private, max-age=1209600');
                //output $view_render to html request
                echo $view_render;
            } else {
                //echo "application permission denied";
            }
            die();
        }

        private static function handleWebService(){
            $mirana_query_string = self::getMiranaQueryString();
            try {
                header("Content-Type: text/plain; charset=utf-8");
                echo Mirana_Protocol::handleWebservice($mirana_query_string);
            } catch (Exception $e){
                header("Content-Type: text/html; charset=utf-8");
                echo $e->getMessage();
            }
            return NULL;
        }

        private static function handleExtension(){
            $mirana_query_string = self::getMiranaQueryString();
            $t = explode(FS, $mirana_query_string);
            array_splice($t,0,1);
            $mirana_query_string = trim(implode(DS, $t));
            //calculate file path
            $path = EXT_FOLDER.$mirana_query_string;

            if (is_file($path)){
                self::renderFileRequest($path);
            } else {
                ;
            }
            die();
        }

        private static function handlePublic(){
            $mirana_query_string = self::getMiranaQueryString();
            $app = CommonUlti::locationStandardize(self::getApp());
            $themeName = CommonUlti::locationStandardize(Mirana_Theme::getThemeName());

            if (is_string($app) && is_string($themeName)){
                $t = explode(FS, $mirana_query_string);
                array_splice($t,0,1);
                $mirana_query_string = trim(implode(DS, $t));
                //calculate file path
                $path = APP_FOLDER.$app.DS.APP_VIEW_FOLDER
                        .$themeName.DS.APP_PUB_FOLDER.$mirana_query_string;

                if (is_file($path)){
                    self::renderFileRequest($path);
                } else {
                    ;
                }
            }

            die();
        }

        ########################################################################
        ########################################################################
        ########################################################################

        private static function renderFileRequest($path){
            $fileExt = strtolower(trim(pathinfo($path, PATHINFO_EXTENSION)));
            $contentType = '';
            switch ($fileExt){
                case 'css': $contentType = 'text/css';
                break;
                case 'js': $contentType = 'text/javascript';
                break;
                default: $contentType = mime_content_type($path);
                break;
            }

            header('Content-Type: '.$contentType);
            header('Content-Length: '.filesize($path));
            header('Vary: User-Agent');
            header('Vary: Accept-Encoding');
            //header('Cache-Control: private, max-age=600');
            //header('Cache-Control: public, max-age=1209600');

            //get the last-modified-date of this very file
            $lastModified=filemtime($path);
            //get a unique hash of this file (etag)
            $etagFile = md5_file($path);
            //get the HTTP_IF_MODIFIED_SINCE header if set
            $ifModifiedSince=(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false);
            //get the HTTP_IF_NONE_MATCH header if set (etag: unique file hash)
            $etagHeader=(isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);

            //set last-modified header
            header("Last-Modified: ".gmdate("D, d M Y H:i:s", $lastModified)." GMT");
            //set etag-header
            header("Etag: $etagFile");
            //make sure caching is turned on
            header('Cache-Control: private, max-age=1209600');

            // echo file_get_contents($path);
            $fp = fopen($path, 'rb');
            fpassthru($fp);
        }
    }
?>
