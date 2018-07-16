<?php
/**
 *
 * @author akniss
 *
 * @version 1.0
 *
 */
class SDZeCOM_Library_Helper_Directory extends SDZeCOM_Library_Helper_Data {

    /**
     *
     * @author Michael Barth, akniss
     * 
	 * @version 1.0
	 * 
	 * @access static
	 *
	 * Verknüpft übergebene Teilstring zu einem Dateipfad
	 *
	 * @param string $path1, $path2, $path3, ...
	 *
	 * @return string $path
	 */
	public static function joinPaths() {

		$args = func_get_args($boolUlr=FALSE);

	    $delimiter = '/';
	    $test = implode('', $args);
	    if(strpos($test, '\\') !== false) {
	    	$delimiter = '\\';
	    }

    	$prefix = '';
	    if(substr($args[0], 0, 1) == $delimiter) {
	    	$prefix = $delimiter;
	    }


	    $paths = array();
	    foreach ($args as $arg) {
	    	$arg = explode($delimiter, $arg);
	        $paths = array_merge($paths, $arg);
	    }

	    $paths = array_filter($paths);
	    if(count($paths) < 1) {
	    	return "";
	    }


    	$path = $prefix;

	    foreach ($paths as &$p) {
       		$p = trim($p, $delimiter);
       		$path .= $p.$delimiter;
	    }

	    // Remove trailing delimiter
	    $path = substr($path, 0, strlen($path) - 1);


	    $path = str_replace( 'http:/' , 'http://', $path);

	    return $path;
	}
}
