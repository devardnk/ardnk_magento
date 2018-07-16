<?php

/**
 * Stellt Methoden bereit, um die Produktbilder aus der CSV-Datei
 * auszulesen und in das Magento Import-Format zu mappen
 *
 * @author akniss
 *
 * @copyright SDZeCOM GmbH & Co. KG 2014
 *
 */
class Aurednik_Integration_Helper_Mapper_Io_Product_Images extends Aurednik_Integration_Helper_Mapper_Io_Product
{

	/**
	 * @var $thumbnailImage
	 */
	protected $thumbnailImage = '';

	/**
	 * @var $smallImage
	 */
	protected $smallImage = '';


	/**
	 * @var $baseImage
	 */
	protected $baseImage = '';


	/**
	 * @var $mediaGalleryImages
	 */
	protected $mediaGalleryImages = array();

	/**
	 * @var $imageAttributeId , Attribut Id, in der die Bilder gespeichert werden
	 */
	protected $imageAttributeId = 0;

	//------------------------------------public-area-----------------------------------------------------

	/**
	 * Konstruktor
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 */
	public function __construct()
	{
		$this->imageAttributeId =
			Mage::getSingleton('catalog/product')->getResource()->getAttribute('media_gallery')->getAttributeId();
	}


	/**
	 * (non-PHPdoc)
	 * @see Aurednik_Integration_Helper_Mapper_Io_Article::setImportData()
	 */
	public function setImportData(array $importData)
	{
		$this->importData = $importData;
		$this->setImages();
	}


	/**
	 * Gibt das Thumbnail Bild des Produkts zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string Thumbnail Bild des Produkts
	 */
	public function getThumbnailImage()
	{
		return $this->thumbnailImage;
	}


	/**
	 * Gibt das Base Image (Hauptbild) des Produkts zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string Thumbnail Bild des Produkts
	 */
	public function getBaseImage()
	{
		return $this->baseImage;
	}


	/**
	 * Prüft, ob beim übergebenen Bild, es sich um ein Thumbnail Bild handelt
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @param string $image , Bild, das geprüft werden soll
	 *
	 * @return boolean
	 */
	public function isThumbnailImage($image)
	{

		return $this->thumbnailImage == $image ? true : false;
	}


	/**
	 * liefert das Small-Image des Produkts
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return string Small-Image des Produkts
	 */
	public function getSmallImage()
	{
		$this->smallImage;

	}


	/**
	 * Prüft, ob beim übergebenen Bild, es sich um ein Small-Image handelt
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @param string $image , Bild, das geprüft werden soll
	 *
	 * @return boolean
	 */
	public function isSmallImage($image)
	{
		return $this->smallImage == $image ? true : false;
	}


	/**
	 * Prüft, ob beim übergebenen Bild, es sich um ein Base-Image handelt
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @param string $image , Bild, das geprüft werden soll
	 *
	 * @return boolean
	 */
	public function isBaseImage($image)
	{
		return $this->baseImage == $image ? true : false;
	}


	/**
	 * Liefert Galleriebilder des Produkts
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return array Galleriebilder
	 */
	public function getMediaGalleryImages()
	{
		return $this->mediaGalleryImages;
	}


	/**
	 * Liefert alle Bilder des Produkts
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return array alle Bilder des Produkts
	 */
	public function getAllImages()
	{

		//Galleriebilder
		$allImages = array();


		//Thumbnail Image
		if (strlen(trim($this->thumbnailImage)) > 0)
		{
			$allImages [] = $this->thumbnailImage;
		}

		//Small Image
		if (strlen(trim($this->smallImage)) > 0)
		{
			$allImages [] = $this->smallImage;
		}

		//Base Image
		if (strlen(trim($this->baseImage)) > 0)
		{
			$allImages [] = $this->baseImage;
		}

		if (!is_array($allImages) || count($allImages) == 0)
		{
			return array();
		}

		return array_unique($allImages);

	}


	/**
	 * Leer alle Bildvariablen
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 */
	public function clearAllImages()
	{
		$this->thumbnailImage = '';
		$this->smallImage = '';
		$this->baseImage = '';
	}


	/**
	 * Gibt die magento des Bilderattributs zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * @return int AttributId
	 */
	public function getImageAttributeId()
	{
		return ( int )$this->imageAttributeId;
	}

	//------------------------------------protected-area--------------------------------------------------

	/**
	 * Lädt ein Bild herunter
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @param string $url , Url des Bildes
	 *
	 * @return string Name des Bildes
	 *
	 */
	protected function downloadImage($url)
	{

		if (strlen(trim($url)) == 0)
		{
			return '';
		}

		if (isset ($url [0]) && ($url [0] == '+'))
		{
			$url = substr($url, 1);
		}

		$imageContent = file_get_contents($url);

		if ($imageContent === false)
		{
			return '';
		}

		$downloadImage = SDZeCOM_Library_Helper_Directory::joinPaths(
			Aurednik_Integration_Helper_Service_Io_Images::getProductImagesDownloadDirectory(),
			basename($downloadImage));


		file_put_contents($downloadImage, $imageContent);

		if (!file_exists($downloadImage))
		{
			return '';
		}

		return basename($downloadImage);
	}


