<?php
/**
 * @package     Seven
 * @subpackage  plg_vmshopper_sevensms
 *
 * @copyright   Copyright (C) seven communications GmbH & Co. KG. All rights reserved.
 * @license     MIT
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Log\Log;
use Joomla\Database\DatabaseInterface;

// Load VirtueMart plugin library
if (!class_exists('vmShopperPlugin')) {
    $vmPluginLibs = JPATH_VM_PLUGINS . '/vmshopperplugin.php';
    if (file_exists($vmPluginLibs)) {
        require_once $vmPluginLibs;
    } else {
        // VirtueMart not properly installed
        return;
    }
}

/**
 * Seven SMS VirtueMart Shopper Plugin
 *
 * Handles VirtueMart order events for SMS automation:
 * - Order confirmed
 * - Order status change
 * - Order shipped
 * - Order cancelled
 *
 * @since  1.0.0
 */
class plgVmShopperSevenSms extends vmShopperPlugin
{
    /**
     * Plugin parameters
     *
     * @var    \Joomla\Registry\Registry
     * @since  1.0.0
     */
    protected $params;

    /**
     * Constructor
     *
     * @param   object  $subject  The object to observe
     * @param   array   $config   Plugin configuration
     *
     * @since   1.0.0
     */
    public function __construct(&$subject, $config = [])
    {
        parent::__construct($subject, $config);

        $this->_tablename = '#__virtuemart_userinfos';
        $this->_tablekey = 'virtuemart_userinfo_id';
    }

    /**
     * Triggered when an order is confirmed
     *
     * @param   object  $cart   The cart object
     * @param   array   $order  The order data
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function plgVmConfirmedOrder($cart, $order): void
    {
        if (!$this->params->get('enabled_order_confirmed', 1)) {
            return;
        }

        $context = $this->extractOrderContext($order);
        $this->triggerAutomation('vm_order_confirmed', $context);
    }

    /**
     * Triggered when order payment status is updated
     *
     * @param   object  $order            The order object
     * @param   string  $old_order_status The previous order status
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function plgVmOnUpdateOrderPayment($order, $old_order_status): void
    {
        if (!$this->params->get('enabled_order_status_change', 1)) {
            return;
        }

        $context = $this->extractOrderContext($order);
        $context['old_status'] = $this->getOrderStatusName($old_order_status);

        // Check for shipped status
        $currentStatus = $order->order_status ?? '';
        if ($currentStatus === 'S' && $this->params->get('enabled_order_shipped', 1)) {
            $this->triggerAutomation('vm_order_shipped', $context);
        }

        // Check for cancelled status
        if ($currentStatus === 'X' && $this->params->get('enabled_order_cancelled', 1)) {
            $this->triggerAutomation('vm_order_cancelled', $context);
        }

        // General status change
        $this->triggerAutomation('vm_order_status_change', $context);
    }

    /**
     * Triggered when order shipment status is updated
     *
     * @param   object  $order            The order object
     * @param   string  $old_order_status The previous order status
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function plgVmOnUpdateOrderShipment($order, $old_order_status): void
    {
        // Handle via plgVmOnUpdateOrderPayment for consistency
    }

    /**
     * Extract context data from VirtueMart order
     *
     * @param   mixed  $order  The order object or array
     *
     * @return  array  Context data for template processing
     *
     * @since   1.0.0
     */
    private function extractOrderContext($order): array
    {
        // Handle different order data structures
        $orderDetails = null;

        if (is_array($order) && isset($order['details']['BT'])) {
            $orderDetails = $order['details']['BT'];
        } elseif (is_object($order)) {
            $orderDetails = $order;
        } elseif (is_array($order)) {
            $orderDetails = (object) $order;
        }

        if (!$orderDetails) {
            return [];
        }

        $firstName = $orderDetails->first_name ?? '';
        $lastName = $orderDetails->last_name ?? '';
        $currentStatus = $orderDetails->order_status ?? '';

        return [
            'order_id' => $orderDetails->virtuemart_order_id ?? '',
            'order_number' => $orderDetails->order_number ?? '',
            'customer_name' => trim($firstName . ' ' . $lastName),
            'customer_firstname' => $firstName,
            'customer_lastname' => $lastName,
            'customer_email' => $orderDetails->email ?? '',
            'customer_phone' => $orderDetails->phone_2 ?? $orderDetails->phone_1 ?? '',
            'total' => number_format((float) ($orderDetails->order_total ?? 0), 2, ',', '.'),
            'currency' => $orderDetails->order_currency ?? 'EUR',
            'status' => $this->getOrderStatusName($currentStatus),
            'new_status' => $this->getOrderStatusName($currentStatus),
            'payment_method' => $orderDetails->payment_name ?? '',
            'shipping_method' => $orderDetails->shipment_name ?? '',
            'tracking_number' => $orderDetails->tracking_number ?? '',
            'carrier' => $orderDetails->carrier ?? '',
            'cancellation_reason' => $orderDetails->cancellation_reason ?? '',
            'shop_name' => $this->getShopName(),
        ];
    }

