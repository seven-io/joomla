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
        $balance = $this->get('balance');

        return null === $balance ? null : (float)$balance;
    }

    public function sms(array $args) {
        $res = $this->post('sms', array_merge($args, ['json' => 1]));

        return in_array($res['success'], ['100', '101']) ? $res : null;
    }

    public function isValidApiKey() {
        return is_float($this->balance());
    }

    private function makeUrl($endpoint) {
        return self::baseURL . $endpoint;
    }

    private function makeHeaders() {
        return ['Authorization' => "Bearer $this->apiKey", 'sentWith' => 'Joomla',];
    }

    private function handleResponse($res) {
        return 200 === $res->code ? json_decode($res->body, true) : null;
    }

    public function get($endpoint, array $args = []) {
        $url = $this->makeUrl($endpoint);
        if (count($args)) {
            $url .= '?' . http_build_query(array_merge($args));
        }

        return $this->handleResponse($this->http->get($url, $this->makeHeaders()));
    }

    public function post($endpoint, array $args = []) {
        return $this->handleResponse(
            $this->http->post($this->makeUrl($endpoint), $args, $this->makeHeaders()));
    }
}