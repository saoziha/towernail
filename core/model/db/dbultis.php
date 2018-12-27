<?php
/*--
COMPANY: ezdev (former: the3ds)
AUTHOR: Tom DINH
PROJECT: mirana core v2
DESCRIPTION: Domain register class
--*/
	class Mirana_DbUltis {
		########################################################################
		//check if date is valid then return $date in Y-m-d format, else return false;
		protected static function validateDate($inputVal, $format){
			if ($format === NULL){
				$dateFormat = array(
					"Y-d-m",
					"Y-m-d",
					"Y/m/d",
					"d-m-Y",
					"d/m/Y",
					"m/d/Y",
					"y-d-m",
					"y-m-d",
					"y/m/d",
					"d-m-y",
					"d/m/y",
					"m/d/y"
				);
				$timeFormat = array(
					"",
					"H:i:s",
					"h:i:sa",
					"h:i:sA",
					"h:i:s a",
					"h:i:s A"
				);
				$dateTimeFormat = array();
				foreach ($dateFormat as $eDate){
					foreach ($timeFormat as $eTime){
						$dateTimeFormat[] = trim("$eDate $eTime");
					}
				}
				foreach ($dateTimeFormat as $e){
					$date_ = DateTime::createFromFormat($e, $inputVal);
					if ($date_ && $date_->format($e) == $inputVal){
						return $date_->format("Y-m-d H:i:s");
					}
				}
			} else {
				$date_ = DateTime::createFromFormat($format, $inputVal);
				return $date_->format("Y-m-d H:i:s");
			}
			return false;
		}

		//output to timestamp format
		public static function formatDateTime($dbType, $inputVal, $format = NULL){
			$inputVal = self::validateDate($inputVal, $format);
			//TODO:should be rewrite using locale
			switch ($dbType){
				case PGSQL: return self::toPostgresTimestamp($inputVal); break;
				case MYSQL: return self::toMysqlTimestamp($inputVal); break;
				default: return null; break;
			}
		}

		//output into postgres format
		private static function toPostgresTimestamp($inputVal){
			if ($inputVal !== NULL && $inputVal !== false)
				return date('Y-m-d H:i:s', strtotime($inputVal));
			else
				throw new Exception(INCORRECT_VALUE);
			return null;
		}

		//output into mySQL format
		private static function toMysqlTimestamp($inputVal){
			if ($inputVal !== NULL && $inputVal !== false)
				return date('Y-m-d H:i:s', strtotime($inputVal));
			else
				throw new Exception(INCORRECT_VALUE);
			return null;
		}
		################################################
		################################################
		################################################
		#SQL builder
		//TODO: move code here
		public static function htmlEscape($needle){
			// return htmlspecialchars($needle, ENT_QUOTES);
			return $needle;
		}

		################################################
		################################################
		################################################
	}
?>
