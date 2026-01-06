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
use Joomla\CMS\Log\Log;
use Joomla\Database\DatabaseInterface;
use Seven\Component\Seven\Administrator\Helper\SevenHelper;

/**
 * Service for handling SMS automation execution
 *
 * @since  3.1.0
 */
class AutomationService
{
    /**
     * Database instance
     *
     * @var DatabaseInterface
     */
    private DatabaseInterface $db;

    /**
     * Template processor instance
     *
     * @var TemplateProcessor
     */
    private TemplateProcessor $templateProcessor;

    /**
     * Constructor
     *
     * @param   DatabaseInterface|null  $db  Database instance (optional, will be resolved from container)
     *
     * @since   3.1.0
     */
    public function __construct(?DatabaseInterface $db = null)
    {
        $this->db = $db ?? Factory::getContainer()->get(DatabaseInterface::class);
        $this->templateProcessor = new TemplateProcessor();
    }

    /**
     * Get all enabled automations for a specific trigger type
     *
     * @param   string  $triggerType  The trigger type to filter by
     *
     * @return  array  Array of automation objects
     *
     * @since   3.1.0
     */
    public function getAutomationsForTrigger(string $triggerType): array
    {
        $query = $this->db->getQuery(true)
            ->select('*')
            ->from($this->db->quoteName('#__seven_automations'))
            ->where($this->db->quoteName('trigger_type') . ' = ' . $this->db->quote($triggerType))
            ->where($this->db->quoteName('enabled') . ' = 1');

        $this->db->setQuery($query);

        return $this->db->loadObjectList() ?: [];
    }

    /**
     * Execute an automation with given context data
     *
     * @param   object  $automation  The automation configuration
     * @param   array   $context     Context data for template processing
     *
     * @return  array  Result with 'success', 'recipient', 'message', 'response' keys
     *
     * @since   3.1.0
     */
    public function executeAutomation(object $automation, array $context): array
    {
        // Check if OAuth is connected
        if (!SevenHelper::isConnected()) {
            $this->logExecution(
                (int) $automation->id,
                $automation->trigger_type,
                '',
                '',
                $context,
                ['error' => 'OAuth not connected']
            );

            return [
                'success' => false,
                'error' => 'OAuth not connected',
            ];
        }

        // Resolve recipient
        $recipient = $this->resolveRecipient($automation, $context);

        if (empty($recipient)) {
            $this->logExecution(
                (int) $automation->id,
                $automation->trigger_type,
                '',
                '',
                $context,
                ['error' => 'No recipient found']
            );

            return [
                'success' => false,
                'error' => 'No recipient found',
            ];
        }

        // Process template
        $message = $this->templateProcessor->process($automation->template, $context);

        // Send SMS
        $result = $this->sendAutomatedSms($automation, $recipient, $message);

        // Log execution
        $this->logExecution(
            (int) $automation->id,
            $automation->trigger_type,
            $recipient,
            $message,
            $context,
            $result['response'] ?? null
        );

        return $result;
    }

    /**
     * Resolve the recipient for an automation
     *
     * @param   object  $automation  The automation configuration
     * @param   array   $context     Context data
     *
     * @return  string  The recipient phone number
     *
     * @since   3.1.0
     */
    private function resolveRecipient(object $automation, array $context): string
    {
        switch ($automation->recipient_type) {
            case 'customer':
                // Get phone from context (order customer or user)
                return $context['customer_phone']
                    ?? $context['phone']
                    ?? '';

            case 'admin':
                // Get admin phone from component parameters
                return $this->getAdminPhone();

            case 'custom':
                return $automation->custom_recipient ?? '';

            default:
                return '';
        }
    }

    /**
     * Get admin phone numbers from component parameters
     *
     * @return  string  Comma-separated phone numbers
     *
     * @since   3.1.0
     */
    private function getAdminPhone(): string
    {
        $params = \Joomla\CMS\Component\ComponentHelper::getParams('com_seven');

        $phones = [];

        $phone1 = trim($params->get('admin_phone', ''));
        if (!empty($phone1)) {
            $phones[] = $phone1;
        }

        $phone2 = trim($params->get('admin_phone_2', ''));
        if (!empty($phone2)) {
            $phones[] = $phone2;
        }

        return implode(',', $phones);
    }

