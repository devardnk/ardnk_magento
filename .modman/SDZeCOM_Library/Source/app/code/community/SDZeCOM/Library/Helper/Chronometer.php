<?php 
/**
 * SDZeCOM_Library_Helper_Chronometer
 *
 * @author     Florian Kolb
 * @copyright  Copyright (c) 2009 SDZeCOM GmbH & Co. KG (http://www.sdzecom.de)
 */
class SDZeCOM_Library_Helper_Chronometer extends SDZeCOM_Library_Helper_Data
{
	/**
	 * Startzeit als Unix-Timestamp
	 * @var float
	 */
	private $start;
	
	/**
	 * Endzeit als Unix-Timestamp
	 * @var float
	 */
	private $end;
	
	
	
	/**
	 * Gibt den Unix-Timestamp als float zurück 
	 * @return number
	 */
	private function microtime_float()
	{
	    list($usec, $sec) = explode(" ", microtime());
	    return ((float)$usec + (float)$sec);
	}
	
	
	/**
	 * Startet die Zeitmessung
	 * 
	 */
	public function start()
	{
		$this->start = $this->microtime_float();	
	}
	
	
	/**
	 * Stoppt / Beendet die Zeitmessung
	 * 
	 */
	public function stop()
	{
		$this->end = $this->microtime_float();
	}
	
	
	/**
	 * Gibt die gemessene Zeit als formatierte Zahl zurück
	 * 
	 * @param number $decimals Wieviele Nachkommastellen soll das Ergebnis haben. Falls keine Angabe gemacht wird wird 2 verwendet
	 * @return string
	 */
	public function getTime($decimals = 2)
	{
		$time = $this->end - $this->start;
		return number_format($time, $decimals);
	}
}