<?php

class SDZeCOM_Aurednik_Block_Adminhtml_Sales_Order_View_Info_Block extends Mage_Core_Block_Template
{
	/**
	 * @var $order
	 */
	protected $order;

	/**
	 * @var
	 */
	protected $additionalOrderDataCSS = [];

	/**
	 * Bestellung zurückgeben
	 *
	 * @ticket
	 *
	 * @author Eugen Gutsche
	 *
	 * @return mixed|Varien_Object
	 */
	public function getOrder()
	{
		if (is_null($this->order))
		{
			if (Mage::registry('current_order'))
			{
				$order = Mage::registry('current_order');
			}
			elseif (Mage::registry('order'))
			{
				$order = Mage::registry('order');
			}
			else
			{
				$order = new Varien_Object();
			}
			$this->order = $order;
		}
		return $this->order;
	}


	/**
	 * Gibt zusätzliche Informationen zu der Bestellung aus der DB zurück
	 *
	 * @ticket https://projects.sdzecom.de/issues/6268
	 *
	 * @author Eugen Gutsche
	 * @return array
	 */
	public function getAdditionalOrderData($value)
	{
		$data = $this->order[$value];

		return $data;
	}


	/**
	 * @TODO Kommentar
	 * @ticket
	 *
	 * @author Eugen Gutsche
	 * @param $item
	 * @return mixed
	 */
	public function getAdditionalOrderItemData($item)
	{
		$additionalItemOrderData = $item->getData('product_options');
		$additionalItemOrderData = unserialize($additionalItemOrderData);

		return $additionalItemOrderData;
	}


	/**
	 * Liefert eine HTML-Tabelle mit Warnhinweisen eines Produkts
	 *
	 * @ticket https://projects.sdzecom.de/issues/11678
	 * @author Eugen Gutsche
	 * @param $additionalItemOrderData
	 * @param string $displayType
	 * @return string
	 */
	public function getProductWarningHintsTable($additionalItemOrderData, $displayType = "frontend")
	{
		// Nur Warnhinweise aus den Zusatzinformationen beziehen
		$itemWarnings = array_filter($additionalItemOrderData, function($k) {
			return $k == 'aurednik_product_warnings';
		}, ARRAY_FILTER_USE_KEY);

		if (isset($itemWarnings['aurednik_product_warnings']) && count($itemWarnings['aurednik_product_warnings']) == 0)
		{
			return '';
		}

		$colspan = $this->getAreaSpecificOrderTableRowCount($displayType);
		$customCSS = $this->getCustomCss($displayType, 'css/custom_order_' . $displayType . '.css');

		$html = strlen($customCSS) == 0 ? "" : $this->additionalOrderDataCSS[$displayType];
		$html .= '<tr class="product-warnings-table-row">';
		$html .= '	<td class="product-warnings-table-row-td" colspan="' . $colspan .'">';
		$html .= '		<strong>' . $this->__('Warnhinweise') . '</strong>';
		$html .= '	</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '	<td class="product-warnings-table-row-details-td" colspan="' . $colspan .'" >';
		$html .= '		<table class="product-warnings-table-list" cellpadding="0" width="100%" border="1" cellspacing="0" >';

		if($displayType == 'email')
		{
			$rowCounter = 0;
			foreach ($itemWarnings['aurednik_product_warnings'] as $key => $warning)
			{
				$rowStyle = ' style="background:white;" ';
				if ($rowCounter % 2 == 0)
				{
					$rowStyle = ' style="background:#dadfe0!important;" ';
				}
				$html .= '<tr>';
				$html .= '	<td' . $rowStyle . '>';
				$html .= 		$warning;
				$html .= '	</td>';
				$html .= '</tr>';
				$rowCounter++;
			}
		}
		else
		{
			$html .= '		<ul class="messages"><li class="error-msg">';
			$html .= '			<ul>';
			foreach ($itemWarnings['aurednik_product_warnings'] as $key => $warning)
			{
				$html .= '<li>';
				$html .= $warning;
				$html .= '</li>';
			}
			$html .= '</ul></li></ul>';
		}
		$html .= '</table></td></tr>';

		return $html;
	}