    /**
     * Send SMS using the Seven API
     *
     * @param   object  $automation  The automation configuration
     * @param   string  $recipient   The phone number to send to
     * @param   string  $message     The message text
     *
     * @return  array  Result with 'success', 'response', 'error' keys
     *
     * @since   3.1.0
     */
    public function sendAutomatedSms(object $automation, string $recipient, string $message): array
    {
        $apiClient = SevenHelper::getApiClient();

        if ($apiClient === null) {
            return [
                'success' => false,
                'error' => 'Failed to get API client',
            ];
        }

        // Build SMS parameters
        $params = [
            'to' => SevenHelper::formatPhoneNumber($recipient),
            'text' => $message,
            'from' => $automation->sender_id ?: 'seven',
        ];

        // Parse additional options if set
        if (!empty($automation->options)) {
            $options = json_decode($automation->options, true);
            if (is_array($options)) {
                if (!empty($options['flash'])) {
                    $params['flash'] = 1;
                }
                if (!empty($options['unicode'])) {
                    $params['unicode'] = 1;
                }
                if (!empty($options['performance_tracking'])) {
                    $params['performance_tracking'] = 1;
                }
            }
        }

        // Send SMS
        $response = $apiClient->sendSms($params);

        if ($response === null) {
            return [
                'success' => false,
                'error' => 'API request failed',
                'response' => null,
            ];
        }

        $success = isset($response['success']) && $response['success'] === '100';

        // Also check for messages array response format
        if (!$success && isset($response['messages']) && is_array($response['messages'])) {
            foreach ($response['messages'] as $msg) {
                if (isset($msg['success']) && $msg['success']) {
                    $success = true;
                    break;
                }
            }
        }

        return [
            'success' => $success,
            'response' => $response,
            'recipient' => $recipient,
            'message' => $message,
        ];
    }

    /**
     * Log automation execution
     *
     * @param   int          $automationId  The automation ID
     * @param   string       $triggerType   The trigger type
     * @param   string       $recipient     The recipient phone number
     * @param   string       $message       The sent message
     * @param   array        $variables     Variables used in template
     * @param   array|null   $response      API response
     *
     * @return  void
     *
     * @since   3.1.0
     */
    public function logExecution(
        int $automationId,
        string $triggerType,
        string $recipient,
        string $message,
        array $variables,
        ?array $response
    ): void {
        $success = false;
        $responseCode = null;

        if ($response !== null) {
            if (isset($response['success'])) {
                $success = $response['success'] === '100' || $response['success'] === true;
                $responseCode = (int) $response['success'];
            } elseif (isset($response['messages'][0]['success'])) {
                $success = (bool) $response['messages'][0]['success'];
            }
        }

        $columns = [
            'automation_id',
            'trigger_type',
            'recipient',
            'message',
            'variables_used',
            'response_code',
            'response_data',
            'success',
            'created',
        ];

        $values = [
            $automationId,
            $this->db->quote($triggerType),
            $this->db->quote($recipient),
            $this->db->quote($message),
            $this->db->quote(json_encode($variables)),
            $responseCode !== null ? $responseCode : 'NULL',
            $this->db->quote(json_encode($response)),
            $success ? 1 : 0,
            $this->db->quote(date('Y-m-d H:i:s')),
        ];

        $query = $this->db->getQuery(true)
            ->insert($this->db->quoteName('#__seven_automation_logs'))
            ->columns($this->db->quoteName($columns))
            ->values(implode(',', $values));

        try {
            $this->db->setQuery($query);
            $this->db->execute();
        } catch (\Exception $e) {
            // Log to Joomla error log if database insert fails
            Log::add('Seven Automation Log Error: ' . $e->getMessage(), Log::ERROR, 'com_seven');
        }
    }

