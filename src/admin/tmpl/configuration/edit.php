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

/** @var \Seven\Component\Seven\Administrator\View\Configuration\HtmlView $this */

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');
?>
<form action="<?php echo Route::_('index.php?option=com_seven&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate">

    <div class="main-card">
        <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'details', 'recall' => true, 'breakpoint' => 768]); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_SEVEN_CONFIGURATION')); ?>
        <div class="row">
            <div class="col-lg-9">
                <fieldset class="adminform">
                    <?php echo $this->form->renderField('api_key'); ?>
                    <?php echo $this->form->renderField('published'); ?>
                </fieldset>
            </div>
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h4 class="alert-heading"><?php echo Text::_('COM_SEVEN_API_KEY_INFO_TITLE'); ?></h4>
                            <p><?php echo Text::_('COM_SEVEN_API_KEY_INFO_DESC'); ?></p>
                            <hr>
                            <p class="mb-0">
                                <a href="https://app.seven.io/developer" target="_blank" rel="noopener noreferrer" class="alert-link">
                                    <?php echo Text::_('COM_SEVEN_GET_API_KEY'); ?>
                                    <span class="icon-external-link" aria-hidden="true"></span>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>

        <input type="hidden" name="task" value="">
        <?php echo $this->form->renderField('id'); ?>
        <?php echo HTMLHelper::_('form.token'); ?>
    </div>
</form>
