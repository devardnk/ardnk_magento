<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Block_Product_View_Attributes extends Mage_Catalog_Block_Product_View_Attributes
{

	/**
	 * (non-PHPdoc)
	 *
	 * @see Mage_Catalog_Block_Product_View_Attributes::getAdditionalData()
	 */
	public function getAdditionalData(array $excludeAttr = array(), $product = '')
	{

		$data = array();
		if (empty($product))
		{
			$product = $this->getProduct();
		}

		$attributes = $product->getAttributes();
		foreach ($attributes as $attribute)
		{
			if ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr))
			{

				if ($attribute->getAttributeCode() == 'weight')
				{
					$value = $product->getFormatedWeight(true);
				}
				else
				{
					$value = $attribute->getFrontend()->getValue($product);
				}

				if (!$product->hasData($attribute->getAttributeCode()))
				{
					$value = Mage:: helper('catalog')->__('N/A');
				}
				elseif (( string )$value == '')
				{
					$value = Mage:: helper('catalog')->__('No');
				}
				elseif ($attribute->getFrontendInput() == 'price' && is_string($value))
				{
					$value = Mage:: app()->getStore()->convertPrice($value, true);
				}

				if (is_string($value) && strlen($value))
				{
					$data [$attribute->getAttributeCode()] = array(
						'label' => $attribute->getStoreLabel(),
						'value' => $value,
						'code' => $attribute->getAttributeCode()
					);
				}
			}
		}
		return $data;
	}
}
