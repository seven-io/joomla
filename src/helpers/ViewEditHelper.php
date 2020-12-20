<?php
/**
 * @package sms77api
 * @author sms77 e.K. <support@sms77.io>
 * @copyright  2020-present
 * @license    MIT; see LICENSE.txt
 * @link       http://sms77.io
 */

namespace Sms77\Joomla\helpers;

use JFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use shopFunctionsF;
use VmConfig;
use vmText;

defined('_JEXEC') or die;

/**
 * View Edit helper.
 * @package sms77api
 * @since    1.3.0
 */
class ViewEditHelper {
    public function __construct($id, $task, $leftFields, $rightFields) {
        $this->js($task);

        $this->form($id, $leftFields, $rightFields);
    }

    private function js($task) {
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
                
                if (task === "$task.cancel" || document.formvalidator.isValid(form)) {
                    Joomla.submitform(task, form);
                }
            };
        });
JS
        );
    }

    public static function vm($shopperGroupIdField) {
        if (!ConfigurationHelper::hasVirtueMart()) {
            return '';
        }

        if (!class_exists('VmConfig')) {
            require JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php';
        }

        VmConfig::loadConfig();

        $lang = JFactory::getLanguage();
        $lang->load('com_virtuemart',
            JPATH_ADMINISTRATOR . '/components/com_virtuemart');
        $lang->load('com_virtuemart_shoppers',
            JPATH_ROOT . '/components/com_virtuemart');

        $label = VmText::_('COM_VIRTUEMART_COUNTRY');
        $countries = ShopFunctionsF::renderCountryList(
            0,
            false,
            [],
            '',
            0,
            'jform_country_id',
            'jform[country_id]');
        return $shopperGroupIdField . "
        <div class='control-group'>
            <div class='control-label'>
                <label id='jform_country_id-lbl' for='jform_country_id'>$label</label>
            </div>

            <div class='controls'>$countries</div>
        </div>
        ";
    }

    private function form($id, $leftFields, $rightFields) {
        HTMLHelper::_('behavior.formvalidation');
        HTMLHelper::_('behavior.keepalive');
        HTMLHelper::_('formbehavior.chosen');

        $action = Route::_("index.php?option=com_sms77api&layout=edit&id={$id}");
        ?>
        <form
                action="<?php echo $action; ?>"
                class="form-validate"
                enctype="multipart/form-data"
                id="adminForm"
                method="post"
                name="adminForm"
        >
            <input type="hidden" name="task" value=""/>

            <div class="container-fluid">
                <div class="row-fluid">
                    <div class="span6">
                        <?php echo $leftFields . HTMLHelper::_('form.token'); ?>
                    </div>

                    <div class="span6">
                        <?php echo $rightFields; ?>
                    </div>
                </div>
            </div>
        </form>
        <?php
    }
}