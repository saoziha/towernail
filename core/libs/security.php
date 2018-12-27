<?php
    class Mirana_Security {
        ##################################################
        public static function randString( $len = 32 ){
            if (is_int($len)){
                return substr(hash("md5", microtime()*rand(1,32), false), 0, $len);
            } else {
                ;
            }
        }

        #######################        ENCRYPTION      #########################
        public static function genPrivateKey(){
            if ( !isset( $_SESSION[MVC_IV] ) ){
                srand((double) microtime() * 1000000);
                $_SESSION[MVC_IV] = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_RAND);
            }

            if ( !isset($_SESSION[MVC_SESSION_SKEY]) ){
                $_SESSION[MVC_SESSION_SKEY] = self::randString();
                $_SESSION[MVC_SESSION_CHKSUM] = microtime();
            } else {
                return false;
            }
            return true;
        }

        private static function safeEncrypt($message, $key){
            if (isset($_SESSION[MVC_IV])){
                $checksumLen = 8;
                $iv = $_SESSION[MVC_IV];
                $strChecksum = substr( hash("md5", $_SESSION[MVC_SESSION_CHKSUM], false),0,$checksumLen );
                $strEncode = rtrim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $message, MCRYPT_MODE_CBC, $iv)), "\0\3");
                return $strChecksum.$strEncode;
            } else {
                return false;
            }
        }

        private static function safeDecrypt($encrypted, $key){
            if (isset($_SESSION[MVC_IV])){
                $checksumLen = 8;
                $iv = $_SESSION[MVC_IV];
                $strChecksum = substr( hash("md5", $_SESSION[MVC_SESSION_CHKSUM], false),0,$checksumLen );
                $strChecksumEncrypted = substr( $encrypted,0,$checksumLen );
                $strEncrypted = substr( $encrypted,$checksumLen,strlen($encrypted)-$checksumLen);
                if ( $strChecksum === $strChecksumEncrypted){
                    return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($strEncrypted), MCRYPT_MODE_CBC, $iv), "\0\3");
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        ##################################################
		public static function sec_session_start() {
			if ( session_status() === PHP_SESSION_NONE) {
				ini_set('session.use_only_cookies', SESSION_ONLYCOOKIE); // Forces sessions to only use cookies.
				$cookieParams = session_get_cookie_params();
				session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], SESSION_SECURED, SESSION_HTTPONLY);
				session_name( MVC_SESSION_NAME );

				// regenerated the session, delete the old one.
				session_start();
				session_regenerate_id();//regenerate id without delete old ones
			}
		}

    }
?>
