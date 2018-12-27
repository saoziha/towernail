<?php
    /*--
    COMPANY: ezdev (former: the3ds)
    AUTHOR: Tom DINH
    PROJECT: mirana core v2
    DESCRIPTION: main class for handling Mirana Request
    --*/

    class Mirana {
        public function __construct(){            
            Mirana_Security::sec_session_start();
		}

        public function main(){
            try {
                Mirana_Routing::route();
            } catch (Exception $e){
                if (intval($e->getCode()) === 404){
                    require_once ERR_404; die();
                } else {
                    if (MIRANA_ERROR_SHOW){
                        echo "<h1>Unexpected error. Message below:</h1>";
                        die("<p>".$e->getMessage()."</p>");
                    } else {
                        echo "<h1>Unexpected error.</h1>";
                        die("Error message showing is: <strong>OFF</strong>");
                    }
                }
            }
            return null;
        }
    }
?>
