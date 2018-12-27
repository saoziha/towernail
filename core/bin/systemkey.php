<?php
	/*-----
	COMPANY: the3ds team
	AUTHOR: Tom DINH
	PROJECT: the3ds_framework
	DESCRIPTION: --
	-----*/

	##############
	//if (!defined('__SOMETHING__')) {}
	#values
	define("DS", DIRECTORY_SEPARATOR); //backend separator
	define("FS", "/"); //frontend separator
	define("NS", "\\"); //namespace separator
	##############################
	define("APP_FOLDER", "application/"); //constant folder name starts with plain and ends with a frontend separator
	define("APP_BIN", "bin/");
	define("APP_VIEW_FOLDER", "view/");
	define("THEME_COMMON_FOLDER", "common/");
	define("THEME_LAYOUT_FOLDER", "layout/");
	define("THEME_PAGE_FOLDER", "page/");
	define("THEME_COM_FOLDER", "component/");
	define("THEME_ROUTEFILE", "register-route.php");
	define("APP_API_FOLDER", "api/");
	define("APP_PUB_FOLDER", "public/");
	define("EXT_FOLDER", "extension/");
	define("ANGULAR_FOLDER", "angularjs/");

	define("APP_MAINFILE", "mirana_main.php");
	define("APP_PAGE_REG", "bin/register-page.php");
	define("APP_DEFAULT_REG", "bin/register-default.php");
	define("APP_PKGFILE", "bin/register-package.php");
	define("THEME_REGISTERFILE", "view/register-theme.php");
	define("APP_ACCESSCONTROL_REG", "bin/register-acesscontrol.php");
	define("ERR_404", "core/html/404.html");
	define("EXT_FILE_REGISTER", "regext.php");
	define("APP_WEBSERVICE_OLD", "webservice.php");

	define("APP_FRONTEND_PUBLIC", "public");
	define("EXT_FRONTEND_PATH", "extmirana");
	define("THEME_ANGULARJS", "angularjs");

	define("APP_MAIN_FUNCTION", "main");
	define("GET_REQUEST", "PARAMS");
	define("APP_RUNTIME", "runtime");

	define("APP_PACKAGE", "miranaPackageList");
	define("DB_NAMESPACE", "database");
	define("APP_WEBSERVICE", "webservice");
	#location

	################################################
	#accessControl
	define("AC_SERVICEDATA","serviceData.php");
	define("CMS_SERVICEDATA","serviceData.php");
	define("JSON_PDENIED", "permission_denied");
	define("USER_ID", "USER_ID");
	define("USERNAME", "USERNAME");
	define("ROOT_USER", "ROOT_USER");
	define("FAKE_ROOT_USER", "FAKE_ROOT_USER");
	define("ROOT_USER_ROLE", "MIRANA_ROOT_USER");
	define("APP_USER_ROLE", "MIRANA_APP_USER");
	define("APP_ADMIN", "APP_ADMIN");
	define("CURRENT_TEN", "CURRENT_TEN");
	define("USER_ROLE", "USER_ROLE");

	################################################
	#applications

	#sys admin
	define("SYSADMIN_APP", "_admin");
	define("SYSTEM_ADMIN", "sysadmin");
	define("SYSTEM_CMS", "cmsadmin");
	define("SYSADMIN_AUTH", "authentication");
	define("SYSADMIN_LOGINPAGE", "login");

	#app
	################################################
	#DAO
	define("STATUS_SQL", "sStatus");
	define("COUNT_SQL", "sDataCount");
	define("VALUE_SQL", "sData");
	define("MESSAGE_SQL", "sMessage");

	define("RESULT_SQL", "result");
	define("ERR_SQL_INFO", "sqlErrorMessage");
	####################################################
	#db
	define("PGSQL","DB_Postgres");
	define("MYSQL","DB_MySQL");
	#database properties
	define("DB_TYPE", 	"db_type");
	define("DB_URL", 	"db_url");
	define("DB_PORT", 	"db_port");
	define("DB_NAME", 	"db_name");
	define("DB_USER", 	"db_username");
	define("DB_PASS", 	"db_password");
	define("APP_DBFILE", "bin/register-datasource.php");

	#system page

	#plugin

	#################################################
	#cache
	define("SYSTEM_CACHE", "SYSCACHE");
	define("SYSTEM_CACHE_MEM", "CACHEMEM");
	define("SYSTEM_CACHE_FILE", "CACHEFILE");
	define("SYSTEM_CACHE_MEMSIZE", "config/SIZE.cache");
	define("SYSTEM_CACHE_DATA", "config/DATA.cache");

	#errorMessage
	###################################################
	define("NO_PROPERTY", "No such property");
	define("ALL_STRING", "Database properties must be all set as string");
	define("NO_AVAI_CON", "No connection is currently available");
	define("INCORRECT_VALUE", "Incorrect input value(s) detected");
	define("ERR_NOT_INSTALLED", "SYSTEM WAS NOT CORRECTLY INSTALLED<br>PLEASE CONTACT ADMIN");
	define("PERMISSION_DENIED_FUNCTION", "denied");

	define("ERR_ACCOUNT_LOCKED", "Account is locked. Please contact admin.");
	define("ERR_NO_CLASS", "Class is not defined");
	define("ERR_NO_FUNCTION", "Class is defined but Function is not defined");
	define("ERR_REQUIRED_VAL", "Parameters for called function is not enough");
	define("ERR_DATATYPE", "Datatype of parameter(s) are not correct.");
	define("ERR_MODEL_PARAMS", "Check required parameters: APP, MOD, CLS, FNC");
	define("ERR_ROLE_FAILURE", "Check rove cover definition: could not cover backward");
	define("ERR_NO_TENANT", "This user does not belong to any tenant");
	define("ERR_OVERFLOW_STRING", "String values is overflow.");
	#errorPage
	#################################
	// define("INVALID_STRING", "
	// 	<html>
	// 		<h1>
	// 			INVALID CONTENT TYPE !!
	// 		</h1>
	// 	</html>
	// ");

	// #pageformat
	// ###################################
	// define("MAIN_PLAIN_FORMAT","
	// 	<!DOCTYPE html>
	// 	<html xmlns=\"http://www.w3.org/1999/xhtml\">
	// 		<head>
	// 			<title> Mirana </title>
	// 			<meta name='viewport' content='width=device-width, initial-scale=1.0' />
	// 	        <meta charset='UTF-8' />
	// 	        <link rel='icon' type='image/png' href='publics/images/logo.png'>
	// 			".RENDER_BASEDIR_INSERT."\n
	// 			".RENDER_CSS_INSERT."\n
	// 		</head>
    //
	// 		<body>
	// 			<div id=\"mirana-main-wrapper\">
	// 				<div id=\"mirana-content-wrapper\">
	// 					".RENDER_CONTENT_INSERT."\n
	// 				</div>
	// 			</div>
	// 			".RENDER_JS_INSERT."\n
	// 		</body>
	// 	</html>
	// ");
	// define("MAIN_CMS_FORMAT","
	// 	<!DOCTYPE html>
	// 	<html xmlns=\"http://www.w3.org/1999/xhtml\">
	// 		<head>
	// 			".RENDER_BASEDIR_INSERT."\n
	// 			".METATAG_INSERT."\n
	// 			".RENDER_CSS_INSERT."\n
	// 		</head>
    //
	// 		<body>
	// 			<div id=\"mirana-main-wrapper\">
	// 				<div id=\"mirana-content-wrapper\">
	// 					".RENDER_CONTENT_INSERT."\n
	// 				</div>
	// 			</div>
	// 			".RENDER_JS_INSERT."\n
	// 		</body>
	// 	</html>
	// ");

	#BAK
	#define("MVC_RENDERFOLDER", "RFLD");
	#define("MVC_PERMISSION_DENIED", "STATUS_PDENIED");
	#define("MVC_CONFIGTABLE", "CONFIG_TABLE");
	#define("MVC_PROPERTIESTABLE", "PROP_TABLE");
	#define("MVC_FOLDERLIST", "FOLDER_LIST");
	#define("PARAM_LIST", "PARAM_LIST");
	#define("REQUIRED_PARAM", "REQUIRED_PARAM");
?>
