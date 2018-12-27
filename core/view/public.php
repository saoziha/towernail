<?php
    class Mirana_Public {
        public static function getCss($url){
            $fileContent = self::load($url, "css");
            echo "<style>\n$fileContent\n</style>\n";

            return null;
        }

        public static function getJs($url){
            $fileContent = self::load($url, "css");
            echo "<script type=\"text/javascript\">\n$fileContent\n</script>\n";

            return null;
        }

        private static function load($url, $ext){
            $retVal = NULL;

            $url = CommonUlti::locationStandardize($url);
            $app = CommonUlti::locationStandardize(Mirana_Routing::getApp());
            $themeName = CommonUlti::locationStandardize(Mirana_Theme::getThemeName());

            if (is_string($app) && is_string($themeName)){
                //calculate file path
                $path = APP_FOLDER.$app.DS.APP_VIEW_FOLDER
                        .$themeName.DS.APP_PUB_FOLDER.$url;

                if (is_file($path) && strtolower(trim(pathinfo($path, PATHINFO_EXTENSION)))===$ext){
                    $retVal = file_get_contents($path);
                } else {
                    ;
                }
            }

            return $retVal;
        }
    }
?>
