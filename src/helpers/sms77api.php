<?php
/**
 * @package sms77api
 * @author sms77 e.K. <support@sms77.io>
 * @copyright  2020-present
 * @license    MIT; see LICENSE.txt
 * @link       http://sms77.io
 */

defined('_JEXEC') or die;

use Joomla\CMS\Http\Http;

/**
 * Sms77api helper.
 * @property Http http
 * @property  string apiKey
 * @package sms77api
 * @since    1.0.0
 */
class Sms77apiHelper {
    const baseURL = 'https://gateway.sms77.io/api/';

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
        $this->http = new Http;
    }

    public function balance() {
        $balance = $this->get(self::baseURL . "balance?p={$this->apiKey}");

        return null === $balance ? null : (float)$balance;
    }

    public function sms(array $args) {
        $qs = http_build_query(array_merge($args, ['json' => 1]));

        $res = json_decode($this->get(self::baseURL . "sms?p={$this->apiKey}&$qs"), true);

        return '100' === $res['success'] ? $res : null;
    }

    public function isValidApiKey() {
        return is_float($this->balance());
    }

    public function get($url) {
        $res = $this->http->get($url);

        if (200 === $res->code) {
            return $res->body;
        }

        return null;
    }
}