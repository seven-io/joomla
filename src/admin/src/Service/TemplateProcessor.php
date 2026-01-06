<?php
/**
 * @package     Seven
 * @subpackage  com_seven
 *
 * @copyright   Copyright (C) seven communications GmbH & Co. KG. All rights reserved.
 * @license     MIT
 */

namespace Seven\Component\Seven\Administrator\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/**
 * Template processor for SMS automation
 * Handles variable substitution in message templates
 *
 * @since  3.1.0
 */
class TemplateProcessor
{
    /**
     * Variable definitions by trigger type
     *
     * @var array
     */
    private static array $variableDefinitions = [
        'vm_order_confirmed' => [
            'order_id', 'order_number', 'customer_name', 'customer_firstname',
            'customer_lastname', 'customer_email', 'customer_phone', 'total',
            'currency', 'status', 'payment_method', 'shipping_method', 'shop_name',
        ],
        'vm_order_status_change' => [
            'order_id', 'order_number', 'customer_name', 'customer_firstname',
            'customer_lastname', 'customer_email', 'customer_phone', 'total',
            'currency', 'status', 'old_status', 'new_status', 'payment_method',
            'shipping_method', 'shop_name',
        ],
        'vm_order_shipped' => [
            'order_id', 'order_number', 'customer_name', 'customer_firstname',
            'customer_lastname', 'customer_email', 'customer_phone', 'total',
            'currency', 'tracking_number', 'carrier', 'shop_name',
        ],
        'vm_order_cancelled' => [
            'order_id', 'order_number', 'customer_name', 'customer_firstname',
            'customer_lastname', 'customer_email', 'customer_phone', 'total',
            'currency', 'cancellation_reason', 'shop_name',
        ],
        'user_registration' => [
            'username', 'name', 'email', 'user_id', 'registration_date', 'site_name',
        ],
        'content_save' => [
            'article_title', 'article_id', 'author_name', 'category',
            'created_date', 'is_new', 'site_name',
        ],
    ];

    /**
     * Process a template with given context data
     *
     * @param   string  $template  The template with {variable} placeholders
     * @param   array   $context   The context data for variable resolution
     *
     * @return  string  The processed message
     *
     * @since   3.1.0
     */
    public function process(string $template, array $context): string
    {
        return preg_replace_callback(
            '/\{([a-z_]+)\}/',
            function ($matches) use ($context) {
                $variable = $matches[1];
                return $context[$variable] ?? $matches[0];
            },
            $template
        );
    }

    /**
     * Extract all variables from a template
     *
     * @param   string  $template  The template to parse
     *
     * @return  array  List of variable names found
     *
     * @since   3.1.0
     */
    public function extractVariables(string $template): array
    {
        preg_match_all('/\{([a-z_]+)\}/', $template, $matches);
        return array_unique($matches[1] ?? []);
    }

    /**
     * Get available variables for a trigger type
     *
     * @param   string  $triggerType  The trigger type
     *
     * @return  array  List of available variable names
     *
     * @since   3.1.0
     */
    public static function getAvailableVariables(string $triggerType): array
    {
        return self::$variableDefinitions[$triggerType] ?? [];
    }

    /**
     * Get all trigger types with their variables
     *
     * @return  array  Associative array of trigger types and their variables
     *
     * @since   3.1.0
     */
    public static function getAllVariableDefinitions(): array
    {
        return self::$variableDefinitions;
    }

    /**
     * Validate that all template variables can be resolved for a trigger type
     *
     * @param   string  $template     The template to validate
     * @param   string  $triggerType  The trigger type
     *
     * @return  array  Array with 'valid' boolean and 'invalid_variables' list
     *
     * @since   3.1.0
     */
    public function validateTemplate(string $template, string $triggerType): array
    {
        $usedVariables = $this->extractVariables($template);
        $availableVariables = self::getAvailableVariables($triggerType);
        $invalidVariables = array_diff($usedVariables, $availableVariables);

        return [
            'valid' => empty($invalidVariables),
            'invalid_variables' => array_values($invalidVariables),
            'used_variables' => $usedVariables,
            'available_variables' => $availableVariables,
        ];
    }

