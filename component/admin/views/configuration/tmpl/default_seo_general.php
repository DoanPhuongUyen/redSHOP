<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) Copyright (C) 2008 - 2019 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

echo RedshopLayoutHelper::render(
	'config.config',
	array(
		'title' => JText::_('COM_REDSHOP_ENABLE_SEF_PRODUCT_NUMBER_LBL'),
		'desc'  => JText::_('COM_REDSHOP_TOOLTIP_ENABLE_SEF_PRODUCT_NUMBER_LBL'),
		'field' => $this->lists['enable_sef_product_number']
	)
);
echo RedshopLayoutHelper::render(
	'config.config',
	array(
		'title' => JText::_('COM_REDSHOP_ENABLE_SEF_NUMBER_NAME_LBL'),
		'desc'  => JText::_('COM_REDSHOP_TOOLTIP_ENABLE_SEF_NUMBER_NAME_LBL'),
		'field' => $this->lists['enable_sef_number_name']
	)
);
echo RedshopLayoutHelper::render(
	'config.config',
	array(
		'title' => JText::_('COM_REDSHOP_CATEGORY_IN_SEF_URL'),
		'desc'  => JText::_('COM_REDSHOP_TOOLTIP_CATEGORY_IN_SEF_URL_LBL'),
		'field' => $this->lists['category_in_sef_url']
	)
);
echo RedshopLayoutHelper::render(
	'config.config',
	array(
		'title' => JText::_('COM_REDSHOP_CATEGORY_TREE_IN_SEF_URL'),
		'desc'  => JText::_('COM_REDSHOP_TOOLTIP_CATEGORY_TREE_IN_SEF_URL_LBL'),
		'field' => $this->lists['category_tree_in_sef_url']
	)
);
echo RedshopLayoutHelper::render(
	'config.config',
	array(
		'title' => JText::_('COM_REDSHOP_AUTOGENERATED_SEO_LBL'),
		'desc'  => JText::_('COM_REDSHOP_TOOLTIP_AUTOGENERATED_SEO_LBL'),
		'field' => $this->lists['autogenerated_seo']
	)
);
echo RedshopLayoutHelper::render(
	'config.config',
	array(
		'title' => JText::_('COM_REDSHOP_SEO_PAGE_LANGAUGE_LBL'),
		'desc'  => JText::_('COM_REDSHOP_TOOLTIP_SEO_PAGE_LANGAUGE'),
		'field' => '<textarea class="form-control" name="seo_page_language" id="seo_page_language" rows="4" cols="40"/>'
			. stripslashes($this->config->get('SEO_PAGE_LANGAUGE')) . '</textarea>',
		'line'  => false
	)
);
