<?php
/**
 * @package sms77api
 * @author sms77 e.K. <support@sms77.io>
 * @copyright  2020-present
 * @license    MIT; see LICENSE.txt
 * @link       http://sms77.io
 */

namespace Sms77\Joomla\helpers;

use Exception;

class ApiKeyMismatchException extends Exception {
    public function __construct($message = 'COM_SMS77API_API_KEY_MISMATCH',
                                $code = 0, Exception $previous = null) {

        parent::__construct($message, $code, $previous);
    }
}

