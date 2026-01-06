<?php

namespace Seven\Component\Seven\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Seven\Component\Seven\Administrator\Service\OAuthService;

class OauthController extends BaseController
{
    /**
     * Start OAuth connection flow
     */
    public function connect()
    {
        // Check for CSRF token
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('JINVALID_TOKEN'), 'error');
            $this->app->redirect(Route::_('index.php?option=com_seven&view=configurations', false));
            return;
        }

        $oauthService = new OAuthService();

        // Generate PKCE values
        $codeVerifier = $oauthService->generateCodeVerifier();
        $codeChallenge = $oauthService->generateCodeChallenge($codeVerifier);
        $state = $oauthService->generateState();

        // Store in session for callback verification
        $oauthService->storeInSession($state, $codeVerifier);

        // Redirect to seven.io authorization
        $authUrl = $oauthService->getAuthorizationUrl($state, $codeChallenge);

        $this->app->redirect($authUrl);
    }

    /**
     * Handle OAuth callback from seven.io
     */
    public function callback()
    {
        $input = $this->input;
        $oauthService = new OAuthService();

        // Get callback parameters
        $code = $input->getString('code');
        $state = $input->getString('state');
        $error = $input->getString('error');
        $errorDescription = $input->getString('error_description');

        // Handle error from OAuth provider
        if ($error) {
            $this->app->enqueueMessage(
                Text::sprintf('COM_SEVEN_OAUTH_ERROR', $errorDescription ?: $error),
                'error'
            );
            $this->app->redirect(Route::_('index.php?option=com_seven&view=configurations', false));
            return;
        }

        // Validate code is present
        if (empty($code)) {
            $this->app->enqueueMessage(Text::_('COM_SEVEN_OAUTH_NO_CODE'), 'error');
            $this->app->redirect(Route::_('index.php?option=com_seven&view=configurations', false));
            return;
        }

        // Get stored session values
        $sessionData = $oauthService->getFromSession();

        if ($sessionData === null) {
            $this->app->enqueueMessage(Text::_('COM_SEVEN_OAUTH_SESSION_EXPIRED'), 'error');
            $this->app->redirect(Route::_('index.php?option=com_seven&view=configurations', false));
            return;
        }

        // Validate state parameter (CSRF protection)
        if ($state !== $sessionData['state']) {
            $this->app->enqueueMessage(Text::_('COM_SEVEN_OAUTH_INVALID_STATE'), 'error');
            $this->app->redirect(Route::_('index.php?option=com_seven&view=configurations', false));
            return;
        }

        // Exchange code for token
        $tokens = $oauthService->exchangeCodeForToken($code, $sessionData['code_verifier']);

        if ($tokens === null) {
            $this->app->enqueueMessage(Text::_('COM_SEVEN_OAUTH_TOKEN_ERROR'), 'error');
            $this->app->redirect(Route::_('index.php?option=com_seven&view=configurations', false));
            return;
        }

        // Save tokens
        if ($oauthService->saveTokens($tokens)) {
            $this->app->enqueueMessage(Text::_('COM_SEVEN_OAUTH_CONNECTED'), 'success');
        } else {
            $this->app->enqueueMessage(Text::_('COM_SEVEN_OAUTH_SAVE_ERROR'), 'error');
        }

        $this->app->redirect(Route::_('index.php?option=com_seven&view=configurations', false));
    }

    /**
     * Disconnect OAuth (remove tokens)
     */
    public function disconnect()
    {
        // Check for CSRF token
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('JINVALID_TOKEN'), 'error');
            $this->app->redirect(Route::_('index.php?option=com_seven&view=configurations', false));
            return;
        }

        $oauthService = new OAuthService();

        if ($oauthService->deleteTokens()) {
            $this->app->enqueueMessage(Text::_('COM_SEVEN_OAUTH_DISCONNECTED'), 'success');
        } else {
            $this->app->enqueueMessage(Text::_('COM_SEVEN_OAUTH_DISCONNECT_ERROR'), 'error');
        }

        $this->app->redirect(Route::_('index.php?option=com_seven&view=configurations', false));
    }
}
