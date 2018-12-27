<?php
    /*-----
    COMPANY: the3ds team
    AUTHOR: Tom DINH
    PROJECT: the3ds_framework
    DESCRIPTION: --
    -----*/

    #load all key values:
    require_once "core/bin/systemkey.php";
    #load all Mirana config:
    require_once "config.php";

    #showing error
    if (SYSTEM_ERROR_SHOW){
        error_reporting(-1);
        ini_set('display_errors', 'On');
        ini_set('display_startup_errors', 'On');
        ini_set('html_errors', 'On');
    } else {
        error_reporting(0);
        ini_set('display_errors', 'Off');
        ini_set('display_startup_errors', 'Off');
        ini_set('html_errors', 'Off');
    }

    #import folder + loader:
    require_once "core/bin/folder.php";
    require_once "core/bin/loader.php";
    Mirana_Loader::loadMirana();

    /*
    #config shortcut and temlate
    try {
        if (!file_exists(APP_CONFIG_SHORTCUT))
            throw new Exception (ERR_NOT_INSTALLED);
        else
            require_once APP_CONFIG_SHORTCUT;
    } catch (Exception $e){
        die("<h1>".$e->getMessage()."</h1>");
    }
    */

    date_default_timezone_set(TIMEZONE);


    #load the FoundationClass for PHP
    #require_once "MVC/foundationClass.php";
    #$webPage = new FoundationClass();

    #####################################################
    #Finish init and loading
 ?>
