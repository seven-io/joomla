<?php
/**
 * @package seven
 * @author seven communications GmbH & Co. KG <support@seven.io>
 * @copyright  2020-present seven communications GmbH & Co. KG
 * @license    MIT; see LICENSE.txt
 * @link       http://www.seven.io
 */

namespace Seven\Joomla\helpers;

use Exception;

class ApiKeyMismatchException extends Exception {
    public function __construct($message = 'COM_SEVEN_API_KEY_MISMATCH',
                                $code = 0, Exception $previous = null) {

        parent::__construct($message, $code, $previous);
    }
}

