<?php
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');


class JFormFieldBlogPost extends JFormFieldList
{

	public $type = 'BlogPost';

	protected function getInput()
	{
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		//$attr .= $this->multiple ? ' multiple="multiple"' : '';

		// Initialize JavaScript field attributes.
		//$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		// Get the field options.
		$options = (array) $this->getOptions();
		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true') {
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}
		// Create a regular list.
		else {
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		}

		return implode($html);
	}
	
	protected function checkPrivacy($id=0)
	{
		$db	= JFactory::getDbo();
			$query	= $db->getQuery(true);

			$query->select('`comments`,`jspostgroup`');
			$query->from('#__joomblog_privacy');
			$query->where('`postid`='.$id);
			$query->where('`isblog`=1');
			$db->setQuery($query);			
			$perm = $db->loadObject();
			$postper = $perm->comments;
			$check = JFactory::getApplication()->isAdmin();
			if ($check) return true;
			if ($postper==3) return false;
			if ($postper==2) return $this->isFriends($id);
			if ($postper==4) return $this->inJSgroup($perm->jspostgroup);
			return true;		
	}
	
	function inJSgroup($gid=0)
	{
		$id = JFactory::getUser()->id;
		$db	= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('groupid');
		$query->from('#__community_groups_members');
		$query->where('groupid='.(int)$gid);
		$query->where('memberid='.(int)$id);
		$db->setQuery($query);
		if ($db->loadResult()) return true; else return false;
	}
	
	function isFriends($id=0)
	{
		if (!$id) return false;
		$db	=& JFactory::getDBO();
		$user =& JFactory::getUser();
		$id1=$user->id;
		
		$db->setQuery(" SELECT `user_id`  FROM `#__joomblog_list_blogs` WHERE id=".$id);
		$id2=$db->loadResult();
		if ($id1 && $id2)
		{
		$db->setQuery(	" SELECT `connection_id` FROM `#__community_connection` " .
						" WHERE connect_from=".(int)$id1." AND connect_to=".(int)$id2." AND `status`=1 ");
		$frindic = $db->loadResult();
		
		if ($frindic) return true; else return false;
		}
		return false;				
	}
	/**
	 * Method to get the field options.
	 */
	protected function getOptions()
	{
		// Initialise variables.
		$options	= array();
		$published	= (string) $this->element['published'];
		// Get the current user object.
			$user = JFactory::getUser();
			
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);

			$query->select('`id` AS `value`');
			$query->select('`title` AS `text`');
			$query->from('#__joomblog_list_blogs');
			$db->setQuery($query);			
			$options = $db->loadObjectList();
			
			foreach($options as $i => $option)
				{
					if ($user->authorise('core.create', 'com_joomblog.blog.'.$option->value) != true ) {
						unset($options[$i]);
					}
					
					if (!$this->checkPrivacy($option->value)) unset($options[$i]);;
					
				}		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}