<?php
    class Mirana_Page extends Mirana_Component{
        public function __construct($name=NULL){
            parent::__construct(self::$theme, $name);
        }

        /*
        public function loadPageContent_old(){
            $this->content = NULL;
            $appName = self::$appName; $theme = self::$theme;

            if ($this->pageName !== NULL){
                $cLocation =  Mirana_Folder::getPageFolder($theme, $appName).$this->pageName;

                //load everything in a (star) component
                $comp = new Mirana_Component($cLocation);
                $r = $comp->loadCompContent();

                $this->content = $comp->getCompContent();
                $this->cssList = $comp->getCompCss();
                $this->jsList = $comp->getCompJs();

                if ($r === NULL){
                    $errMessage = "<h3>Could not load page at \"$this->pageName\"</h3>
                    <p>It might be from your routing folder.</p>";
                    throw new Exception("<p>$errMessage</p>", 1);
                }
            } else {
                ;
            }
        }
        */

        public function loadPage(){
            $appName = self::$appName;
            $theme = self::$theme;

            $location = Mirana_Folder::getPageFolder($theme, $appName).$this->name; //get full url

            $r = $this->getRenderLocation($location);
            $location = $r["location"];
            $filePath = $r["filePath"];
            //at this stage filePath is ready
            $this->loadContent($filePath, $location);

            return null;
        }
    }
?>
