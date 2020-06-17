<?php
/**
 * @package sms77api
 * @author sms77 e.K. <support@sms77.io>
 * @copyright  2020-present
 * @license    MIT; see LICENSE.txt
 * @link       http://sms77.io
 */

defined('_JEXEC') or die;

/**
 * Sms77api script file.
 * @package  sms77api
 * @since    1.0.0
 */
class Com_Sms77apiInstallerScript {
    /**
     * Constructor
     * @param JAdapterInstance $adapter The object responsible for running this script
     * @since   1.0.0
     */
    public function __construct(JAdapterInstance $adapter) {
    }

    /**
     * Called before any type of action
     * @param string $route Which action is happening (install|uninstall|discover_install|update)
     * @param JAdapterInstance $adapter The object responsible for running this script
     * @return  boolean  True on success
     * @since   1.0.0
     */
    public function preflight($route, JAdapterInstance $adapter) {
    }

    /**
     * Called after any type of action
     * @param string $route Which action is happening (install|uninstall|discover_install|update)
     * @param JAdapterInstance $adapter The object responsible for running this script
     * @return  boolean  True on success
     * @since   1.0.0
     */
    public function postflight($route, JAdapterInstance $adapter) {
    }

    /**
     * Called on installation
     * @param JAdapterInstance $adapter The object responsible for running this script
     * @return  boolean  True on success
     * @since   1.0.0
     */
    public function install(JAdapterInstance $adapter) {
    }

    /**
     * Called on update
     * @param JAdapterInstance $adapter The object responsible for running this script
     * @return  boolean  True on success
     * @since   1.0.0
     */
    public function update(JAdapterInstance $adapter) {
    }

    /**
     * Called on uninstallation
     * @param JAdapterInstance $adapter The object responsible for running this script
     * @since   1.0.0
     */
    public function uninstall(JAdapterInstance $adapter) {
    }
}