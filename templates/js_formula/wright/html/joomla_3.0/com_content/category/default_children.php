<?php
// no direct access
defined('_JEXEC') or die;
$class = ' class="first"';
?>

<?php if (count($this->children[$this->category->id]) > 0) : ?>
	<ul>
	<?php foreach($this->children[$this->category->id] as $id => $child) : ?>
		<?php
		if ($this->params->get('show_empty_categories') || $child->getNumItems(true) || count($child->getChildren())) :
			if (!isset($this->children[$this->category->id][$id + 1])) :
				$class = ' class="last"';
			endif;
		?>

		<li<?php echo $class; ?>>
			<?php $class = ''; ?>
			<?php if ( $this->params->get('show_cat_num_articles',1)) : ?>
			<span class="article-count small"><?php echo JText::_('COM_CONTENT_NUM_ITEMS') ; ?> <?php echo $child->getNumItems(true); ?></span>
			<?php endif ; ?>
			
			<span class="item-title"><i class="icon-folder-open"></i><a href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($child->id));?>">
				<?php echo $this->escape($child->title); ?></a>
			</span>
			<?php if ($this->params->get('show_subcat_desc') == 1) :?>
			<?php if ($child->description) : ?>
				<div class="category-desc">
					<?php echo JHtml::_('content.prepare', $child->description); ?>
				</div>
			<?php endif; ?>
			<?php endif; ?>

			<?php if (count($child->getChildren()) > 0 ) :
				$this->children[$child->id] = $child->getChildren();
				$this->category = $child;
				$this->maxLevel--;
				if ($this->maxLevel != 0) :
					include('default_children.php');
				endif;
				$this->category = $child->getParent();
				$this->maxLevel++;
			endif; ?>
			</li>
		<?php endif; ?>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>
