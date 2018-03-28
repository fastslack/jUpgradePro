<?php
/**
 * jUpgradePro
 *
 * @version $Id:
 * @package jUpgradePro
 * @copyright Copyright (C) 2004 - 2018 Matware. All rights reserved.
 * @author Matias Aguirre
 * @email maguirre@matware.com.ar
 * @link http://www.matware.com.ar/
 * @license GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'media/com_jupgradepro/css/form.css');

$id = isset($this->item->id) ? $this->item->id : 0;

?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {

	});

	Joomla.submitbutton = function (task) {
		if (task == 'site.cancel') {
			Joomla.submitform(task, document.getElementById('site-form'));
		}
		else {

			if (task != 'site.cancel' && document.formvalidator.isValid(document.id('site-form'))) {

				Joomla.submitform(task, document.getElementById('site-form'));
			}
			else {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}

</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_jupgradepro&layout=edit&id=' . (int) $id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="site-form" class="form-validate">

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_JUPGRADEPRO_TITLE_GLOBAL', true)); ?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">

					<fieldset>
					<?php foreach ($this->form->getFieldset('global') as $name => $field) : ?>
					  <div class="control-group">
					     <div class="control-label">
					        <?php echo $field->label; ?>
					     </div>
					     <div class="controls">
					        <?php echo $field->input; ?>
					     </div>
					  </div>
					<?php endforeach; ?>
					</fieldset>


					<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
					<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
					<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
					<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
					<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'restful', JText::_('COM_JUPGRADEPRO_TITLE_RESTFUL', true)); ?>
		<fieldset>
		<?php foreach ($this->form->getFieldset('restful') as $name => $field) : ?>
			<div class="control-group">
				<div class="control-label">
					<?php echo $field->label; ?>
				</div>
				<div class="controls">
					<?php echo $field->input; ?>
				</div>
			</div>
		<?php endforeach; ?>
		</fieldset>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'database', JText::_('COM_JUPGRADEPRO_TITLE_DATABASE', true)); ?>
		<fieldset>
		<?php foreach ($this->form->getFieldset('database') as $name => $field) : ?>
		  <div class="control-group">
	     <div class="control-label">
	        <?php echo $field->label; ?>
	     </div>
	     <div class="controls">
	        <?php echo $field->input; ?>
	     </div>
		  </div>
		<?php endforeach; ?>
		</fieldset>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'skips', JText::_('COM_JUPGRADEPRO_TITLE_SKIPS', true)); ?>
		<fieldset>
		<?php foreach ($this->form->getFieldset('skips') as $name => $field) : ?>
		  <div class="control-group">
	     <div class="control-label">
	        <?php echo $field->label; ?>
	     </div>
	     <div class="controls">
	        <?php echo $field->input; ?>
	     </div>
		  </div>
		<?php endforeach; ?>
		</fieldset>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>

	</div>
</form>