    /**
     * Test an automation with sample data
     *
     * @param   int     $automationId   The automation ID to test
     * @param   string  $testRecipient  The phone number to send test to
     *
     * @return  array  Test result
     *
     * @since   3.1.0
     */
    public function testAutomation(int $automationId, string $testRecipient): array
    {
        // Load automation
        $query = $this->db->getQuery(true)
            ->select('*')
            ->from($this->db->quoteName('#__seven_automations'))
            ->where($this->db->quoteName('id') . ' = ' . $automationId);

        $this->db->setQuery($query);
        $automation = $this->db->loadObject();

        if (!$automation) {
            return [
                'success' => false,
                'error' => 'Automation not found',
            ];
        }

        // Generate sample context based on trigger type
        $context = $this->getSampleContext($automation->trigger_type);

        // Override recipient for test
        $context['customer_phone'] = $testRecipient;
        $context['phone'] = $testRecipient;

        // Process template with sample data
        $message = $this->templateProcessor->process($automation->template, $context);

        // Create a copy of automation for testing
        $testAutomation = clone $automation;
        $testAutomation->recipient_type = 'custom';
        $testAutomation->custom_recipient = $testRecipient;

        // Send test SMS
        return $this->sendAutomatedSms($testAutomation, $testRecipient, $message);
    }

    /**
     * Get sample context data for testing
     *
     * @param   string  $triggerType  The trigger type
     *
     * @return  array  Sample context data
     *
     * @since   3.1.0
     */
    private function getSampleContext(string $triggerType): array
    {
        $siteName = Factory::getApplication()->get('sitename', 'Mein Shop');

        switch ($triggerType) {
            case 'vm_order_confirmed':
            case 'vm_order_status_change':
            case 'vm_order_shipped':
            case 'vm_order_cancelled':
                return [
                    'order_id' => '12345',
                    'order_number' => 'ORD-2025-12345',
                    'customer_name' => 'Max Mustermann',
                    'customer_firstname' => 'Max',
                    'customer_lastname' => 'Mustermann',
                    'customer_email' => 'max@example.com',
                    'customer_phone' => '+49123456789',
                    'total' => '99,95',
                    'currency' => 'EUR',
                    'status' => 'Bestätigt',
                    'old_status' => 'Ausstehend',
                    'new_status' => 'Bestätigt',
                    'payment_method' => 'PayPal',
                    'shipping_method' => 'DHL Standard',
                    'tracking_number' => '1234567890',
                    'carrier' => 'DHL',
                    'cancellation_reason' => 'Kunde hat storniert',
                    'shop_name' => $siteName,
                ];

            case 'user_registration':
                return [
                    'username' => 'maxmustermann',
                    'name' => 'Max Mustermann',
                    'email' => 'max@example.com',
                    'user_id' => '42',
                    'registration_date' => date('d.m.Y H:i'),
                    'site_name' => $siteName,
                ];

            case 'content_save':
                return [
                    'article_title' => 'Beispiel-Artikel',
                    'article_id' => '123',
                    'author_name' => 'Admin',
                    'category' => 'News',
                    'created_date' => date('d.m.Y H:i'),
                    'is_new' => 'Ja',
                    'site_name' => $siteName,
                ];

            default:
                return [
                    'site_name' => $siteName,
                ];
        }
    }

    /**
     * Get automation logs for a specific automation
     *
     * @param   int  $automationId  The automation ID
     * @param   int  $limit         Maximum number of logs to return
     *
     * @return  array  Array of log objects
     *
     * @since   3.1.0
     */
    public function getAutomationLogs(int $automationId, int $limit = 50): array
    {
        $query = $this->db->getQuery(true)
            ->select('*')
            ->from($this->db->quoteName('#__seven_automation_logs'))
            ->where($this->db->quoteName('automation_id') . ' = ' . $automationId)
            ->order($this->db->quoteName('created') . ' DESC')
            ->setLimit($limit);

        $this->db->setQuery($query);

        return $this->db->loadObjectList() ?: [];
    }

    /**
     * Get automation statistics
     *
     * @param   int  $automationId  The automation ID (0 for all)
     *
     * @return  object  Statistics object with total, success, failed counts
     *
     * @since   3.1.0
     */
    public function getStatistics(int $automationId = 0): object
    {
        $query = $this->db->getQuery(true)
            ->select([
                'COUNT(*) as total',
                'SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as success_count',
                'SUM(CASE WHEN success = 0 THEN 1 ELSE 0 END) as failed_count',
            ])
            ->from($this->db->quoteName('#__seven_automation_logs'));

        if ($automationId > 0) {
            $query->where($this->db->quoteName('automation_id') . ' = ' . $automationId);
        }

        $this->db->setQuery($query);

        return $this->db->loadObject() ?: (object) ['total' => 0, 'success_count' => 0, 'failed_count' => 0];
    }
}
