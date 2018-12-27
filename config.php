<?php
    /*--
    COMPANY: ezdev (former: the3ds)
    AUTHOR: Tom DINH
    PROJECT: mirana core v2
    DESCRIPTION: model class
    DATE: Oct-2016
    --*/

	define("PHPDOM_XML_VERSION", "1.0"); //default = 1.0, barely use 1.1 for specific purpose
	define("PHPDOM_XML_ENCODING", "utf-8"); //the encoding char-set set in view
    //execution time
	define("EXEC_TIME", FALSE);
	//system
	define("TIMEZONE", "ASIA/HO_CHI_MINH");
	define("USING_ACCESSCONTROL", TRUE); //turn on $AUTHORIZATION in model class
	define("MIRANA_ERROR_SHOW", TRUE);
	define("SYSTEM_ERROR_SHOW", TRUE);
	define("LOGIN_THRESHOLD", 10);
	define("GET_REQUEST_INPUT_LIMIT", 1*1024); //1KB as default
	define("POST_REQUEST_INPUT_LIMIT", 32*1024*1024); //32MB as default
	define("USING_STRICT_CLASS_PROPS", FALSE);
	define("STARFALL_RECURSIVE", TRUE); //component may or may not have children

    define("USING_MULTITENANCY", TRUE);
	define("REMOVE_MULTITENANCY", FALSE);
	//default
	#define("DEFAULT_APP", SYSTEM_ADMIN);
	define("DEFAULT_APP", "test");

	define("USERDEF_ADMIN_APP", "");

	define("PLUGIN_BOOTSTRAP", TRUE);
	define("PLUGIN_JQUERY", TRUE);
	define("PLUGIN_SYSTEM", TRUE);

	//shared_mem
	define("SYSTEM_CACHE_LENGTH", 1*1024*1024); // 1MB - Default value
	define("SYSTEM_CACHE_SIZE_LENGTH", 10);
	define("SYSTEM_CACHE_ID", 0x00000000);
	define("SYSTEM_CACHE_SIZE_ID", 0x00000001);
	define("SYSTEM_CACHE_MODE", SYSTEM_CACHE_FILE);

	//folder
	define("ENCODE_RES", true); //put back to true when deployed

	//session
	define("SESSION_SECURED", TRUE);	// Set to true if using https.
	define("SESSION_HTTPONLY", TRUE);	// This stops javascript being able to access the session id.
	define("SESSION_ONLYCOOKIE", 1);
	define("MVC_SESSION_NAME", "mirana");
?>
