<?php
/**
 * @package seven
 * @author seven communications GmbH & Co. KG <support@seven.io>
 * @copyright  2020-present seven communications GmbH & Co. KG
 * @license    MIT; see LICENSE.txt
 * @link       http://www.seven.io
 */

namespace Seven\Joomla\helpers;

use Joomla\CMS\Http\Http;

defined('_JEXEC') or die;

/**
 * Seven helper.
 * @package seven
 * @since    1.0.0
 */
class SevenHelper {
    const baseURL = 'https://gateway.seven.io/api/';
    /**
     * @var Http
     * @since 1.0.0
     */
    private $http;
    /**
     * @var string
     * @since 1.0.0
     */
    private $apiKey;

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
        $this->http = new Http;
    }

    public function balance() {
        $balance = $this->get('balance');

        return null === $balance ? null : (float)$balance;
    }

    public function sms(array $args) {
        $res = json_decode(
            $this->post('sms', array_merge($args, ['json' => 1])), true);

        return in_array($res['success'], ['100', '101']) ? $res : null;
    }

    public function voice(array $args) {
        $res = $this->post('voice', $args);

        if (null === $res) {
            return null;
        }

        $lines = explode(PHP_EOL, $res);

        return [
            'code' => $lines[0],
            'seven_id' =>
                array_key_exists(1, $lines) && '' !== $lines[1] ? $lines[1] : null,
            'eur' => array_key_exists(2, $lines) ? $lines[2] : null,
        ];
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
        return 200 === $res->code ? trim($res->body) : null;
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
