<?php
/**
 * @package     Seven
 * @subpackage  com_seven
 *
 * @copyright   Copyright (C) seven communications GmbH & Co. KG. All rights reserved.
 * @license     MIT
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Seven\Component\Seven\Administrator\Helper\SevenHelper;

/** @var \Seven\Component\Seven\Administrator\View\Configurations\HtmlView $this */

$isConnected = SevenHelper::isConnected();
$token = Session::getFormToken();
?>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <span class="icon-plug" aria-hidden="true"></span>
                    <?php echo Text::_('COM_SEVEN_OAUTH_CONNECTION'); ?>
                </h3>
            </div>
            <div class="card-body">
                <?php if ($isConnected) : ?>
                    <div class="alert alert-success">
                        <span class="icon-check-circle" aria-hidden="true"></span>
                        <?php echo Text::_('COM_SEVEN_OAUTH_STATUS_CONNECTED'); ?>
                    </div>
                    <p><?php echo Text::_('COM_SEVEN_OAUTH_CONNECTED_DESC'); ?></p>
                    <a href="<?php echo Route::_('index.php?option=com_seven&task=oauth.disconnect&' . $token . '=1'); ?>"
                       class="btn btn-danger"
                       onclick="return confirm('<?php echo Text::_('COM_SEVEN_OAUTH_DISCONNECT_CONFIRM'); ?>');">
                        <span class="icon-unlink" aria-hidden="true"></span>
                        <?php echo Text::_('COM_SEVEN_OAUTH_DISCONNECT'); ?>
                    </a>
                <?php else : ?>
                    <div class="alert alert-warning">
                        <span class="icon-exclamation-triangle" aria-hidden="true"></span>
                        <?php echo Text::_('COM_SEVEN_OAUTH_STATUS_NOT_CONNECTED'); ?>
                    </div>
                    <p><?php echo Text::_('COM_SEVEN_OAUTH_CONNECT_DESC'); ?></p>
                    <a href="<?php echo Route::_('index.php?option=com_seven&task=oauth.connect&' . $token . '=1'); ?>"
                       class="btn btn-primary btn-lg">
                        <span class="icon-link" aria-hidden="true"></span>
                        <?php echo Text::_('COM_SEVEN_OAUTH_CONNECT'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <span class="icon-info-circle" aria-hidden="true"></span>
                    <?php echo Text::_('COM_SEVEN_OAUTH_INFO_TITLE'); ?>
                </h3>
            </div>
            <div class="card-body">
                <p><?php echo Text::_('COM_SEVEN_OAUTH_INFO_DESC'); ?></p>
                <ul>
                    <li><?php echo Text::_('COM_SEVEN_OAUTH_INFO_SECURE'); ?></li>
                    <li><?php echo Text::_('COM_SEVEN_OAUTH_INFO_NO_PASSWORD'); ?></li>
                    <li><?php echo Text::_('COM_SEVEN_OAUTH_INFO_REVOKE'); ?></li>
                </ul>
                <a href="https://www.seven.io" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm">
                    <span class="icon-external-link" aria-hidden="true"></span>
                    <?php echo Text::_('COM_SEVEN_VISIT_SEVEN'); ?>
                </a>
            </div>
        </div>
    </div>
</div>
