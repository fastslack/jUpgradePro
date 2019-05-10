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

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

// Import CSS
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'administrator/components/com_jupgradepro/assets/css/jupgradepro.css');
$document->addStyleSheet(Uri::root() . 'media/com_jupgradepro/css/list.css');

$user      = Factory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_jupgradepro');
$saveOrder = $listOrder == 'a.`ordering`';
/*
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_jupgradepro&task=sites.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'productList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$sortFields = $this->getSortFields();
*/

?>

<form action="<?php echo Route::_('index.php?option=com_jupgradepro&view=sites'); ?>" method="post"
      name="adminForm" id="adminForm">
	<?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
		<?php //echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
		<?php else : ?>
        <div id="j-main-container">
			<?php endif; ?>

			<?php //echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

            <div class="clearfix"></div>
            <table class="table table-striped" id="productList">
                <thead>
                <tr>
					<?php if (isset($this->items[0]->ordering)): ?>
                        <!--
						<th width="1%" class="nowrap center hidden-phone">
                            <?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                        </th>-->
					<?php endif; ?>
                    <th width="1%" class="hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value=""
                               title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
                    </th>
					<?php if (isset($this->items[0]->state)): ?>
                        <th width="1%" class="nowrap center">
							<?php echo Html::_('searchtools.sort', 'JSTATUS', 'a.`state`', $listDirn, $listOrder); ?>
                        </th>
					<?php endif; ?>

                    <th class='left' width="2%">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JUPGRADEPRO_SITES_ID', 'a.`id`', $listDirn, $listOrder); ?>
                    </th>
                    <th class='left' width="10%">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JUPGRADEPRO_SITES_NAME', 'a.`name`', $listDirn, $listOrder); ?>
                    </th>
                    <th class='left' width="20%">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JUPGRADEPRO_SITES_GENERAL_INFO', 'a.`restful`', $listDirn, $listOrder); ?>
                    </th>
                    <th class='left' width="20%">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JUPGRADEPRO_SITES_METHOD', 'a.`mysql`', $listDirn, $listOrder); ?>
                    </th>
                    <th class='left' width="20%">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JUPGRADEPRO_SITES_SKIPS', 'a.`skips`', $listDirn, $listOrder); ?>
                    </th>

                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
						<?php //echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
                </tfoot>
                <tbody>
				<?php foreach ($this->items as $i => $item) :

					$ordering = ($listOrder == 'a.ordering');
					$canCreate = $user->authorise('core.create', 'com_jupgradepro');
					$canEdit = $user->authorise('core.edit', 'com_jupgradepro');
					$canCheckin = $user->authorise('core.manage', 'com_jupgradepro');
					$canChange = $user->authorise('core.edit.state', 'com_jupgradepro');

					$info                = array();
					$info['method']      = $item->method;
					$info['chunk_limit'] = $item->chunk_limit;
					$info['keep_ids']    = $item->keep_ids;

					?>
                    <tr class="row<?php echo $i % 2; ?>">

						<?php if (isset($this->items[0]->ordering)) : ?>
                            <!--
							<td class="order nowrap center hidden-phone">
								<?php if ($canChange) :
								$disableClassName = '';
								$disabledLabel = '';

								if (!$saveOrder) :
									$disabledLabel    = Text::_('JORDERINGDISABLED');
									$disableClassName = 'inactive tip-top';
								endif; ?>
									<span class="sortable-handler hasTooltip <?php echo $disableClassName ?>"
										  title="<?php echo $disabledLabel ?>">
							<i class="icon-menu"></i>
						</span>
									<input type="text" style="display:none" name="order[]" size="5"
										   value="<?php echo $item->ordering; ?>" class="width-20 text-area-order "/>
								<?php else : ?>
									<span class="sortable-handler inactive">
							<i class="icon-menu"></i>
						</span>
								<?php endif; ?>
							</td>
						-->
						<?php endif; ?>
                        <td class="hidden-phone">
							<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                        </td>
						<?php if (isset($this->items[0]->state)): ?>
                            <td class="center">
								<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'sites.', $canChange, 'cb'); ?>
                            </td>
						<?php endif; ?>

                        <td>

							<?php echo $item->id; ?>
                        </td>
                        <td>
							<?php if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) : ?>
								<?php //echo JHtml::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'sites.', $canCheckin); ?>
							<?php endif; ?>
							<?php if ($canEdit) : ?>
                                <a href="<?php echo Route::_('index.php?option=com_jupgradepro&task=site.edit&id=' . (int) $item->id); ?>">
									<?php echo $this->escape($item->name); ?></a>
							<?php else : ?>
								<?php echo $this->escape($item->name); ?>
							<?php endif; ?>
                        </td>
                        <td>
                            <b>☘ <?php echo Text::_('COM_JUPGRADEPRO_SITES_GENERAL_INFO'); ?></b><br/>
							<?php echo $this->fixJSON(json_encode($info)); ?>
                        </td>
                        <td>
                            <b><?php echo Text::_('COM_JUPGRADEPRO_SITES_DATABASE'); ?></b><br/>
							<?php echo $this->fixJSON($item->database); ?>
                            <b><?php echo Text::_('COM_JUPGRADEPRO_SITES_RESTFUL'); ?></b><br/>
							<?php echo $this->fixJSON($item->restful); ?>
                        </td>
                        <td>
                            <b>☘ <?php echo Text::_('COM_JUPGRADEPRO_SITES_SKIPS'); ?></b><br/>
							<?php echo $this->fixJSON($item->skips); ?>
                        </td>

                    </tr>
				<?php endforeach; ?>
                </tbody>
            </table>

            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="boxchecked" value="0"/>
            <input type="hidden" name="list[fullorder]" value="<?php echo $listOrder; ?> <?php echo $listDirn; ?>"/>
			<?php echo HTMLHelper::_('form.token'); ?>
        </div>
</form>

<script>
    window.toggleField = function (id, task, field) {

        var f = document.adminForm, i = 0, cbx, cb = f[id];

        if (!cb) return false;

        while (true) {
            cbx = f['cb' + i];

            if (!cbx) break;

            cbx.checked = false;
            i++;
        }

        var inputField = document.createElement('input');

        inputField.type = 'hidden';
        inputField.name = 'field';
        inputField.value = field;
        f.appendChild(inputField);

        cb.checked = true;
        f.boxchecked.value = 1;
        window.submitform(task);

        return false;
    };
</script>
