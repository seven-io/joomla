<?php
/**
 * @package sms77api
 * @author sms77 e.K. <support@sms77.io>
 * @copyright  2020-present
 * @license    MIT; see LICENSE.txt
 * @link       http://sms77.io
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

defined('_JEXEC') or die;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('formbehavior.chosen');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirection = $this->escape($this->state->get('list.direction'));
$loggedInUser = Factory::getUser();
?>
<form action="index.php?option=com_sms77api&view=messages" method="post" name="adminForm"
      id="adminForm" class="form-validate form-horizontal">
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>

    <div id="j-main-container" class="span10">
        <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

        <?php if (empty($this->messages)) : ?>
            <div class="alert alert-no-items">
                <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
        <?php else : ?>
            <table class="table table-striped" id="itemsList">
                <thead>
                <tr>
                    <th class="left">
                        <?php echo Text::_('COM_SMS77API_ID'); ?>
                    </th>
                    <th class="left">
                        <?php echo Text::_('COM_SMS77API_CREATED'); ?>
                    </th>
                    <th class="left">
                        <?php echo Text::_('COM_SMS77API_RESPONSE'); ?>
                    </th>
                    <th class="left">
                        <?php echo Text::_('COM_SMS77API_CONFIGURATION'); ?>
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
                <?php foreach ($this->messages as $i => $message) : ?>
                    <tr>
                        <td class="center">
                            <?php echo $this->escape($message->id) ?>
                        </td>
                        <td class="center">
                            <?php echo $this->escape($message->created) ?>
                        </td>
                        <td>
                            <div class="name break-word">
                                <?php echo $this->escape($message->response); ?>
                            </div>
                        </td>
                        <td>
                            <div class="name break-word">
                                <?php echo $this->escape($message->config); ?>
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