<?php
/**
 * @package   sms77api
 * @author     sms77 e.K. <support@sms77.io>
 * @copyright  sms77 e.K.
 * @license    MIT; see LICENSE.txt
 * @link     support@sms77.io
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen');

Factory::getDocument()->addScriptDeclaration(<<<JS
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('adminForm');
            const to = document.getElementById('jform_to');
            const shopperGroupId = document.getElementById('jform_shopper_group_id');
            const countryId = document.getElementById('jform_country_id');
                        
            const ignoreTo = () => '' !== shopperGroupId.value || '' !== countryId.value;
            		
            shopperGroupId.addEventListener('change', () => to.disabled = ignoreTo());
            countryId.addEventListener('change', () => to.disabled = ignoreTo());
            
            Joomla.submitbutton = task => {
                if (ignoreTo()) {
                    to.value = '';
                }
                
                if (task === 'message.cancel' || document.formvalidator.isValid(form)) {
                    Joomla.submitform(task, form);
                }
            };
        });
JS
);
$hasVirtueMart = JComponentHelper::isEnabled('com_virtuemart');
$fields = ['text', 'configuration', 'to', 'from', 'foreign_id'];
if ($hasVirtueMart) {
    $fields[] = 'shopper_group_id';

    if (!class_exists('VmConfig')) {
        require JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php';
    }

    VmConfig::loadConfig();

    $lang = JFactory::getLanguage();
    $lang->load('com_virtuemart', JPATH_ADMINISTRATOR . '/components/com_virtuemart');
    $lang->load('com_virtuemart_shoppers', JPATH_ROOT . '/components/com_virtuemart');
}
?>
<form action="<?php echo Route::_("index.php?option=com_sms77api&layout=edit&id={$this->message->id}") ?>"
      method="post" name="adminForm" enctype="multipart/form-data" id="adminForm"
      class="form-validate">
    <input type="hidden" name="task" value=""/>

    <?php
    foreach ($fields as $field) {
        echo $this->form->renderField($field);
    }

    if ($hasVirtueMart) {
        ?>
        <div class="control-group">
            <div class="control-label">
                <label id="jform_country_id-lbl" for="jform_country_id">
                    <?php echo VmText::_('COM_VIRTUEMART_COUNTRY') ?></label>
            </div>

            <div class="controls">
                <?php echo ShopFunctionsF::renderCountryList(
                    0, false, [], '', 0, 'jform_country_id', 'jform[country_id]') ?>
            </div>
        </div>
        <?php
    }

    echo $this->form->getInput('id');
    echo HTMLHelper::_('form.token');
    ?>
</form>