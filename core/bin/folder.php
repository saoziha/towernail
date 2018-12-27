<?php
    /*--
    COMPANY: ezdev (former: the3ds)
    AUTHOR: Tom DINH
    PROJECT: mirana core v2
    DESCRIPTION: Domain register class
    --*/

    class Mirana_Folder {
        #property declaration

        #######################################################################
        ### APPLICATION ########
        #######################################################################
        public static function getAppFolder($appName = NULL){
            //this follows Folder naming standard: clean start, ending with DS.
            if ($appName === NULL) {
                $appName = Mirana_Routing::getApp();
            }

            if (is_string($appName)){
                $appName = CommonUlti::locationStandardize($appName);
                return APP_FOLDER.$appName.DS;
            } else {
                throw new Exception("Application name must be string", 1);
            }
        }

        public static function getBinFolder($appName = NULL){
            if ($appName === NULL) {
                $appName = Mirana_Routing::getApp();
            }

            if (is_string($appName)){
                $appName = trim(trim(trim($appName, "\\"), "/"));
                //already have DS at the end of constant
                return APP_FOLDER.$appName.DS.APP_BIN;
            } else {
                throw new Exception("Application name must be string", 1);
            }
        }

        public static function getPublicFolder($appName = NULL){
            if ($appName === NULL) {
                $appName = Mirana_Routing::getApp();
            }

            if (is_string($appName)){
                $appName = trim(trim(trim($appName, "\\"), "/"));
                //already have DS at the end of constant
                return APP_FOLDER.$appName.DS.APP_PUB_FOLDER;
            } else {
                throw new Exception("Application name must be string", 1);
            }
        }

        #######################################################################
        ### FRONTEND ########
        #######################################################################

        public static function getThemeFolder($themeName, $appName = NULL){
            //this follows Folder naming standard: clean start, ending with DS.
            if ($appName === NULL) {
                $appName = Mirana_Routing::getApp();
            }

            if (is_string($appName) && is_string($themeName)){
                $appName = CommonUlti::locationStandardize($appName);
                $themeName = CommonUlti::locationStandardize($themeName);
                return CommonUlti::locationStandardize(
                    APP_FOLDER.$appName.DS.APP_VIEW_FOLDER.$themeName
                ).DS;
            } else {
                if (!is_string($appName)){
                    throw new Exception("Application name must be string", 1);
                } else if (!is_string($themeName)) {
                    throw new Exception("Theme name must be string", 1);
                } else {
                    ;
                }
            }
        }

        public static function getViewCommonFolder($themeName, $appName = NULL){
            if ($appName === NULL) {
                $appName = Mirana_Routing::getApp();
            }

            if (is_string($appName)){
                $appName = CommonUlti::locationStandardize($appName);
                if (is_string($themeName)){
                    //already have DS at the end of constant
                    return self::getThemeFolder($themeName, $appName).THEME_COMMON_FOLDER;
                } else {
                    throw new Exception("Theme name must be string", 1);
                }
            } else {
                throw new Exception("Application name must be string", 1);
            }
        }

        public static function getLayoutFolder($themeName, $appName = NULL){
            if ($appName === NULL) {
                $appName = Mirana_Routing::getApp();
            }

            if (is_string($appName)){
                $appName = CommonUlti::locationStandardize($appName);
                if (is_string($themeName)){
                    //already have DS at the end of constant
                    return self::getThemeFolder($themeName, $appName).THEME_LAYOUT_FOLDER;
                } else {
                    throw new Exception("Theme name must be string", 1);
                }
            } else {
                throw new Exception("Application name must be string", 1);
            }
        }

        public static function getPageFolder($themeName, $appName = NULL){
            if ($appName === NULL) {
                $appName = Mirana_Routing::getApp();
            }

            if (is_string($appName)){
                $appName = CommonUlti::locationStandardize($appName);
                if (is_string($themeName)){
                    //already have DS at the end of constant
                    return self::getThemeFolder($themeName, $appName).THEME_PAGE_FOLDER;
                } else {
                    throw new Exception("Theme name must be string", 1);
                }
            } else {
                throw new Exception("Application name must be string", 1);
            }
        }

        public static function getCompFolder($themeName, $appName = NULL){
            if ($appName === NULL) {
                $appName = Mirana_Routing::getApp();
            }

            if (is_string($appName)){
                $appName = CommonUlti::locationStandardize($appName);
                if (is_string($themeName)){
                    //already have DS at the end of constant
                    return self::getThemeFolder($themeName, $appName).THEME_COM_FOLDER;
                } else {
                    throw new Exception("Theme name must be string", 1);
                }
            } else {
                throw new Exception("Application name must be string", 1);
            }
        }

        #######################################################################
        ### BACKEND ########
        #######################################################################

        public static function getPackageFolder($appName = NULL){
            if ($appName === NULL) {
                $appName = Mirana_Routing::getApp();
            }

            if (is_string($appName)){
                $appName = trim(trim(trim($appName, "\\"), "/"));
                //already have DS at the end of constant
                return APP_FOLDER.$appName.DS.APP_API_FOLDER;
            } else {
                throw new Exception("Application name must be string", 1);
            }
        }

        /*
        public static function getExtFolder($extname)

        public static function getExtPublicFolder($extname)
        */
    }
?>
