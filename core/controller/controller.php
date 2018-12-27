<?php
    /*--
    COMPANY: ezdev (former: the3ds)
    AUTHOR: Tom DINH
    PROJECT: mirana core v2
    DESCRIPTION: controller class
    --*/

    abstract class Mirana_C {
        #property declaration
        private static $domainAppName;
		#####

		###################### MAIN STRUCTURE ##################################
		public function __construct(){
            //init values
            self::$domainAppName = NULL; //NULL means no domain is selected
            return null;
		}
		#########################       API   ##################################
        
    }
?>
