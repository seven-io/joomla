<?php
/**
 * @package seven
 * @author seven communications GmbH & Co. KG <support@seven.io>
 * @copyright  2020-present seven communications GmbH & Co. KG
 * @license    MIT; see LICENSE.txt
 * @link       http://www.seven.io
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('formbehavior.chosen');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirection = $this->escape($this->state->get('list.direction'));
$loggedInUser = Factory::getUser();
?>
<form action="index.php?option=com_seven&view=configurations" method="post" name="adminForm"
      id="adminForm" class="form-validate form-horizontal">
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>

    <div id="j-main-container" class="span10">
        <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

        <?php if (empty($this->configurations)) : ?>
            <div class="alert alert-no-items">
                <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
        <?php else : ?>
            <table class="table table-striped" id="itemsList">
                <thead>
                <tr>
                    <th width="1%" class="nowrap center">
                        <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'configurations.published', $listDirection, $listOrder); ?>
                    </th>
                    <th class="left">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_SEVEN_CONFIGURATION_API_KEY', 'configurations.id', $listDirection, $listOrder); ?>
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
                <?php
                $canEdit = $this->canDo->get('core.edit');
                $canChange = $loggedInUser->authorise('core.edit.state', 'com_seven');

                foreach ($this->configurations as $i => $configuration) :
                    ?>
                    <tr>
                        <td class="center">
                            <div class="btn-group">
                                <?php echo HTMLHelper::_('jgrid.published', $configuration->published, $i, 'configurations.', $canChange); ?>
                            </div>
                        </td>
                        <td>
                            <div class="name break-word">
                                <?php if ($canEdit) : ?>
                                    <a href="<?php echo Route::_('index.php?option=com_seven&task=configuration.edit&id=' . (int)$configuration->id); ?>">
                                        <?php echo $this->escape($configuration->api_key); ?></a>
                                <?php else : ?>
                                    <?php echo $this->escape($configuration->api_key); ?>
                                <?php endif; ?>

                                <small style='display: block;'><?php echo $this->escape($configuration->updated); ?></small>
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
