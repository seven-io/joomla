<?php
/**
 * @package     Seven
 * @subpackage  com_seven
 *
 * @copyright   Copyright (C) seven communications GmbH & Co. KG. All rights reserved.
 * @license     MIT
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var \Seven\Component\Seven\Administrator\View\Automations\HtmlView $this */

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo Route::_('index.php?option=com_seven&view=automations'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-info">
                        <span class="icon-info-circle" aria-hidden="true"></span>
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else : ?>
                    <table class="table" id="automationList">
                        <caption class="visually-hidden">
                            <?php echo Text::_('COM_SEVEN_AUTOMATIONS_TABLE_CAPTION'); ?>
                        </caption>
                        <thead>
                            <tr>
                                <td class="w-1 text-center">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                </td>
                                <th scope="col" class="w-1 text-center">
                                    <?php echo Text::_('JSTATUS'); ?>
                                </th>
                                <th scope="col">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_SEVEN_AUTOMATION_TITLE', 'a.title', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-20 d-none d-md-table-cell">
                                    <?php echo Text::_('COM_SEVEN_AUTOMATION_TRIGGER'); ?>
                                </th>
                                <th scope="col" class="w-10 text-center d-none d-md-table-cell">
                                    <?php echo Text::_('COM_SEVEN_STATISTICS'); ?>
                                </th>
                                <th scope="col" class="w-15 d-none d-md-table-cell">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_SEVEN_CREATED', 'a.created', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-5 d-none d-md-table-cell">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->items as $i => $item) : ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                    <td class="text-center">
                                        <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($item->enabled) : ?>
                                            <span class="icon-publish text-success" aria-hidden="true" title="<?php echo Text::_('JENABLED'); ?>"></span>
                                            <span class="visually-hidden"><?php echo Text::_('JENABLED'); ?></span>
                                        <?php else : ?>
                                            <span class="icon-unpublish text-danger" aria-hidden="true" title="<?php echo Text::_('JDISABLED'); ?>"></span>
                                            <span class="visually-hidden"><?php echo Text::_('JDISABLED'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo Route::_('index.php?option=com_seven&task=automation.edit&id=' . $item->id); ?>">
                                            <?php echo $this->escape($item->title); ?>
                                        </a>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <?php
                                        $triggerLabel = $this->triggerLabels[$item->trigger_type] ?? $item->trigger_type;
                                        $badgeClass = strpos($item->trigger_type, 'vm_') === 0 ? 'bg-primary' : 'bg-secondary';
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?>">
                                            <?php echo $this->escape($triggerLabel); ?>
                                        </span>
                                    </td>
                                    <td class="text-center d-none d-md-table-cell">
                                        <?php
                                        $total = (int) ($item->total_sent ?? 0);
                                        $success = (int) ($item->success_count ?? 0);
                                        $failed = $total - $success;
                                        ?>
                                        <?php if ($total > 0) : ?>
                                            <span class="badge bg-success" title="<?php echo Text::_('COM_SEVEN_SUCCESSFUL'); ?>">
                                                <?php echo $success; ?>
                                            </span>
                                            <?php if ($failed > 0) : ?>
                                            <span class="badge bg-danger" title="<?php echo Text::_('COM_SEVEN_FAILED'); ?>">
                                                <?php echo $failed; ?>
                                            </span>
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC4')); ?>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <?php echo $item->id; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php echo $this->pagination->getListFooter(); ?>
                <?php endif; ?>

                <input type="hidden" name="task" value="">
                <input type="hidden" name="boxchecked" value="0">
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>