	/**
	 * Gibt den Dateipfad zur Magento-Datei zurück, falls die Datei existiert
	 *
	 * @author akkniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @param string $url
	 *
	 * @return boolean| string Pfad zu der Datei im Magento
	 */
	protected function getLocalImage($url)
	{

		if (strlen(trim($url)) == 0)
		{
			return false;
		}

		if (isset ($url [0]) && ($url [0] == '+'))
		{
			$url = substr($url, 1);
		}

		$localImage = SDZeCOM_Library_Helper_Directory::joinPaths
		(
			Aurednik_Integration_Helper_Service_Io_Images::getProductImagesDownloadDirectory(),
			basename($url)
		);
		if (strlen(trim($localImage)) == 0 || !file_exists($localImage))
		{
			return false;
		}

		return basename($localImage);
	}


	//------------------------------------private-area----------------------------------------------------

	/**
	 * Gibt den Namen des Bildes, das heruntergeladen werden soll
	 * anhand seiner Url zurück
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * @param string $url , Url des Bildes
	 *
	 * @return string
	 */
	private function getDownloadImageFileName($url)
	{

		$urlComponents = array();

		parse_str($url, $urlComponents);


		if (!is_array($urlComponents) || count($urlComponents) == 0)
		{
			return '';
		}

		$imageId = array_shift($urlComponents);

		if (!is_array($urlComponents) || count($urlComponents) == 0)
		{
			return '';
		}

		$downloadImageFileName = '';

		if (strlen(trim($imageId)) > 0)
		{
			$downloadImageFileName .= $imageId;
		}

		if (isset ($urlComponents ['width']))
		{
			$downloadImageFileName .= '_' . $urlComponents ['width'];
		}

		if (isset ($urlComponents ['height']))
		{
			$downloadImageFileName .= '_' . $urlComponents ['width'];
		}

		if (isset ($urlComponents ['colorspace']))
		{
			$downloadImageFileName .= '_' . $urlComponents ['colorspace'];
		}

		if (isset ($urlComponents ['dpi']))
		{
			$downloadImageFileName .= '_' . $urlComponents ['dpi'];
		}

		if (isset ($urlComponents ['format']))
		{
			$downloadImageFileName .= '.' . $urlComponents ['format'];
		}

		return $downloadImageFileName;
	}


	/**
	 * Löscht ein existierendes Produktbild
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * @param string $image , Produktbild, dass gelöscht werden soll
	 *
	 */
	private function removeExistingImage($image)
	{

		if (strlen(trim($image)) == 0)
		{
			return;
		}

		$imagePath = SDZeCOM_Library_Helper_Directory::joinPaths(
			Mage::getBaseDir('media'),
			'catalog',
			'product',
			$image{0},
			$image{1},
			$image
		);

		if (file_exists($imagePath))
		{
			@unlink($imagePath);
		}
	}


	/**
	 * Lädt die Bilder herunter und speichert sie lokal
	 * im Magento Bilder Import Verzeichnis
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * @param array $images , Bilder die heruntergeladen
	 * werden sollen
	 *
	 * @return array heruntergeladene Bilder
	 */
	private function getImages(array $images)
	{

		if (count($images) == 0)
		{
			return array();
		}

		$downloadImages = array();

		foreach ($images as $currImage)
		{

			$downloadImage = $this->getLocalImage($currImage);

			if ($downloadImage === false)
			{
				$downloadImage = $this->downloadImage($currImage);
			}

			$downloadImages [] = $downloadImage;
		}

		return $downloadImages;
	}


	/**
	 * Setzt das Thumbnail-Bild
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 */
	private function setThumbnailImage()
	{

		if (!isset ($this->importData ['thumbnail']) || strlen(trim($this->importData ['thumbnail'])) == 0)
		{
			return;
		}

		$images = $this->getImages(explode(',', $this->importData ['thumbnail']));

		if (!is_array($images) || !isset ($images [0]))
		{
			return;
		}

		$this->thumbnailImage = $images [0];
	}


	/**
	 * Setzt das Small-Bild
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 */
	public function setSmallImage()
	{

		if (!isset ($this->importData ['small_image']) || strlen(trim($this->importData ['small_image'])) == 0)
		{
			return;
		}

		$images = $this->getImages(explode(',', $this->importData ['small_image']));

		if (!is_array($images) || !isset ($images [0]))
		{
			return;
		}

		$this->smallImage = $images [0];

	}


	/**
	 * Setzt das Base-Bild
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 */
	public function setBaseImage()
	{

		if (!isset ($this->importData ['image']) || strlen(trim($this->importData ['image'])) == 0)
		{
			return;
		}

		$images = $this->getImages(explode(',', $this->importData ['image']));

		if (!is_array($images) || !isset ($images [0]))
		{
			return;
		}

		$this->baseImage = $images [0];

	}


	/**
	 * Setzt die Gallerie Bilder
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 */
	public function setMediaGalleryImages()
	{

		if (!isset ($this->importData ['media_gallery']))
		{
			return;
		}

		$mediaGalleryImages = $this->getImages(explode(';', $this->importData ['media_gallery']));

		if (!is_array($mediaGalleryImages) || count($mediaGalleryImages) == 0)
		{
			return;
		}

		$mediaGalleryImages = array_filter($mediaGalleryImages);

		$this->mediaGalleryImages = array_unique($mediaGalleryImages);
	}


	/**
	 * Setzt das Thumbnail-Bild, Hauptbild, Small-Bild und
	 * die Gallerie Bilder
	 *
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 */
	private function setImages()
	{
		$this->clearAllImages();
		$this->setThumbnailImage();
		$this->setSmallImage();
		$this->setBaseImage();
		$this->setMediaGalleryImages();
	}
}