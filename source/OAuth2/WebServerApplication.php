<?php
/*
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace alxmsl\Google\OAuth2;
use alxmsl\Google\OAuth2\Response\Error;
use alxmsl\Google\OAuth2\Response\Token;
use alxmsl\Network\Http\HttpClientErrorCodeException;
use alxmsl\Network\Http\HttpCodeException;

/**
 * Class for login via web server applications
 * @author alxmsl
 * @date 1/13/13
 */ 
class WebServerApplication extends Client {
    /**
     * Response type constants
     */
    const RESPONSE_TYPE_CODE = 'code';

    /**
     * Access type constants
     */
    const ACCESS_TYPE_ONLINE  = 'online',
          ACCESS_TYPE_OFFLINE = 'offline';

    /**
     * Approval constants
     */
    const APPROVAL_PROMPT_AUTO  = 'auto',
          APPROVAL_PROMPT_FORCE = 'force';

    /**
     * Grant type constants
     */
    const GRANT_TYPE_AUTHORIZATION = 'authorization_code',
          GRANT_TYPE_REFRESH       = 'refresh_token';

    /**
     * Google Api endpoints
     */
    const ENDPOINT_INITIAL_REQUEST      = 'https://accounts.google.com/o/oauth2/auth',
          ENDPOINT_ACCESS_TOKEN_REQUEST = 'https://accounts.google.com/o/oauth2/token',
          ENDPOINT_REVOKE_TOKEN         = 'https://accounts.google.com/o/oauth2/revoke';

    /**
     * @var Token access token
     */
    private $Token = null;

    /**
     * Setter for token value
     * @param Token $Token token object
     * @return Client self
     */
    public function setToken(Token $Token) {
        $this->Token = $Token;
        $this->setAccessToken($this->Token->getAccessToken());
        return $this;
    }

    /**
     * Getter for token
     * @return Token access token value
     */
    public function getToken() {
        return $this->Token;
    }

    /**
     * Method for create authorization url
     * @param string[] $scopes set of permissions
     * @param string $state something state
     * @param string $responseType type of the response
     * @param string $accessType type of the access. Online or offline
     * @param string $approvalPrompt type of re-prompted user consent
     * @return string url string for user authorization
     */
    public function createAuthUrl(array $scopes, $state = '', $responseType = self::RESPONSE_TYPE_CODE, $accessType = self::ACCESS_TYPE_ONLINE, $approvalPrompt = self::APPROVAL_PROMPT_AUTO, $loginHint = '') {
        $parameters = array(
            'response_type=' . $responseType,
            'client_id=' . $this->getClientId(),
            'redirect_uri=' . urlencode($this->getRedirectUri()),
            'scope=' . urlencode(implode(' ', $scopes)),
            'access_type=' . $accessType,
            'approval_prompt=' . $approvalPrompt,
        );
        if (!empty($state)) {
            $parameters['state'] = $state;
        }
        if (!empty($loginHint)) {
            $parameters['login_hint'] = $loginHint;
        }
        return self::ENDPOINT_INITIAL_REQUEST . '?' . implode('&', $parameters);
    }

    /**
     * Get access token by user authorization code
     * @param string $code user authorization code
     * @return Error|Token Google Api response object
     */
    public function authorizeByCode($code) {
        $Request = $this->getRequest(self::ENDPOINT_ACCESS_TOKEN_REQUEST);
        $Request->addPostField('code', $code)
            ->addPostField('client_id', $this->getClientId())
            ->addPostField('client_secret', $this->getClientSecret())
            ->addPostField('redirect_uri', $this->getRedirectUri())
            ->addPostField('grant_type', self::GRANT_TYPE_AUTHORIZATION);
        try {
            $Token = Token::initializeByString($Request->send());
            $this->setToken($Token);
            return $Token;
        } catch (HttpClientErrorCodeException $ex) {
            return Error::initializeByString($ex->getMessage());
        }
    }

    /**
     * Get access by refresh token
     * @param string $refreshToken refresh token
     * @return Error|Token Google Api response object
     */
    public function refresh($refreshToken) {
        $Request = $this->getRequest(self::ENDPOINT_ACCESS_TOKEN_REQUEST);
        $Request->addPostField('client_id', $this->getClientId())
            ->addPostField('client_secret', $this->getClientSecret())
            ->addPostField('refresh_token', $refreshToken)
            ->addPostField('grant_type', self::GRANT_TYPE_REFRESH);
        try {
            $Token = Token::initializeByString($Request->send());
            $this->setToken($Token);
            return $Token;
        } catch (HttpClientErrorCodeException $ex) {
            return Error::initializeByString($ex->getMessage());
        }
    }

    /**
     * Revoke access or refresh token
     * If the token is an access token and it has a corresponding refresh token, the refresh token will also be revoked
     * @param string $token user access or refresh token
     * @return bool revoke token result
     */
    public function revoke($token) {
        $Request = $this->getRequest(self::ENDPOINT_REVOKE_TOKEN);
        $Request->addGetField('token', (string) $token);
        try {
            $Request->send();
            return true;
        } catch (HttpCodeException $Ex) {
            return false;
        }
    }
}
