<?php

namespace Google\Client\InAppPurchases;

use Google\Client\InAppPurchases\Response\Error;
use Google\Client\InAppPurchases\Response\Resource;
use Google\Client\OAuth2\WebServerApplication;

/**
 * Class for support Google InApp Purchases API
 * @author alxmsl
 * @date 12/4/13
 */ 
final class InAppPurchases extends WebServerApplication {
    /**
     * Google Api InAppPurchases endpoint
     */
    const ENDPOINT_PURCHASES = 'https://www.googleapis.com/androidpublisher/v1.1';

    /**
     * @var string package name
     */
    private $package = '';

    /**
     * Setter for package name
     * @param string $package package name
     * @return InAppPurchases self
     */
    public function setPackage($package) {
        $this->package = (string) $package;
        return $this;
    }

    /**
     * Getter for package name
     * @return string package name
     */
    public function getPackage() {
        return $this->package;
    }

    /**
     * Check user subscription
     * @param string $productId product identifier
     * @param string $token subscription identifier
     * @return Resource|Error user subscription data
     * @throws \UnexpectedValueException when access token not presented
     */
    public function get($productId, $token) {
        $accessToken = $this->getAccessToken();
        if (!empty($accessToken)) {
            $Request = $this->getRequest(self::ENDPOINT_PURCHASES)
                ->addUrlField('applications', $this->getPackage())
                ->addUrlField('inapp', $productId)
                ->addUrlField('purchases', $token)
                ->addGetField('access_token', $accessToken);
            try {
                return Resource::initializeByString($Request->send());
            } catch (\Network\Http\HttpClientErrorCodeException $ex) {
                switch ($ex->getCode()) {
                    case 400:
                    case 401:
                    case 404:
                        $Error = Error::initializeByString($ex->getMessage());
                        if ($Error->getCode()) {
                            return $Error;
                        } else {
                            throw $ex;
                        }
                    default:
                        throw $ex;
                }
            } catch (\Network\Http\HttpServerErrorCodeException $ex) {
                switch ($ex->getCode()) {
                    case 500:
                        return Error::initializeByString($ex->getMessage());
                    default:
                        throw $ex;
                }
            }
        } else {
            throw new \UnexpectedValueException('access token is empty');
        }
    }
}
 