	/**
	 * Liefert eine HTML-Tabelle mit Zusatzinformationen eines Produkts nach dem Bestellvorgang
	 *
	 * @ticket https://projects.sdzecom.de/issues/11678
	 * @author Eugen Gutsche
	 * @param $additionalItemOrderData
	 * @param string $displayType
	 * @return string, HTML Tabelle
	 */
	public function getAdditionalOrderItemHtml($additionalItemOrderData, $displayType = "frontend")
	{
		$colspan = $this->getAreaSpecificOrderTableRowCount($displayType);
		$customCSS = $this->getCustomCss($displayType, 'css/custom_order_' . $displayType . '.css');

		// Warnhinweise aus dem Array filtern, da diese separat behandelt werden
		$additionalItemOrderData = array_filter($additionalItemOrderData, function($k) {
			return $k != 'aurednik_product_warnings';
		}, ARRAY_FILTER_USE_KEY);

		$html = strlen($customCSS) == 0 ? "" : $this->additionalOrderDataCSS[$displayType];
		$html .= '<tr class="additional-product-info-table-row">';
		$html .= '	<td class="additional-product-info-table-row-td" colspan="' . $colspan .'">';
		$html .= '		<strong>' . $this->__('Zusatzinformationen') . '</strong>';
		$html .= '	</td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '	<td class="additional-product-info-table-row-details-td" colspan="' . $colspan .'" >';
		$html .= '	<table class="additional-product-info-table-list" cellpadding="0" width="100%" border="1" cellspacing="0" >';

		$rowCounter = 0;
		foreach ($additionalItemOrderData as $attributeCode => $dataEntry)
		{
			if (count($dataEntry) == 0 || strlen($dataEntry) == 0)
			{
				continue;
			}

			/** @var Mage_Eav_Model_Entity_Attribute $attributeModel */
			$attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attributeCode);
			$attributeLabel = $attributeModel->getStoreLabel();
			if (strlen($attributeLabel) == 0)
			{
				$attributeLabel = $attributeCode;
			}

			$rowStyle = ' class="odd" style="background:white;" ';
			if ($rowCounter % 2 == 0)
			{
				$rowStyle = ' class="even" style="background:#dadfe0;" ';
			}

			$html .= '<tr><td' . $rowStyle . '><strong>' . $this->__($attributeLabel) . '</strong></td>';
			$html .= '<td' . $rowStyle . '>' . $dataEntry . '</td>';
			$html .= '</tr>';

			$rowCounter++;
		}
		$html .= '</table></td></tr>';

		return $html;
	}


	/**
	 * Liefert die Anzahl der Spalten, die für die Bestelltabelle für den jeweiligen Bereich benötigt werden
	 *
	 * @ticket https://projects.sdzecom.de/issues/11678
	 * @author Eugen Gutsche
	 * @param $displayType
	 * @return int
	 */
	protected function getAreaSpecificOrderTableRowCount($displayType)
	{
		switch ($displayType)
		{
			case 'email':
				return 4;
			case 'backend':
				return 11;
			default:
				return 5;
		}
	}


	/**
	 * Liefert entsprechend den Parametern ein Link zur CSS oder CSS-Code,
	 * der als inline style eingebunden werden kann
	 *
	 * @ticket https://projects.sdzecom.de/issues/11678
	 * @author Eugen Gutsche
	 * @param $displayType
	 * @param string $styleType
	 * @param $cssFileUrl
	 * @return mixed|string
	 */
	protected function getCustomCss($displayType, $cssFileUrl)
	{
		switch ($displayType)
		{
			case 'email':
				//Bei Email Templates muss das CSS als inline Style eingebunden werden
				//CSS: skin/frontend/aurednik/default/css/custom_order_mail.css
				$styleType = 'inline';
				break;
			case 'backend':
				//CSS: skin/adminhtml/default/aurednik/css/custom_order_backend.css
				$styleType = "";
				break;
			default:
				//CSS: skin/frontend/aurednik/default/css/custom_order_frontend.css
				$styleType = "";
		}

		if (!isset($this->additionalOrderDataCSS[$displayType]))
		{
			if ($styleType == 'inline')
			{
				//TODO - Set Url back to SkinUrl
				$customCSS = file_get_contents('http://192.168.56.102/magento/skin/frontend/aurednik/default/css/custom_order_email.css');//$this->getSkinUrl() . $cssFileUrl);
				$this->additionalOrderDataCSS[$displayType] = '<style type="text/css">' . $customCSS . '</style>';
			}
			else
			{
				$customCSS = $this->getSkinUrl() . $cssFileUrl;
				$this->additionalOrderDataCSS[$displayType] = '<link rel="stylesheet" href="' . $customCSS .'" type="text/css" media="all">';
			}

			return $this->additionalOrderDataCSS[$displayType];
		}

		return '';
	}
}