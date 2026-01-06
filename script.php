<?php
/**
 * @package     Seven
 * @subpackage  pkg_seven
 *
 * @copyright   Copyright (C) seven communications GmbH & Co. KG. All rights reserved.
 * @license     MIT
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\Database\DatabaseInterface;

return new class () implements InstallerScriptInterface {

    /**
     * Runs after install, update or discover_install
     *
     * @param   string            $type    The type of installation
     * @param   InstallerAdapter  $parent  The parent installer
     *
     * @return  boolean
     */
    public function postflight(string $type, InstallerAdapter $parent): bool
    {
        // Enable the system plugin after installation
        if ($type === 'install' || $type === 'discover_install') {
            $this->enablePlugin('system', 'sevensms');
        }

        return true;
    }

    /**
     * Enable a plugin
     *
     * @param   string  $group    Plugin group
     * @param   string  $element  Plugin element
     *
     * @return  void
     */
    private function enablePlugin(string $group, string $element): void
    {
        try {
            $db = Factory::getContainer()->get(DatabaseInterface::class);

            $query = $db->getQuery(true)
                ->update($db->quoteName('#__extensions'))
                ->set($db->quoteName('enabled') . ' = 1')
                ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
                ->where($db->quoteName('folder') . ' = ' . $db->quote($group))
                ->where($db->quoteName('element') . ' = ' . $db->quote($element));

            $db->setQuery($query);
            $db->execute();
        } catch (\Exception $e) {
            // Silently fail - user can enable manually
        }
    }

    /**
     * Function called before extension installation/update/removal procedure commences.
     *
     * @param   string            $type    The type of change
     * @param   InstallerAdapter  $parent  The parent installer
     *
     * @return  boolean
     */
    public function preflight(string $type, InstallerAdapter $parent): bool
    {
        return true;
    }

    /**
     * Function called on install
     *
     * @param   InstallerAdapter  $parent  The parent installer
     *
     * @return  boolean
     */
    public function install(InstallerAdapter $parent): bool
    {
        return true;
    }

    /**
     * Function called on update
     *
     * @param   InstallerAdapter  $parent  The parent installer
     *
     * @return  boolean
     */
    public function update(InstallerAdapter $parent): bool
    {
        return true;
    }

    /**
     * Function called on uninstall
     *
     * @param   InstallerAdapter  $parent  The parent installer
     *
     * @return  boolean
     */
    public function uninstall(InstallerAdapter $parent): bool
    {
        return true;
    }
};
