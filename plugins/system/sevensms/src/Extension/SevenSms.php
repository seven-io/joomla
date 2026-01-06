<?php
/**
 * @package     Seven
 * @subpackage  plg_system_sevensms
 *
 * @copyright   Copyright (C) seven communications GmbH & Co. KG. All rights reserved.
 * @license     MIT
 */

namespace Seven\Plugin\System\SevenSms\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\DispatcherInterface;

/**
 * Seven SMS System Plugin
 *
 * Handles Joomla core events for SMS automation:
 * - User registration
 * - Content save
 *
 * @since  1.0.0
 */
class SevenSms extends CMSPlugin
{
    use DatabaseAwareTrait;

    /**
     * Load the language file on instantiation
     *
     * @var    boolean
     * @since  1.0.0
     */
    protected $autoloadLanguage = true;

    /**
     * Track processed items to prevent duplicates
     *
     * @var    array
     * @since  1.0.0
     */
    private static array $processedItems = [];

    /**
     * Constructor - register event listeners
     *
     * @param   DispatcherInterface  $dispatcher  The event dispatcher
     * @param   array                $config      Plugin configuration
     */
    public function __construct(DispatcherInterface $dispatcher, array $config)
    {
        parent::__construct($dispatcher, $config);

        // Manually register event listeners for Joomla 5/6
        $dispatcher->addListener('onContentAfterSave', [$this, 'onContentAfterSave']);
        $dispatcher->addListener('onUserAfterSave', [$this, 'onUserAfterSave']);
    }

    /**
     * Handle user registration event
     *
     * @param   \Joomla\CMS\Event\User\AfterSaveEvent  $event  The event object
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function onUserAfterSave($event): void
    {
        // Check if user registration trigger is enabled
        if (!$this->params->get('enabled_user_registration', 1)) {
            return;
        }

        // Get event arguments - handle both event object and legacy array
        if (is_object($event) && method_exists($event, 'getArgument')) {
            $user = $event->getArgument('subject') ?? $event->getArgument(0);
            $isNew = $event->getArgument('isNew') ?? $event->getArgument(1) ?? false;
        } else {
            // Legacy fallback
            $args = func_get_args();
            $user = $args[0] ?? [];
            $isNew = $args[1] ?? false;
        }

        // Only trigger on new user registrations
        if (!$isNew) {
            return;
        }

        // Ensure user data is an array
        if (is_object($user)) {
            $user = (array) $user;
        }

        $this->triggerAutomation('user_registration', [
            'username' => $user['username'] ?? '',
            'name' => $user['name'] ?? '',
            'email' => $user['email'] ?? '',
            'user_id' => $user['id'] ?? '',
            'registration_date' => date('d.m.Y H:i'),
            'site_name' => $this->getSiteName(),
        ]);
    }

    /**
     * Handle content save event
     *
     * @param   \Joomla\CMS\Event\Content\AfterSaveEvent  $event  The event object
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function onContentAfterSave($event): void
    {
        // Check if content save trigger is enabled
        if (!$this->params->get('enabled_content_save', 1)) {
            return;
        }

        // Get event arguments
        if (is_object($event) && method_exists($event, 'getArgument')) {
            $context = $event->getArgument('context') ?? $event->getArgument(0);
            $article = $event->getArgument('subject') ?? $event->getArgument(1);
            $isNew = $event->getArgument('isNew') ?? $event->getArgument(2) ?? false;
        } else {
            // Legacy fallback
            $args = func_get_args();
            $context = $args[0] ?? '';
            $article = $args[1] ?? null;
            $isNew = $args[2] ?? false;
        }

        // Only trigger for articles
        if ($context !== 'com_content.article') {
            return;
        }

        // Prevent duplicate processing (Joomla sometimes fires events twice)
        $itemKey = 'content_' . ($article->id ?? 0) . '_' . md5(serialize($article->title ?? ''));
        if (isset(self::$processedItems[$itemKey])) {
            return;
        }
        self::$processedItems[$itemKey] = true;

        // Get author name
        $authorName = '';
        if (!empty($article->created_by)) {
            try {
                $user = Factory::getContainer()
                    ->get(\Joomla\CMS\User\UserFactoryInterface::class)
                    ->loadUserById($article->created_by);
                $authorName = $user->name ?? '';
            } catch (\Exception $e) {
                // Ignore user loading errors
            }
        }

        $this->triggerAutomation('content_save', [
            'article_title' => $article->title ?? '',
            'article_id' => $article->id ?? '',
            'author_name' => $authorName,
            'category' => $article->category_title ?? '',
            'created_date' => date('d.m.Y H:i'),
            'is_new' => $isNew ? 'Ja' : 'Nein',
            'site_name' => $this->getSiteName(),
        ]);
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

            $db = Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);
            $service = new \Seven\Component\Seven\Administrator\Service\AutomationService($db);

            $automations = $service->getAutomationsForTrigger($triggerType);

            foreach ($automations as $automation) {
                $service->executeAutomation($automation, $context);
            }
        } catch (\Exception $e) {
            Log::add('Seven SMS Plugin Error: ' . $e->getMessage(), Log::ERROR, 'plg_system_sevensms');
        }
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
}
