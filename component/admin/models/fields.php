<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2017 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

class RedshopModelFields extends RedshopModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_fields';

	/**
	 * Construct class
	 *
	 * @since 1.x
	 */
	public function __construct()
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'f.id',
				'ordering', 'f.ordering',
				'title', 'f.title',
				'name', 'f.name',
				'type', 'f.type',
				'section', 'f.section',
				'published', 'f.published'
			);
		}

		parent::__construct($config);
	}

		/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.5
	 */
	protected function getStoreId($id = '')
	{
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.type');
		$id .= ':' . $this->getState('filter.section');

		return parent::getStoreId($id);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @note    Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$field_type = $this->getUserStateFromRequest($this->context . '.filter.field_type', 'filter_field_type');
		$this->setState('filter.field_type', $field_type);

		$field_section = $this->getUserStateFromRequest($this->context . '.filter.field_section', 'filter_field_section');
		$this->setState('filter.field_section', $field_section);

		parent::populateState('ordering', $direction);
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	public function getListQuery()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('f.*')
			->from($db->qn('#__redshop_fields', 'f'));

		// Filter by search in name.
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where($db->qn('f.id') . ' = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->q('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where($db->qn('f.title') . ' LIKE ' . $search);
			}
		}

		// Filter: Field type
		$filterFieldType = $this->getState('filter.field_type', '');

		if ($filterFieldType)
		{
			$query->where($db->qn('f.type') . ' = ' . $db->q($filterFieldType));
		}

		// Filter: Field section
		$filterFieldSection = $this->getState('filter.field_section', '');

		if ($filterFieldSection)
		{
			$query->where($db->qn('f.section') . ' = ' . $filterFieldSection);
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'ordering');
		$orderDirn = $this->state->get('list.direction', 'asc');

		if ($orderCol == 'ordering')
		{
			$query->order($db->escape('f.section, f.ordering ' . $orderDirn));
		}
		else
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn) . ', f.section, f.ordering');
		}

		return $query;
	}

	/**
	 * Get Fields information from Sections Ids.
	 * 		Note: This will return non-published fields also
	 *
	 * @param   array  $section  Sections Ids in index array
	 *
	 * @return  mixed  Object information array of Fields
	 */
	public function getFieldInfoBySection($section)
	{
		if (!is_array($section))
		{
			throw new InvalidArgumentException(__FUNCTION__ . 'only accepts Array. Input was ' . $section);
		}

		JArrayHelper::toInteger($section);
		$sections = implode(',', $section);

		// Initialiase variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select('f.name,f.type,f.section')
			->from($db->qn('#__redshop_fields', 'f'))
			->where($db->qn('f.section') . ' IN(' . $sections . ')');

		// Set the query and load the result.
		$db->setQuery($query);

		try
		{
			$fields = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException($e->getMessage(), $e->getCode());
		}

		return $fields;
	}

	public function getFieldsBySection($section, $fieldName = '')
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
					->select('*')
					->from($db->qn('#__redshop_fields', 'f'))
					->where($db->qn('f.section') . ' = ' . (int) $section)
					->where($db->qn('f.published') . '= 1 ');

		if ($fieldName != '')
		{
			$fieldName = redhelper::quote(explode(',', $fieldName));
			$query->where($db->qn('f.name') . ' IN (' . implode(',', $fieldName) . ') ');
		}

		$query->order($db->qn('f.ordering'));
		$db->setQuery($query);

		return $db->loadObjectlist();
	}

	public function getFieldDataList($fieldid, $section = 0, $orderitemid = 0, $user_email = "")
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
					->select('*')
					->from($db->qn('#__redshop_fields_data'))
					->where($db->qn('itemid') . ' = ' . (int) $orderitemid)
					->where($db->qn('fieldid') . ' = ' . (int) $fieldid)
					->where($db->qn('user_email') . ' = ' . $db->q($user_email))
					->where($db->qn('section') . ' = ' . (int) $section);

		$db->setQuery($query);

		return $db->loadObject();
	}

	public function getFieldValue($id)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
					->select('*')
					->from($db->qn('#__redshop_fields_value'))
					->where($db->qn('field_id') . ' = ' . (int) $id)
					->order($db->qn('field_id') . ' ASC');

		$db->setQuery($query);

		return $db->loadObjectlist();
	}
}
