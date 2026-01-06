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
use Seven\Component\Seven\Administrator\Helper\SevenHelper;

/** @var \Seven\Component\Seven\Administrator\View\Message\HtmlView $this */

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

$isNew = ($this->item->id == 0);
$hasVirtueMart = SevenHelper::hasVirtueMart();
?>
<form action="<?php echo Route::_('index.php?option=com_seven&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate">

    <div class="main-card">
        <?php if ($isNew) : ?>
            <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'details', 'recall' => true, 'breakpoint' => 768]); ?>

            <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_SEVEN_WRITE_SMS')); ?>
            <div class="row">
                <div class="col-lg-9">
                    <fieldset class="adminform">
                        <?php echo $this->form->renderField('to'); ?>
                        <?php echo $this->form->renderField('text'); ?>
                        <?php echo $this->form->renderField('from'); ?>
                    </fieldset>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h4 class="alert-heading"><?php echo Text::_('COM_SEVEN_SMS_INFO_TITLE'); ?></h4>
                                <p><?php echo Text::_('COM_SEVEN_SMS_INFO_DESC'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>

            <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'options', Text::_('COM_SEVEN_OPTIONS')); ?>
            <div class="row">
                <div class="col-lg-9">
                    <fieldset class="adminform">
                        <?php echo $this->form->renderField('flash'); ?>
                        <?php echo $this->form->renderField('unicode'); ?>
                        <?php echo $this->form->renderField('utf8'); ?>
                        <?php echo $this->form->renderField('performance_tracking'); ?>
                        <?php echo $this->form->renderField('no_reload'); ?>
                        <?php echo $this->form->renderField('delay'); ?>
                        <?php echo $this->form->renderField('ttl'); ?>
                        <?php echo $this->form->renderField('foreign_id'); ?>
                        <?php echo $this->form->renderField('label'); ?>
                        <?php echo $this->form->renderField('udh'); ?>
                    </fieldset>
                </div>
            </div>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>

            <?php if ($hasVirtueMart) : ?>
                <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'virtuemart', Text::_('COM_SEVEN_VIRTUEMART')); ?>
                <div class="row">
                    <div class="col-lg-9">
                        <fieldset class="adminform">
                            <div class="alert alert-warning">
                                <?php echo Text::_('COM_SEVEN_TO_IGNORED_IF_SHOPPER_GROUPS'); ?>
                            </div>
                            <?php echo $this->form->renderField('country_id'); ?>
                            <?php echo $this->form->renderField('shopper_group_id'); ?>
                        </fieldset>
                    </div>
                </div>
                <?php echo HTMLHelper::_('uitab.endTab'); ?>
            <?php endif; ?>

            <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
        <?php else : ?>
            <!-- View existing message -->
            <div class="row">
                <div class="col-lg-12">
                    <h2><?php echo Text::_('COM_SEVEN_MESSAGE_DETAILS'); ?></h2>

                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th scope="row" style="width: 200px;"><?php echo Text::_('COM_SEVEN_TO_LABEL'); ?></th>
                                <td><?php echo $this->escape($this->item->recipient); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo Text::_('COM_SEVEN_TEXT_LABEL'); ?></th>
                                <td><?php echo nl2br($this->escape($this->item->text)); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo Text::_('COM_SEVEN_FROM'); ?></th>
                                <td><?php echo $this->escape($this->item->sender ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo Text::_('COM_SEVEN_CODE'); ?></th>
                                <td>
                                    <?php
                                    $statusCode = (int) $this->item->response_code;
                                    $badgeClass = ($statusCode >= 100 && $statusCode < 200) ? 'bg-success' : 'bg-danger';
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>">
                                        <?php echo $statusCode; ?>
                                    </span>
                                    <small class="text-muted">
                                        <?php echo $this->escape(SevenHelper::getSmsStatusDescription($statusCode)); ?>
                                    </small>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo Text::_('COM_SEVEN_RESPONSE'); ?></th>
                                <td><pre class="mb-0"><?php echo $this->escape($this->item->response); ?></pre></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo Text::_('COM_SEVEN_CREATED'); ?></th>
                                <td><?php echo HTMLHelper::_('date', $this->item->created, Text::_('DATE_FORMAT_LC2')); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <input type="hidden" name="task" value="">
        <?php echo $this->form->renderField('id'); ?>
        <?php echo HTMLHelper::_('form.token'); ?>
    </div>
</form>
