<?php
/**
 * @package seven
 * @author seven communications GmbH & Co. KG <support@seven.io>
 * @copyright  2020-present seven communications GmbH & Co. KG
 * @license    MIT; see LICENSE.txt
 * @link       http://www.seven.io
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

defined('_JEXEC') or die;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('formbehavior.chosen');
?>
<form action="index.php?option=com_seven&view=messages" method="post" name="adminForm"
      id="adminForm" class="form-validate form-horizontal">
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>

    <div id="j-main-container" class="span10">
        <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

        <?php if (empty($this->_entities)) : ?>
            <div class="alert alert-no-items">
                <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
        <?php else : ?>
            <table class="table table-striped" id="itemsList">
                <thead>
                <tr>
                    <th class="left">
                        <?php echo Text::_('COM_SEVEN_ID'); ?>
                    </th>
                    <th class="left">
                        <?php echo Text::_('COM_SEVEN_CREATED'); ?>
                    </th>
                    <th class="left">
                        <?php echo Text::_('COM_SEVEN_RESPONSE'); ?>
                    </th>
                    <th class="left">
                        <?php echo Text::_('COM_SEVEN_CONFIGURATION'); ?>
                    </th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td colspan="15">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
                </tfoot>
                <tbody>
                <?php foreach ($this->_entities as $i => $e) : ?>
                    <tr>
                        <td class="center">
                            <?php echo $this->escape($e->id) ?>
                        </td>
                        <td class="center">
                            <?php echo $this->escape($e->created) ?>
                        </td>
                        <td>
                            <div class="name break-word">
                                <?php echo $this->escape($e->response); ?>
                            </div>
                        </td>
                        <td>
                            <div class="name break-word">
                                <?php echo $this->escape($e->config); ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
