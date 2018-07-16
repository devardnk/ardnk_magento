<?php

/**
 *
 * @author mwalter, akniss
 */
abstract class SDZeCOM_Integration_Model_Mapper_Io_Csv_Abstract extends SDZeCOM_Integration_Model_Mapper_Abstract {
    
	/**
	 * 
	 * @var $csvDelimiter
	 */
	protected $csvDelimiter = ';';
	
	/**
	 * 
	 * @var $csvEnclosure
	 */
	protected $csvEnclosure = '"';
	
	/**
	 * 
	 * @var $csvEscape;
	 */
	protected $csvEscape = '\\';
	
	/**
	 * 
	 * @var $detectColumns
	 */
    protected $detectColumns = true;
    
    /**
     * 
     * @var $csvColumns
     */
    protected $csvColumns = array();
	
	/**
	 *
	 * @param array $lineData        	
	 */
	abstract protected function mapFromCsvLine( array $lineData );

    /**
     * @param string $filename
     * @return array
     */
    protected function mapFromFile ( $filename ) {
    	
    	$content = file($filename, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
    	
    	$this->mapFromCsvLines($content);
    	
    	return $this->getData();
    }

    protected function mapFromCsvLines ( array $lines ) {

        if ( $this->detectColumns ) {
            $csvColumns = array_shift( $lines );
            
            $this->csvColumns = str_getcsv (
            		$csvColumns, 
            		$this->csvDelimiter , 
            		$this->csvEnclosure , 
            		$this->csvEscape
            );
        }
       
       $countCsvColumns = -1;
 
       if (is_array($this->csvColumns)) {
       		$countCsvColumns = count($this->csvColumns);
       }
     

        foreach ( $lines as $lineIndex => $line ) {

        	$line = $this->prepareLine( $line );
            
            $lineData = str_getcsv (
            		$line,
            		$this->csvDelimiter ,
            		$this->csvEnclosure ,
            		$this->csvEscape
            );
            
            $countLineData = count( $lineData );
            
            if ( $this->detectColumns ) {
            	
            	if ($countLineData != $countCsvColumns ) {
            		
            		Mage::log( "{SDZeCOM_Integration_Model_Mapper_Io_Csv_Abstract}: CSV-Zeile {$lineIndex} übersprungen, Anzahl Spalten Header {$countCsvColumns} stimmt nicht mit Anzahl Spalten Daten {$countLineData} überein" );
            		continue;
            	}
            	
            	$lineData = array_combine ( $this->csvColumns , $lineData );
            }
   
			$this->mapFromCsvLine ( $lineData );
        }
    }

    /**
     * Overwrite this method if you need to convert Encoding or do replacements on the complete line
     *
     * @param $line
     * @return mixed
     */
    protected function prepareLine($line)
    {
        return $line;
    }

}