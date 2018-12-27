<?php
    abstract class Mirana_Component {
        protected static $theme, $appName;
        protected $name;
        protected $content;
        protected $cssList;
        protected $jsList;

        public function __construct($theme, $name=NULL){
            self::$appName = Mirana_Routing::getApp();

            self::setTheme($theme);
            if ($name!==NULL) self::setName($name);

            $this->content = NULL;
            $this->cssList = $this->jsList = [];
        }

        public static function setTheme($theme){
            if (is_string($theme)){
                self::$theme = $theme;
            } else {
                $errMessage = "Invalid theme in setTheme function";
                throw new Exception("<p>$errMessage</p>", 1);
            }
        }

        public function setName($name){
            if (is_string($name)){
                $this->name = $name;
            } else {
                $errMessage = "Invalid name in setName function";
                throw new Exception("<p>$errMessage</p>", 1);
            }
        }

        public static function getTheme(){
            return self::theme;
        }

        public function getName(){
            return $this->name;
        }

        public function getCss(){
            return is_array($this->cssList)?$this->cssList:[];
        }

        public function getJs(){
            return is_array($this->jsList)?$this->jsList:[];
        }

        public function getContent(){
            return $this->content;
        }

        public function renderScript($filePath){
            $retVal = NULL;

            $s = NULL;
            ob_start();
            require $filePath;
            $s = ob_get_clean();

            $retVal = html_entity_decode($s);

            return $retVal;
        }
        #######################################################################
        private function loadComponent( $urlLocation ){
            $content = NULL;
            $cssList = $jsList = [];
            if (is_string($urlLocation)){
                $urlLocation = CommonUlti::locationStandardize($urlLocation);

                $r = self::getRenderLocation($urlLocation);
                $location = $r["location"];
                $filePath = $r["filePath"];

                $content = $this->renderScript($filePath);
            } else {
                ;
            }

            if ($content !== NULL){
                if (STARFALL_RECURSIVE){
                    $comp = $this->renderStars($content);
                    $content  = $comp["content"];
                    $cssList  = is_array($comp["cssList"])?$comp["cssList"]:[];
                    $jsList   = is_array($comp["jsList"])?$comp["jsList"]:[];
                }

                $cssList    = array_merge($cssList, Mirana_Loader::loadClient($location, ["css"]));
                $jsList     = array_merge($jsList, Mirana_Loader::loadClient($location, ["js"]));

                return [
                    "content" => $content,
                    "cssList" => $cssList,
                    "jsList" => $jsList
                ];
            }

            return NULL;
        }

        private function renderStars($htmlContent){
            $cTag = "star";
            libxml_use_internal_errors(true); //surpress Warnings

            $appName = self::$appName;
            $theme = self::$theme;
            $content = NULL;
            $cssList = $jsList = [];

            $dom = new DOMDocument(PHPDOM_XML_VERSION, PHPDOM_XML_ENCODING);
            $dom->encoding = PHPDOM_XML_ENCODING;

            $xml_string = "<?xml encoding=\"".PHPDOM_XML_ENCODING."\" ?>";
            //append of $xml_string for correctly encoding
            $dom->loadHTML( $xml_string.$htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            // LIBXML_HTML_NOIMPLIED turns off the automatic adding of implied html/body elements
            // LIBXML_HTML_NODEFDTD prevents a default doctype being added when one is not found.

            if ($dom->getElementsByTagName($cTag)->length>0){
                while ($dom->getElementsByTagName($cTag)->length>0){
                    $cDom = $dom->getElementsByTagName($cTag)[0];

                    $cLocation = Mirana_Folder::getCompFolder($theme, $appName).$cDom->getAttribute("name");
                    $is_not_wrapped = strtolower(trim($cDom->getAttribute("wrap"))) === "false";

                    $comp = $this->loadComponent($cLocation); //load everything in a (star) component

                    $comp_html = $dom->createDocumentFragment(); // create fragment
                    $comp_html->strictErrorChecking = true;
                    $comp_html->validateOnParse = true;

                    if ($comp !== NULL){
                        //load js+css into array, prepare for page
                        $cssList    = array_merge($cssList, is_array($comp["cssList"])?$comp["cssList"]:[]);
                        $jsList     = array_merge($jsList, is_array($comp["jsList"])?$comp["jsList"]:[]);

                        $comp_html->appendXML($comp["content"]);
                    } else {
                        $errMessage = "Could not load Star at name =\"".$cDom->getAttribute("name")."\"";
                        $comp_html->appendXML($errMessage);
                    }

                    if ($is_not_wrapped){
                        //directly replace the loaded component
                        $cDom->parentNode->replaceChild($comp_html, $cDom);
                    } else {
                        //put a div and transfer properties of a star
                        $component = $dom->createElement("div");
                        foreach($cDom->attributes as $attr){
                            $component->setAttribute($attr->nodeName, $attr->nodeValue);
                        }
                        //append the component content into div and replace the star
                        $component->appendChild($comp_html);
                        $cDom->parentNode->replaceChild($component, $cDom);
                    }
                }

                $content = $dom->saveHTML();
                $content = str_replace($xml_string, "", $content); //remove the occurence of $xml_string
                libxml_use_internal_errors(false);
            } else {
                $content = $htmlContent;
            }

            return [
                "content" => $content,
                "cssList" => $cssList,
                "jsList" => $jsList
            ];
        }
        #######################################################################
        #libraries
        public static function getRenderLocation($location){
            $filePath = NULL;
            if (is_file($location)){
                //if location is already file, take this
                $filePath = $location;
                $location = CommonUlti::getNearestFolder($location);
            } else if (is_dir($location)){
                //else, find then php (priority) or html file with SAME NAME as the folder
                $folderName = CommonUlti::getNearestFolderName($location);
                $fileList = ["$folderName.php", "$folderName.html"];

                foreach ($fileList as $f){
                    $path = $location.DS.$f;

                    if (is_file($path)){
                        $filePath = $path;
                        break; //PHP priority
                    }
                }
            } else {
                ;
            }

            return ["filePath"=>$filePath, "location"=>$location];
        }

        public function loadContent($filePath, $location){
            if (isset($filePath) && is_string($filePath) && strlen($filePath)>0){
                //read content from php page
                $htmlContent = $this->renderScript($filePath);
                //load css, js
                $this->cssList = array_merge( $this->cssList, Mirana_Loader::loadClient($location, ["css"]) );
                $this->jsList = array_merge( $this->jsList, Mirana_Loader::loadClient($location, ["js"]) );

                $comp = self::renderStars($htmlContent);

                $this->content = $comp["content"];
                $this->cssList  = array_merge($this->cssList, $comp["cssList"]);
                $this->jsList   = array_merge($this->jsList, $comp["jsList"]);
            } else {
                $errMessage = "<h3>Could not load component content at \"$filePath\"</h3>
                    <p>It might be from your routing folder.</p>";
                throw new Exception("<p>$errMessage</p>", 1);
            }
        }
        #######################################################################
    }
?>
