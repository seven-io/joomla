<?php
/**
 * @package     Seven
 * @subpackage  com_seven
 *
 * @copyright   Copyright (C) seven communications GmbH & Co. KG. All rights reserved.
 * @license     MIT
 */

namespace Seven\Component\Seven\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Database\DatabaseInterface;
use Seven\Component\Seven\Administrator\Service\SevenApiClient;
use Seven\Component\Seven\Administrator\Service\OAuthService;

/**
 * General helper class for the Seven component
 *
 * @since  3.0.0
 */
class SevenHelper
{
    /**
     * Get the active (published) configuration
     *
     * @return  object|null  The active configuration or null if none found
     *
     * @since   3.0.0
     */
    public static function getActiveConfiguration(): ?object
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__seven_configurations'))
            ->where($db->quoteName('published') . ' = 1')
            ->setLimit(1);

        $db->setQuery($query);

        return $db->loadObject();
    }

    /**
     * Get configuration by ID
     *
     * @param   int  $id  The configuration ID
     *
     * @return  object|null  The configuration or null if not found
     *
     * @since   3.0.0
     */
    public static function getConfiguration(int $id): ?object
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__seven_configurations'))
            ->where($db->quoteName('id') . ' = ' . $id);

        $db->setQuery($query);

        return $db->loadObject();
    }

    /**
     * Create API client using OAuth access token
     *
     * @return  SevenApiClient|null  The API client or null if not connected
     *
     * @since   3.0.0
     */
    public static function getApiClient(): ?SevenApiClient
    {
        $oauthService = new OAuthService();
        $accessToken = $oauthService->getValidAccessToken();

        if ($accessToken === null) {
            return null;
        }

        return new SevenApiClient($accessToken);
    }

    /**
     * Check if OAuth is connected
     *
     * @return  bool  True if connected to seven.io via OAuth
     *
     * @since   3.0.0
     */
    public static function isConnected(): bool
    {
        $oauthService = new OAuthService();
        return $oauthService->isConnected();
    }

    /**
     * Check if VirtueMart is installed and enabled
     *
     * @return  bool  True if VirtueMart is available
     *
     * @since   3.0.0
     */
    public static function hasVirtueMart(): bool
    {
        return ComponentHelper::isInstalled('com_virtuemart')
            && ComponentHelper::isEnabled('com_virtuemart');
    }

    /**
     * Get VirtueMart customers by country and/or shopper group
     *
     * @param   int|null  $countryId       Optional country ID filter
     * @param   int|null  $shopperGroupId  Optional shopper group ID filter
     *
     * @return  array  Array of phone numbers
     *
     * @since   3.0.0
     */
    public static function getVirtueMartCustomers(?int $countryId = null, ?int $shopperGroupId = null): array
    {
        if (!self::hasVirtueMart()) {
            return [];
        }

        $db = Factory::getContainer()->get(DatabaseInterface::class);

        $query = $db->getQuery(true)
            ->select('DISTINCT COALESCE(NULLIF(' . $db->quoteName('ui.phone_2') . ', ' . $db->quote('') . '), ' . $db->quoteName('ui.phone_1') . ') AS phone')
            ->from($db->quoteName('#__virtuemart_userinfos', 'ui'))
            ->join('INNER', $db->quoteName('#__virtuemart_vmusers', 'vu') . ' ON ' . $db->quoteName('ui.virtuemart_user_id') . ' = ' . $db->quoteName('vu.virtuemart_user_id'))
            ->where($db->quoteName('ui.address_type') . ' = ' . $db->quote('BT'))
            ->where('COALESCE(' . $db->quoteName('ui.phone_2') . ', ' . $db->quoteName('ui.phone_1') . ') IS NOT NULL')
            ->where('COALESCE(' . $db->quoteName('ui.phone_2') . ', ' . $db->quoteName('ui.phone_1') . ') != ' . $db->quote(''));

        if ($countryId !== null) {
            $query->where($db->quoteName('ui.virtuemart_country_id') . ' = ' . (int) $countryId);
        }

        if ($shopperGroupId !== null) {
            $query->join('INNER', $db->quoteName('#__virtuemart_vmuser_shoppergroups', 'sg')
                . ' ON ' . $db->quoteName('vu.virtuemart_user_id') . ' = ' . $db->quoteName('sg.virtuemart_user_id'))
                ->where($db->quoteName('sg.virtuemart_shoppergroup_id') . ' = ' . (int) $shopperGroupId);
        }

        // Exclude vendors
        $query->where('(' . $db->quoteName('vu.user_is_vendor') . ' = 0 OR ' . $db->quoteName('vu.user_is_vendor') . ' IS NULL)');

        $db->setQuery($query);

        $results = $db->loadColumn();

        return array_filter($results);
    }

    /**
     * Parse recipients string or get from VirtueMart
     *
     * @param   string|null  $to              Comma/semicolon/newline separated phone numbers
     * @param   int|null     $countryId       Optional VirtueMart country ID filter
     * @param   int|null     $shopperGroupId  Optional VirtueMart shopper group ID filter
     *
     * @return  array  Array of phone numbers
     *
     * @since   3.0.0
     */
    public static function getRecipients(?string $to, ?int $countryId = null, ?int $shopperGroupId = null): array
    {
        // If VirtueMart filters are set, use those
        if ($countryId !== null || $shopperGroupId !== null) {
            return self::getVirtueMartCustomers($countryId, $shopperGroupId);
        }

        // Otherwise parse the 'to' string
        if (empty($to)) {
            return [];
        }

        // Split by comma, semicolon, or newline
        $recipients = preg_split('/[,;\n]+/', $to);

        return array_filter(array_map('trim', $recipients));
    }

    /**
     * Mask API key for display (show first 4 and last 4 characters)
     *
     * @param   string  $apiKey  The API key to mask
     *
     * @return  string  The masked API key
     *
     * @since   3.0.0
     */
    public static function maskApiKey(string $apiKey): string
    {
        $length = strlen($apiKey);

        if ($length <= 8) {
            return str_repeat('*', $length);
        }

        return substr($apiKey, 0, 4) . str_repeat('*', $length - 8) . substr($apiKey, -4);
    }

    /**
     * Format phone number for display
     *
     * @param   string  $phone  The phone number
     *
     * @return  string  Formatted phone number
     *
     * @since   3.0.0
     */
    public static function formatPhoneNumber(string $phone): string
    {
        // Handle multiple comma-separated numbers
        if (strpos($phone, ',') !== false) {
            $numbers = explode(',', $phone);
            $formatted = array_map([self::class, 'formatSinglePhoneNumber'], $numbers);
            return implode(',', array_filter($formatted));
        }

        return self::formatSinglePhoneNumber($phone);
    }

    /**
     * Format a single phone number
     *
     * @param   string  $phone  The phone number
     *
     * @return  string  Formatted phone number
     *
     * @since   3.1.0
     */
    private static function formatSinglePhoneNumber(string $phone): string
    {
        $phone = trim($phone);

        // Remove all non-digit characters except leading +
        $cleaned = preg_replace('/[^\d+]/', '', $phone);

        // Ensure + is only at the start
        if (strpos($cleaned, '+') !== false && strpos($cleaned, '+') !== 0) {
            $cleaned = str_replace('+', '', $cleaned);
        }

        return $cleaned;
    }

    /**
     * Get SMS status code description
     *
     * @param   int  $code  The status code from the API
     *
     * @return  string  Human-readable status description
     *
     * @since   3.0.0
     */
    public static function getSmsStatusDescription(int $code): string
    {
        $statuses = [
            100 => 'SMS sent successfully',
            101 => 'SMS sent to at least one recipient',
            201 => 'Invalid sender ID',
            202 => 'Invalid recipient number',
            300 => 'Insufficient credit',
            301 => 'Recipient country not supported',
            302 => 'Invalid API key',
            303 => 'Unknown error',
            304 => 'Bad request',
            305 => 'Internal server error',
            306 => 'Server busy',
            307 => 'Network error',
            400 => 'Invalid message ID',
            401 => 'Invalid message type',
            402 => 'Invalid URL',
            500 => 'No route found',
            600 => 'Carrier error',
            700 => 'Unknown error',
            900 => 'Authentication failed',
            901 => 'Too many requests',
            902 => 'Not enough credit',
            903 => 'Server error',
        ];

        return $statuses[$code] ?? 'Unknown status code: ' . $code;
    }

    /**
     * Get voice call status code description
     *
     * @param   int  $code  The status code from the API
     *
     * @return  string  Human-readable status description
     *
     * @since   3.0.0
     */
    public static function getVoiceStatusDescription(int $code): string
    {
        $statuses = [
            100 => 'Voice call initiated successfully',
            900 => 'Authentication failed',
            901 => 'Too many requests',
            902 => 'Not enough credit',
            903 => 'Server error',
        ];

        return $statuses[$code] ?? 'Unknown status code: ' . $code;
    }
}
