<?php
/**
 *
 * @author akniss
 *
 * @version 1.0
 *
 */
class SDZeCOM_Library_Helper_Date extends SDZeCOM_Library_Helper_Data {

	/**
	 *
	 * @author akniss
	 * 
	 * @version 1.2
	 *
	 * @access static
	 * 
	 * Prüft, ob ein Datum ( $intTimeStampFromPeriod ) innerhalb der Periode liegt
	 *
	 * @param string $intCheckDate, das Datum, dass gerprüft werden sollte
	 * @param string $intFrom, Beginn des Zeitraumes
	 * @param string $intTo, Ende des Zeitraumes
	 *
	 * @return boolean TRUE falls das übergebene Datum innerhalb der Periode liegt, ansonnsten boolean FALSE
	 */
	public static function inPeriod( $intCheckDate, $intFrom=null, $intTo=null ) {

		if( ( is_null( $intFrom ) || $intFrom == 0 ) &&  ( is_null( $intTo ) || $intTo == 0 ) ) { //Beide Daten sind null
			return TRUE;
		} else if( ( ( is_null( $intFrom ) || $intFrom == 0 ) ) &&  ( ! is_null( $intTo ) || $intTo != 0  ) ) { //Von-Datum  ist null
			if( $intTo >= $intCheckDate ) {
				return TRUE;
			}
		} else if( ( ! is_null( $intFrom ) || $intFrom > 0 ) && ( is_null( $intTo ) || $intTo == 0 ) ) { //Bis-Datum ist null
			if( $intFrom <= $intCheckDate ) {
				return TRUE;
			}
		} else if( ! is_null( $intFrom ) &&  ! is_null( $intTo ) ) { //Beide Daten sind gesetzt
			if( ( $intTo >= $intCheckDate ) &&  $intFrom <= $intCheckDate ) {
				return TRUE;
			}
		}

		return FALSE;
	}
}