    /**
     * Build context from VirtueMart order data
     *
     * @param   mixed   $order      VirtueMart order object/array
     * @param   string  $oldStatus  Previous order status (for status change events)
     *
     * @return  array  Context array for template processing
     *
     * @since   3.1.0
     */
    public function buildOrderContext($order, string $oldStatus = ''): array
    {
        // Handle both object and array order data
        $orderDetails = is_array($order) && isset($order['details']['BT'])
            ? $order['details']['BT']
            : (is_object($order) ? $order : (object) $order);

        $firstName = $orderDetails->first_name ?? '';
        $lastName = $orderDetails->last_name ?? '';

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
            'status' => $this->getOrderStatusName($orderDetails->order_status ?? ''),
            'old_status' => $oldStatus ? $this->getOrderStatusName($oldStatus) : '',
            'new_status' => $this->getOrderStatusName($orderDetails->order_status ?? ''),
            'payment_method' => $orderDetails->payment_name ?? '',
            'shipping_method' => $orderDetails->shipment_name ?? '',
            'tracking_number' => $orderDetails->tracking_number ?? '',
            'carrier' => $orderDetails->carrier ?? '',
            'cancellation_reason' => $orderDetails->cancellation_reason ?? '',
            'shop_name' => $this->getShopName(),
        ];
    }

    /**
     * Build context from Joomla user data
     *
     * @param   array  $user  User data array
     *
     * @return  array  Context array for template processing
     *
     * @since   3.1.0
     */
    public function buildUserContext(array $user): array
    {
        return [
            'username' => $user['username'] ?? '',
            'name' => $user['name'] ?? '',
            'email' => $user['email'] ?? '',
            'user_id' => $user['id'] ?? '',
            'registration_date' => date('d.m.Y H:i'),
            'site_name' => $this->getSiteName(),
        ];
    }

    /**
     * Build context from Joomla content/article data
     *
     * @param   object  $article  Article object
     * @param   bool    $isNew    Whether the article is newly created
     *
     * @return  array  Context array for template processing
     *
     * @since   3.1.0
     */
    public function buildContentContext(object $article, bool $isNew = false): array
    {
        $authorName = '';
        if (!empty($article->created_by)) {
            $user = Factory::getContainer()->get(\Joomla\CMS\User\UserFactoryInterface::class)
                ->loadUserById($article->created_by);
            $authorName = $user->name ?? '';
        }

        return [
            'article_title' => $article->title ?? '',
            'article_id' => $article->id ?? '',
            'author_name' => $authorName,
            'category' => $article->category_title ?? '',
            'created_date' => date('d.m.Y H:i'),
            'is_new' => $isNew ? 'Ja' : 'Nein',
            'site_name' => $this->getSiteName(),
        ];
    }

    /**
     * Get VirtueMart order status name from code
     *
     * @param   string  $statusCode  Status code (e.g., 'P', 'C', 'X')
     *
     * @return  string  Human-readable status name
     *
     * @since   3.1.0
     */
    private function getOrderStatusName(string $statusCode): string
    {
        $statusMap = [
            'P' => 'Ausstehend',
            'U' => 'Bestätigt',
            'C' => 'Abgeschlossen',
            'X' => 'Storniert',
            'R' => 'Erstattet',
            'S' => 'Versendet',
        ];

        return $statusMap[$statusCode] ?? $statusCode;
    }

    /**
     * Get the Joomla site name
     *
     * @return  string
     *
     * @since   3.1.0
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
     * Get the VirtueMart shop name
     *
     * @return  string
     *
     * @since   3.1.0
     */
    private function getShopName(): string
    {
        // Try to get VirtueMart vendor name, fallback to site name
        try {
            if (class_exists('VmConfig')) {
                \VmConfig::loadConfig();
                return \VmConfig::get('shop_name', $this->getSiteName());
            }
        } catch (\Exception $e) {
            // VirtueMart not available
        }

        return $this->getSiteName();
    }
}
