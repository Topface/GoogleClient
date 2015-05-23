<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 *
 * Script for Google API authorization
 * @author alxmsl
 */

include __DIR__ . '/../vendor/autoload.php';

use alxmsl\Cli\CommandPosix;
use alxmsl\Cli\Exception\RequiredOptionException;
use alxmsl\Cli\Option;
use alxmsl\Google\OAuth2\Response\Token;
use alxmsl\Google\OAuth2\WebServerApplication;

$clientId     = '';
$clientSecret = '';
$code         = '';
$redirectUri  = '';
$scopes       = [];

$Command = new CommandPosix();
$Command->appendHelpParameter('show help');
$Command->appendParameter(new Option('code', 'o', 'authorization code', Option::TYPE_STRING)
    , function($name, $value) use (&$code) {
        $code = $value;
    });
$Command->appendParameter(new Option('client', 'c', 'client id', Option::TYPE_STRING, true)
    , function($name, $value) use (&$clientId) {
        $clientId = $value;
    });
$Command->appendParameter(new Option('redirect', 'r', 'redirect uri', Option::TYPE_STRING)
    , function($name, $value) use (&$redirectUri) {
        $redirectUri = $value;
    });
$Command->appendParameter(new Option('scopes', 's', 'grant scopes', Option::TYPE_STRING)
    , function($name, $value) use (&$scopes) {
        $scopes = explode(',', $value);
    });
$Command->appendParameter(new Option('secret', 'e', 'client secret', Option::TYPE_STRING, true)
    , function($name, $value) use (&$clientSecret) {
        $clientSecret = $value;
    });

try {
    $Command->parse(true);
    // Create new client
    $Client = new WebServerApplication();
    $Client->setClientId($clientId)
        ->setClientSecret($clientSecret)
        ->setRedirectUri($redirectUri);

    if (!empty($code)) {
        // Get authorization token for access application
        $Token = $Client->authorizeByCode($code);
        if ($Token instanceof Token) {
            printf("%s\n", (string) $Token);
        } else {
            var_dump($Token);
        }
    } else {
        // Get authorization uri
        $uri = $Client->createAuthUrl($scopes
            , ''
            , WebServerApplication::RESPONSE_TYPE_CODE
            , WebServerApplication::ACCESS_TYPE_OFFLINE
            // Use FORCE to get new refresh token for offline access type
            , WebServerApplication::APPROVAL_PROMPT_FORCE);
        printf("%s\n", $uri);
    }
} catch (RequiredOptionException $Ex) {
    $Command->displayHelp();
}