    /**
     * Get VirtueMart order status name from code
     *
     * @param   string  $statusCode  Status code (e.g., 'P', 'C', 'X')
     *
     * @return  string  Human-readable status name
     *
     * @since   1.0.0
     */
    private function getOrderStatusName(string $statusCode): string
    {
        $statusMap = [
            'P' => 'Ausstehend',
            'U' => 'Bestaetigt',
            'C' => 'Abgeschlossen',
            'X' => 'Storniert',
            'R' => 'Erstattet',
            'S' => 'Versendet',
        ];

        return $statusMap[$statusCode] ?? $statusCode;
    }

    /**
     * Get the VirtueMart shop name
     *
     * @return  string
     *
     * @since   1.0.0
     */
    private function getShopName(): string
    {
        try {
            if (class_exists('VmConfig')) {
                VmConfig::loadConfig();
                return VmConfig::get('shop_name', $this->getSiteName());
            }
        } catch (\Exception $e) {
            // VirtueMart config not available
        }

        return $this->getSiteName();
    }

    /**
     * Get the Joomla site name
     *
     * @return  string
     *
     * @since   1.0.0
     */
    private function getSiteName(): string
    {
        try {
            return Factory::getApplication()->get('sitename', '');
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Trigger automations for a specific type
     *
     * @param   string  $triggerType  The trigger type
     * @param   array   $context      Context data for template processing
     *
     * @return  void
     *
     * @since   1.0.0
     */
    private function triggerAutomation(string $triggerType, array $context): void
    {
        // Check if Seven component is installed
        if (!ComponentHelper::isInstalled('com_seven') || !ComponentHelper::isEnabled('com_seven')) {
            return;
        }

        try {
            // Load the AutomationService
            $servicePath = JPATH_ADMINISTRATOR . '/components/com_seven/src/Service/AutomationService.php';
            $templatePath = JPATH_ADMINISTRATOR . '/components/com_seven/src/Service/TemplateProcessor.php';
            $helperPath = JPATH_ADMINISTRATOR . '/components/com_seven/src/Helper/SevenHelper.php';

            if (!file_exists($servicePath) || !file_exists($templatePath) || !file_exists($helperPath)) {
                return;
            }

            // Include necessary files if not already loaded
            if (!class_exists('\\Seven\\Component\\Seven\\Administrator\\Service\\AutomationService')) {
                require_once $helperPath;
                require_once $templatePath;
                require_once $servicePath;
            }

            $db = Factory::getContainer()->get(DatabaseInterface::class);
            $service = new \Seven\Component\Seven\Administrator\Service\AutomationService($db);

            $automations = $service->getAutomationsForTrigger($triggerType);

            foreach ($automations as $automation) {
                $service->executeAutomation($automation, $context);
            }
        } catch (\Exception $e) {
            Log::add('Seven SMS VirtueMart Plugin Error: ' . $e->getMessage(), Log::ERROR, 'plg_vmshopper_sevensms');
        }
    }
}
