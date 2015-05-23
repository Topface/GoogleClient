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
use alxmsl\Google\AndroidPublisher\InAppProducts\Client;

$accessToken = '';
$packageName = '';
$productId   = '';

$Command = new CommandPosix();
$Command->appendHelpParameter('show help');
$Command->appendParameter(new Option('access', 'a', 'access token', Option::TYPE_STRING, true)
    , function($name, $value) use (&$accessToken) {
        $accessToken = $value;
    });
$Command->appendParameter(new Option('package', 'p', 'package name', Option::TYPE_STRING)
    , function($name, $value) use (&$packageName) {
        $packageName = $value;
    });
$Command->appendParameter(new Option('product', 'r', 'product id', Option::TYPE_STRING, true)
    , function($name, $value) use (&$productId) {
        $productId = $value;
    });

try {
    $Command->parse(true);
    $Client = new Client();
    $Client->setPackage($packageName)
        ->setAccessToken($accessToken);
    $Resource = $Client->get($productId);
    printf("%s\n", (string) $Resource);
} catch (RequiredOptionException $Ex) {
    $Command->displayHelp();
}
