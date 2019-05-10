<?php
/**
 * jUpgradePro
 *
 * @version   $Id:
 * @package   jUpgradePro
 * @copyright Copyright (C) 2004 - 2019 Matware. All rights reserved.
 * @author    Matias Aguirre
 * @email     maguirre@matware.com.ar
 * @link      http://www.matware.com.ar/
 * @license   GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('behavior.keepalive');

// Import CSS
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'media/com_jupgradepro/css/form.css');

$id = isset($this->item->id) ? $this->item->id : 0;

?>
<script type="text/javascript">

    js = jQuery.noConflict();

    js(document).ready(function () {

    });

    <?php
        if (JVersion::MAJOR_VERSION !== 4) {
    ?>

    Joomla.submitbutton = function (task) {
        if (task == 'site.cancel') {
            Joomla.submitform(task, document.getElementById('site-form'));
        } else {

            if (task != 'site.cancel' && document.formvalidator.isValid(document.id('site-form'))) {

                Joomla.submitform(task, document.getElementById('site-form'));
            } else {
                alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }

    <?php
        }
    ?>

</script>

<form action="<?php echo Route::_('index.php?option=com_jupgradepro&layout=edit&id=' . (int) $id); ?>"
      method="post" enctype="multipart/form-data" name="adminForm" id="site-form" class="form-validate">

    <div class="form-horizontal">
		<?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'general', Text::_('COM_JUPGRADEPRO_TITLE_GLOBAL', true)); ?>
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

                    <input type="hidden" name="jform[id]"
                           value="<?php echo isset($this->item->id) ? $this->item->id : 0; ?>"/>
                    <input type="hidden" name="jform[ordering]"
                           value="<?php isset($this->item->ordering) ? $this->item->ordering : 0; ?>"/>
                    <input type="hidden" name="jform[state]"
                           value="<?php isset($this->item->state) ? $this->item->state : 0; ?>"/>
                    <input type="hidden" name="jform[checked_out]"
                           value="<?php echo isset($this->item->checked_out) ? $this->item->checked_out : 0; ?>"/>
                    <input type="hidden" name="jform[checked_out_time]"
                           value="<?php echo isset($this->item->checked_out_time) ? $this->item->checked_out_time : 0; ?>"/>

                </fieldset>
            </div>
        </div>
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>

		<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'restful', Text::_('COM_JUPGRADEPRO_TITLE_RESTFUL', true)); ?>
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
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>

		<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'database', Text::_('COM_JUPGRADEPRO_TITLE_DATABASE', true)); ?>
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
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>

		<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'skips', Text::_('COM_JUPGRADEPRO_TITLE_SKIPS', true)); ?>
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
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>

		<?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>

        <input type="hidden" name="task" value=""/>
		<?php echo HTMLHelper::_('form.token'); ?>

    </div>
</form>
