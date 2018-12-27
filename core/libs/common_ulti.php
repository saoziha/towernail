<?php
	//common Ulti for framework
	class CommonUlti {
		public static function locationStandardize( $folder_location ){
			$retVal = NULL;
			if (is_string($folder_location)){
				$s = trim($folder_location);
				$s = str_replace("/", DS, $s);
				$s = str_replace("\\", DS, $s);
				$retVal = trim($s, DS);
			} else {
				;
			}
			return $retVal;
		}

		public static function urlStandardize( $url ){
			$retVal = NULL;
			if (is_string($url)){
				$s = trim($url);
				$s = str_replace("/", FS, $s);
				$s = str_replace("\\", FS, $s);
				$retVal = trim($s, FS);
			} else {
				;
			}
			return $retVal;
		}

		public static function namespaceStandardize( $namespace ){
			$retVal = NULL;
			if (is_string($namespace)){
				$s = trim($namespace);
				$s = str_replace("/", NS, $s);
				$s = str_replace("\\", NS, $s);
				$retVal = trim($s, NS);
			} else {
				;
			}
			return $retVal;
		}

		public static function getNearestFolder($urlLocation){
			$retVal = NULL;
			$urlLocation = self::locationStandardize($urlLocation);
			if (is_string($urlLocation) && is_file($urlLocation)){
				$t = explode(DS, $urlLocation);
				if (count($t) > 0) {
					$t = array_splice($t, count($t)-1, 1);
				}
				$retVal = implode(DS, $t);
			} else if (is_string($urlLocation) && is_dir($urlLocation)){
				$retVal = $urlLocation;
			} else {
				;
			}
			return $retVal;
		}

		public static function getNearestFolderName($urlLocation){
			$retVal = NULL;
			$urlLocation = self::locationStandardize($urlLocation);
			if (is_string($urlLocation) && is_file($urlLocation)){
				$t = explode(DS, $urlLocation);
				if (count($t) > 0) {
					$t = array_splice($t, count($t)-1, 1);
				}
				$retVal = end($t);
			} else if (is_string($urlLocation) && is_dir($urlLocation)){
				$t = explode(DS, $urlLocation);
				$retVal = end($t);
			} else {
				;
			}
			return $retVal;
		}
		########################################################################
		public static function topoDfs($u, $graph, $topo, $visited){
			$retVal = false;

			$visited[] = $u;
			if (isset($graph[$u])){
				foreach ($graph[$u] as $v){
					if (in_array($v, $topo)){
						return false;
					} elseif (!in_array($v, $visited)){
						$topo = $this->topoDfs($v, $graph, $topo, $visited);
					} else {
						;
					}
				}
			} else {
				;
			}
			if (is_array($topo)){
				$topo[] = $u;
			}
			$retVal = $topo;

			return $retVal;
		}
	}
?>
