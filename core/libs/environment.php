<?php
	/*-----
	COMPANY: the3ds team
	AUTHOR: Tom DINH
	PROJECT: the3ds_framework
	DESCRIPTION: --
	-----*/
	class Mirana_Environment {
		private static function getSize_mem(){
			$shmkey = shmop_open(SYSTEM_CACHE_SIZE_ID, "a", 0777, SYSTEM_CACHE_SIZE_LENGTH);
			$size = shmop_read($shmkey, 0, SYSTEM_CACHE_SIZE_LENGTH);
			shmop_close($shmkey);
			$size = intval(trim($size, "\0"));

			return $size===0?SYSTEM_CACHE_LENGTH:$size;
		}

		private static function getSize_file(){
			if (file_exists(SYSTEM_CACHE_MEMSIZE)){
				$f = fopen(SYSTEM_CACHE_MEMSIZE, "r");
				$size = fread($f, SYSTEM_CACHE_SIZE_LENGTH);
				fclose($f);
				$size = intval(trim($size, "\0"));
			} else {
				$size = 0;
			}
			return $size===0?SYSTEM_CACHE_LENGTH:$size;
		}

		private static function getSize(){
			if (SYSTEM_CACHE_MODE === SYSTEM_CACHE_MEM){
				return self::getSize_mem();
			} else if (SYSTEM_CACHE_MODE === SYSTEM_CACHE_FILE) {
				return self::getSize_file();
			} else {
				;
			}
		}
################################################################################
		private static function updateSize_mem($size){
			$retVal = NULL;
			if (is_integer($size)){
				$shmkey = shmop_open(SYSTEM_CACHE_SIZE_ID, "c", 0777, SYSTEM_CACHE_SIZE_LENGTH);
				$shm_bytes_written = shmop_write($shmkey, $size, 0);
				shmop_close($shmkey);
				$retVal = $shm_bytes_written === sizeof($size);
			} else {
				$retVal = false;
			}

			return $retVal;
		}

		private static function updateSize_file($size){
			$retVal = NULL;

			if (!file_exists(SYSTEM_CACHE_MEMSIZE)){
				fclose(fopen(SYSTEM_CACHE_MEMSIZE, "w"));
			}

			if (is_integer($size)){
				$f = fopen(SYSTEM_CACHE_MEMSIZE, "w");
				$bytes_written = fwrite($f, $size);
				fclose($f);
				$retVal = $bytes_written === sizeof($size);
			} else {
				$retVal = false;
			}

			return $retVal;
		}

		private static function updateSize($size){
			if (SYSTEM_CACHE_MODE === SYSTEM_CACHE_MEM){
				return self::updateSize_mem($size);
			} else if (SYSTEM_CACHE_MODE === SYSTEM_CACHE_FILE) {
				return self::updateSize_file($size);
			} else {
				;
			}
		}
################################################################################
		//replace all the cache with $content
	    public static function save_mem($fData){
			$fData = json_encode($fData);
			$shmkey = self::get_key();
	        $shm_bytes_written = shmop_write($shmkey, $fData, 0);
			shmop_close($shmkey);
			if (!$shm_bytes_written){
				#TODO: backup data?
				;
			} else {
				self::updateSize($shm_bytes_written);
			}

            return $shm_bytes_written;
	    }

		public static function save_file($fData){
			if (!file_exists(SYSTEM_CACHE_DATA)){
				fclose(fopen(SYSTEM_CACHE_DATA, "w"));
			}

			$fData = json_encode($fData);
			$f = fopen(SYSTEM_CACHE_DATA, "w");
	        $bytes_written = fwrite($f, $fData);
			fclose($f);
			if (!$bytes_written){
				#TODO: backup data?
				;
			} else {
				self::updateSize($bytes_written);
			}

            return $bytes_written;
	    }

		public static function save($fData){
			if (SYSTEM_CACHE_MODE === SYSTEM_CACHE_MEM){
				return self::save_mem($fData);
			} else if (SYSTEM_CACHE_MODE === SYSTEM_CACHE_FILE) {
				return self::save_file($fData);
			} else {
				;
			}
		}
################################################################################
	    public static function load_mem(){
			$shmkey = self::get_key("a");
	        $fData = shmop_read($shmkey, 0, self::getSize());
			shmop_close($shmkey);

			$retVal = json_decode(trim($fData, "\0"), true);

	        if(!$fData) {
	            return false;
	        } else {
	            return $retVal;
			}
	    }

		public static function load_file(){
			if (file_exists(SYSTEM_CACHE_DATA) && self::getSize()>0){
				$f = fopen(SYSTEM_CACHE_DATA, "r");
		        $fData = fread($f,self::getSize());
				fclose($f);
				$retVal = json_decode(trim($fData, "\0"), true);
			} else {
				$fData = false;
			}

	        if(!$fData) {
	            return false;
	        } else {
	            return $retVal;
			}
	    }

		public static function load(){
			if (SYSTEM_CACHE_MODE === SYSTEM_CACHE_MEM){
				return self::load_mem();
			} else if (SYSTEM_CACHE_MODE === SYSTEM_CACHE_FILE) {
				return self::load_file();
			} else {
				;
			}
		}
################################################################################
	    public static function clear(){
			//clear content by write empty string
			if (!self::save(NULL)){
				return false;
			} else {
				return true;
			}
	    }
################################################################################
		private static function get_key($flag = "c"){
			$size = self::getSize();
			$shmkey = shmop_open(SYSTEM_CACHE_ID, $flag, 0777, $size);

			if(!$shmkey) {
				return false;
			} else {
				return $shmkey;
			}//fi
		}

		//close current cache
		private static function close($shmkey){
			if(!shmop_close($shmkey)) {
				return false;
			} else {
				return true;
			}
		}

		private static function delete($shmkey){
			if(!shmop_delete($shmkey)) {
				shmop_close($shmkey);
				return false;
			}else{
				shmop_close($shmkey);
				return true;
			}
		}

		//check if a string is key or not
	    private static function isKey($key, $size){
	        if($ret = self::get_key($key, $size)){
	            return $ret;
	        }else{
	            return false;
	        }
	    }

		private static function getSytemId($name = "data"){
			$retVal = array(
				"data" => 0,
				"size" => 1
			);
			if (isset($retVal[$name])){
				return $retVal[$name];
			} else {
				return NULL;
			}
		}
	}
?>
