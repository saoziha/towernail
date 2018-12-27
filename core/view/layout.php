<?php
    class Mirana_Layout extends Mirana_Component{
        private $layout, $page;
        private $page_content;
        private $needle_page, $needle_css, $needle_js;

        public function __construct($inpTheme = NULL, $inpLayout = NULL, $inpPage = NULL){
            parent::__construct($inpTheme, $inpLayout);
            if ($inpLayout!==NULL) self::setLayout($inpLayout);
            if ($inpPage!==NULL) self::setPage($inpPage);
            $this->needle_page = "<miranapage>".Mirana_Security::randString(16)."</miranapage>";
            $this->needle_css = "<miranacss>".Mirana_Security::randString(16)."</miranacss>";
            $this->needle_js = "<miranajs>".Mirana_Security::randString(16)."</miranajs>";
        }

        ####################################################################
        public function page(){
            echo $this->needle_page;
            return NULL;
        }

        public function css(){
            echo $this->needle_css;
            return NULL;
        }

        public function js(){
            echo $this->needle_js;
            return NULL;
        }
        ########################################################################
        public function setLayout($inpLayout){
            if (is_string($inpLayout)){
                $this->layout = $inpLayout;
            } else {
                $errMessage = "Invalid layout name in setLayout function";
                throw new Exception("<p>$errMessage</p>", 1);
            }
        }

        public function setPage($inpPage){
            if (is_string($inpPage)){
                $this->page = $inpPage;
            } else {
                $errMessage = "Invalid page name in setLayout function";
                throw new Exception("<p>$errMessage</p>", 1);
            }
        }
        ########################################################################
        public function render(){
            $this->renderLayout();
            $this->renderPage();
            $this->renderCss();
            $this->renderJs();

            echo $this->content;
            return null;
        }

        ########################################################################
        public function renderLayout(){
            $appName = self::$appName;
            $theme = self::$theme;
            #common files
            $this->cssList = Mirana_Loader::loadClient(
                Mirana_Folder::getViewCommonFolder($theme, $appName), ["css"]
            );
            $this->jsList = Mirana_Loader::loadClient(
                Mirana_Folder::getViewCommonFolder($theme, $appName), ["js"]
            );
            #layout files
            $location = Mirana_Folder::getLayoutFolder($theme, $appName).$this->layout; //get full url
            $r = $this->getRenderLocation($location);

            $location = $r["location"];
            $filePath = $r["filePath"];
            //at this stage filePath is ready
            $this->loadContent($filePath, $location);

            return null;
        }

        public function renderPage(){
            $page = new Mirana_Page($this->page);
            $page->loadPage();

            $this->page_content = $page->getContent();
            $this->cssList = array_merge($this->cssList, $page->getCss());
            $this->jsList = array_merge($this->jsList, $page->getJs());
            //directly change $this->content
            $this->content = str_replace($this->needle_page, $this->page_content, $this->content);
            return NULL;
        }

        public function renderCss(){
            //directly change $this->template
            $cssContent = "";
            if (is_array($this->cssList)){
                foreach ($this->cssList as $e) {
                    $cssContent .= "<style>\n$e\n</style>\n";
                }
                $this->content = str_replace($this->needle_css, $cssContent, $this->content);
            } else {
                ;
            }
            return NULL;
        }

        public function renderJs(){
            $jsContent = "";
            if (is_array($this->jsList)){
                foreach ($this->jsList as $e) {
                    $jsContent .= "<script type=\"text/javascript\">\n$e\n</script>\n";
                }
                $this->content = str_replace($this->needle_js, $jsContent, $this->content);
            } else {
                ;
            }
            return NULL;
        }
        ########################################################################
        private function getBackbone(){
            // not yet use
            header("Content-Type: text/html; charset=utf-8"); //TODO: allow header configuration

            $html = '
                <!DOCTYPE html>
                <html lang="en">

                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN">
                <html>
                </html>'
            ;
            //TODO: allow config doctype

            //css at the end of header, js at the end of body
        }

    }
?>
