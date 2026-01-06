<?php

namespace Seven\Component\Seven\Administrator\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;

class OAuthService
{
    private const CLIENT_ID = 'joomla';
    private const AUTHORIZE_URL = 'https://oauth.seven.io/authorize';
    private const TOKEN_URL = 'https://oauth.seven.io/token';
    private const REVOKE_URL = 'https://oauth.seven.io/revoke';

    /**
     * Generate a cryptographically secure code verifier for PKCE
     */
    public function generateCodeVerifier(): string
    {
        $bytes = random_bytes(32);
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }

    /**
     * Generate code challenge from verifier (S256 method)
     */
    public function generateCodeChallenge(string $verifier): string
    {
        $hash = hash('sha256', $verifier, true);
        return rtrim(strtr(base64_encode($hash), '+/', '-_'), '=');
    }

    /**
     * Generate a random state parameter for CSRF protection
     */
    public function generateState(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * Get the OAuth callback URL for this Joomla installation
     */
    public function getCallbackUrl(): string
    {
        return Uri::base() . 'index.php?option=com_seven&task=oauth.callback';
    }

    /**
     * Get the full authorization URL with all parameters
     */
    public function getAuthorizationUrl(string $state, string $codeChallenge): string
    {
        $params = [
            'response_type' => 'code',
            'client_id' => self::CLIENT_ID,
            'redirect_uri' => $this->getCallbackUrl(),
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ];

        return self::AUTHORIZE_URL . '?' . http_build_query($params);
    }

    /**
     * Exchange authorization code for access token
     */
    public function exchangeCodeForToken(string $code, string $codeVerifier): ?array
    {
        $http = HttpFactory::getHttp();

        $data = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->getCallbackUrl(),
            'client_id' => self::CLIENT_ID,
            'code_verifier' => $codeVerifier,
        ];

        try {
            $response = $http->post(
                self::TOKEN_URL,
                http_build_query($data),
                ['Content-Type' => 'application/x-www-form-urlencoded']
            );

            if ($response->code === 200) {
                $result = json_decode($response->body, true);
                if (isset($result['access_token'])) {
                    return $result;
                }
            }
        } catch (\Exception $e) {
            // Log error if needed
        }

        return null;
    }

    /**
     * Refresh access token using refresh token
     */
    public function refreshAccessToken(string $refreshToken): ?array
    {
        $http = HttpFactory::getHttp();

        $data = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => self::CLIENT_ID,
        ];

        try {
            $response = $http->post(
                self::TOKEN_URL,
                http_build_query($data),
                ['Content-Type' => 'application/x-www-form-urlencoded']
            );

            if ($response->code === 200) {
                $result = json_decode($response->body, true);
                if (isset($result['access_token'])) {
                    return $result;
                }
            }
        } catch (\Exception $e) {
            // Log error if needed
        }

        return null;
    }

    /**
     * Get stored OAuth tokens from component parameters
     */
    public function getStoredTokens(): ?array
    {
        $params = ComponentHelper::getParams('com_seven');

        $accessToken = $params->get('oauth_access_token');
        $refreshToken = $params->get('oauth_refresh_token');
        $expiresAt = $params->get('oauth_expires_at');

        if (empty($accessToken)) {
            return null;
        }

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_at' => $expiresAt,
        ];
    }

    /**
     * Get valid access token (refreshes if expired)
     */
    public function getValidAccessToken(): ?string
    {
        $tokens = $this->getStoredTokens();

        if ($tokens === null) {
            return null;
        }

        // Check if token is expired (with 5 minute buffer)
        $expiresAt = strtotime($tokens['expires_at']);
        if ($expiresAt && $expiresAt < (time() + 300)) {
            // Token expired or expiring soon, try to refresh
            if (!empty($tokens['refresh_token'])) {
                $newTokens = $this->refreshAccessToken($tokens['refresh_token']);
                if ($newTokens) {
                    $this->saveTokens($newTokens);
                    return $newTokens['access_token'];
                }
            }
            // Refresh failed, token is invalid
            return null;
        }

        return $tokens['access_token'];
    }

    /**
     * Save OAuth tokens to component parameters
     */
    public function saveTokens(array $tokens): bool
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        // Calculate expiration time
        $expiresIn = $tokens['expires_in'] ?? 3600;
        $expiresAt = date('Y-m-d H:i:s', time() + $expiresIn);

        // Get existing params
        $params = ComponentHelper::getParams('com_seven');
        $params->set('oauth_access_token', $tokens['access_token']);
        $params->set('oauth_refresh_token', $tokens['refresh_token'] ?? '');
        $params->set('oauth_expires_at', $expiresAt);

        // Save to database
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__extensions'))
            ->set($db->quoteName('params') . ' = ' . $db->quote($params->toString()))
            ->where($db->quoteName('element') . ' = ' . $db->quote('com_seven'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'));

        try {
            $db->setQuery($query);
            $db->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Revoke token at seven.io OAuth server
     *
     * @param string $token The token to revoke
     * @param string $tokenTypeHint Either 'access_token' or 'refresh_token'
     */
    public function revokeToken(string $token, string $tokenTypeHint = 'access_token'): bool
    {
        $http = HttpFactory::getHttp();

        $data = [
            'token' => $token,
            'client_id' => self::CLIENT_ID,
            'token_type_hint' => $tokenTypeHint,
        ];

        try {
            $response = $http->post(
                self::REVOKE_URL,
                http_build_query($data),
                ['Content-Type' => 'application/x-www-form-urlencoded']
            );

            // Server returns 200 even if token was already invalid
            return $response->code === 200;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete stored OAuth tokens (and revoke at server)
     */
    public function deleteTokens(): bool
    {
        // First, revoke tokens at seven.io
        $tokens = $this->getStoredTokens();
        if ($tokens !== null && !empty($tokens['access_token'])) {
            $this->revokeToken($tokens['access_token'], 'access_token');

            // Also revoke refresh token if present
            if (!empty($tokens['refresh_token'])) {
                $this->revokeToken($tokens['refresh_token'], 'refresh_token');
            }
        }

        // Then delete local tokens
        $db = Factory::getContainer()->get('DatabaseDriver');

        $params = ComponentHelper::getParams('com_seven');
        $params->set('oauth_access_token', '');
        $params->set('oauth_refresh_token', '');
        $params->set('oauth_expires_at', '');

        $query = $db->getQuery(true)
            ->update($db->quoteName('#__extensions'))
            ->set($db->quoteName('params') . ' = ' . $db->quote($params->toString()))
            ->where($db->quoteName('element') . ' = ' . $db->quote('com_seven'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'));

        try {
            $db->setQuery($query);
            $db->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if OAuth is connected
     */
    public function isConnected(): bool
    {
        return $this->getValidAccessToken() !== null;
    }

    /**
     * Store PKCE values in session for callback verification
     */
    public function storeInSession(string $state, string $codeVerifier): void
    {
        $session = Factory::getApplication()->getSession();
        $session->set('com_seven.oauth_state', $state);
        $session->set('com_seven.oauth_code_verifier', $codeVerifier);
    }

    /**
     * Get and clear PKCE values from session
     */
    public function getFromSession(): ?array
    {
        $session = Factory::getApplication()->getSession();

        $state = $session->get('com_seven.oauth_state');
        $codeVerifier = $session->get('com_seven.oauth_code_verifier');

        // Clear session values
        $session->clear('com_seven.oauth_state');
        $session->clear('com_seven.oauth_code_verifier');

        if (empty($state) || empty($codeVerifier)) {
            return null;
        }

        return [
            'state' => $state,
            'code_verifier' => $codeVerifier,
        ];
    }
}
