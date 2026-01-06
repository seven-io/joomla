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

use Joomla\CMS\Http\HttpFactory;

/**
 * API client for seven.io services
 *
 * @since  3.0.0
 */
class SevenApiClient
{
    /**
     * Base URL for the seven.io API
     *
     * @var string
     */
    private const BASE_URL = 'https://gateway.seven.io/api/';

    /**
     * API key for authentication
     *
     * @var string
     */
    private string $apiKey;

    /**
     * Constructor
     *
     * @param   string  $apiKey  The seven.io API key
     *
     * @since   3.0.0
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Get account balance
     *
     * @return  float|null  The account balance or null on failure
     *
     * @since   3.0.0
     */
    public function balance(): ?float
    {
        $response = $this->get('balance');

        return $response !== null ? (float) $response : null;
    }

    /**
     * Validate API key by checking balance
     *
     * @return  bool  True if API key is valid
     *
     * @since   3.0.0
     */
    public function isValidApiKey(): bool
    {
        return $this->balance() !== null;
    }

    /**
     * Send SMS message
     *
     * @param   array  $params  Parameters: to, text, from, flash, unicode, utf8, delay, ttl, foreign_id, label, udh, no_reload, performance_tracking
     *
     * @return  array|null  Response from API or null on failure
     *
     * @since   3.0.0
     */
    public function sendSms(array $params): ?array
    {
        // Ensure JSON response
        $params['json'] = 1;

        $response = $this->post('sms', $params);

        if ($response === null) {
            return null;
        }

        $decoded = json_decode($response, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Send voice call
     *
     * @param   array  $params  Parameters: to, text, from
     *
     * @return  array|null  Response from API or null on failure
     *
     * @since   3.0.0
     */
    public function sendVoice(array $params): ?array
    {
        // Ensure JSON response
        $params['json'] = 1;

        $response = $this->post('voice', $params);

        if ($response === null) {
            return null;
        }

        $decoded = json_decode($response, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Lookup phone number
     *
     * @param   string  $number  The phone number to lookup
     * @param   string  $type    Lookup type: format, cnam, hlr, mnp
     *
     * @return  array|null  Lookup result or null on failure
     *
     * @since   3.0.0
     */
    public function lookup(string $number, string $type = 'format'): ?array
    {
        $response = $this->post('lookup', [
            'number' => $number,
            'type' => $type,
            'json' => 1,
        ]);

        if ($response === null) {
            return null;
        }

        $decoded = json_decode($response, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Get pricing information
     *
     * @param   string|null  $country  Optional country code (ISO 3166-1 alpha-2)
     *
     * @return  array|null  Pricing information or null on failure
     *
     * @since   3.0.0
     */
    public function pricing(?string $country = null): ?array
    {
        $params = ['format' => 'json'];

        if ($country !== null) {
            $params['country'] = $country;
        }

        $response = $this->get('pricing', $params);

        if ($response === null) {
            return null;
        }

        $decoded = json_decode($response, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Get SMS status
     *
     * @param   string  $msgId  The message ID
     *
     * @return  array|null  Status information or null on failure
     *
     * @since   3.0.0
     */
    public function status(string $msgId): ?array
    {
        $response = $this->get('status', [
            'msg_id' => $msgId,
        ]);

        if ($response === null) {
            return null;
        }

        // Status API returns: STATUS\nTIMESTAMP
        $lines = explode("\n", trim($response));

        return [
            'status' => $lines[0] ?? '',
            'timestamp' => $lines[1] ?? null,
        ];
    }

    /**
     * Perform GET request
     *
     * @param   string  $endpoint  API endpoint
     * @param   array   $params    Query parameters
     *
     * @return  string|null  Response body or null on failure
     *
     * @since   3.0.0
     */
    private function get(string $endpoint, array $params = []): ?string
    {
        return $this->request('GET', $endpoint, $params);
    }

    /**
     * Perform POST request
     *
     * @param   string  $endpoint  API endpoint
     * @param   array   $params    POST parameters
     *
     * @return  string|null  Response body or null on failure
     *
     * @since   3.0.0
     */
    private function post(string $endpoint, array $params = []): ?string
    {
        return $this->request('POST', $endpoint, $params);
    }

    /**
     * Perform HTTP request
     *
     * @param   string  $method    HTTP method (GET or POST)
     * @param   string  $endpoint  API endpoint
     * @param   array   $params    Request parameters
     *
     * @return  string|null  Response body or null on failure
     *
     * @since   3.0.0
     */
    private function request(string $method, string $endpoint, array $params = []): ?string
    {
        $http = HttpFactory::getHttp();

        $headers = [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
            'SentWith' => 'Joomla',
        ];

        $url = self::BASE_URL . $endpoint;

        try {
            if ($method === 'GET') {
                if (!empty($params)) {
                    $url .= '?' . http_build_query($params);
                }

                $response = $http->get($url, $headers);
            } else {
                $headers['Content-Type'] = 'application/x-www-form-urlencoded';
                $response = $http->post($url, http_build_query($params), $headers);
            }

            if ($response->code >= 200 && $response->code < 300) {
                return $response->body;
            }
        } catch (\Exception $e) {
            // Silently fail - caller should handle null response
        }

        return null;
    }
}
