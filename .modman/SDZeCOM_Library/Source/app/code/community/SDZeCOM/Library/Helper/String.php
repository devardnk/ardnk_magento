<?php
/**
 *
 * @author akniss
 *
 * @version 1.0
 *
 */
class SDZeCOM_Library_Helper_String extends SDZeCOM_Library_Helper_Data {

	/**
	 *
	 * @author akniss
	 * @version 1.0
	 * 
	 * @access static
	 *
	 * Prüft, ob zwei Strings gleich sind
	 *
	 * @param string $str1 der erste String
	 * @param string $str2 der zweite String
	 *
	 * @return boolean TRUE falls die Strings gleich sind | FALSE
	 */
	public static function isEqual ( $str1 , $str2 ) {

		if ( is_null ( $str1 ) || is_null ( $str2 ) ) {
			return FALSE;
		}
	
		$str1 = strtolower ( str_replace ( array ( " " , "\r" , "\n" , "\t" ) , "" , $str1 ) );
		$str2 = strtolower ( str_replace ( array ( " " , "\r" , "\n" , "\t" ) , "" , $str2 ) );

		if ( ( strlen ( $str1 ) == 0 && strlen ( $str2 ) > 0 ) ||
		( strlen ( $str1 ) > 0 && strlen ( $str2 ) == 0 ) ) {
			return FALSE;
		}
	
		return strcmp ( $str1 , $str2 ) == 0;
			
	}
}
