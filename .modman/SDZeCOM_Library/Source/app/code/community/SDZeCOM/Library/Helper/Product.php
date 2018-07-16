<?php
/**
 *
 * @author akniss
 *
 * @version 1.0
 *
 */
class SDZeCOM_Library_Helper_Product extends SDZeCOM_Library_Helper_Data 
{
	/**
	 * Gibt den Preisvorteil in Prozent zwischen 2 Preisen zurück
	 *
	 * @author akniss
	 * @version 1.0
	 *
	 * @param float $doubleRegularPrice
	 * @param float $doubleSpecialPrice
	 * @return float
	 */
	public function getProductDiscount ( $doubleRegularPrice, $doubleSpecialPrice=null ) {

		if( is_null( $doubleRegularPrice ) ) {
			throw new InvalidArgumentException();
		}

		if( is_null( $doubleSpecialPrice ) ) {
			throw new InvalidArgumentException();
		}

		$doubleDiscount = ( $doubleSpecialPrice / $doubleRegularPrice ) * 100;
		$doubleDiscount = 100 - $doubleDiscount;

		return number_format(round($doubleDiscount, 1, PHP_ROUND_HALF_UP), 1, ',', '.');
	}
}
