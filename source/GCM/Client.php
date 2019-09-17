<?php
/*
 * Copyright 2015 Alexey Maslov <alexey.y.maslov@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace alxmsl\Google\GCM;

use alxmsl\Google\GCM\Exception\GCMFormatException;
use alxmsl\Google\GCM\Exception\GCMServerError;
use alxmsl\Google\GCM\Exception\GCMUnauthorizedException;
use alxmsl\Google\GCM\Exception\GCMUnrecoverableError;
use alxmsl\Google\GCM\Message\PayloadMessage;
use alxmsl\Google\GCM\Response\Response;
use alxmsl\Network\Exception\HttpClientErrorCodeException;
use alxmsl\Network\Exception\HttpServerErrorCodeException;
use alxmsl\Network\Http\Request;

/**
 * GCM sender client class
 * @author alxmsl
 * @date 5/26/13
 */ 
final class Client {
    /**
     * Supported content types
     */
    const CONTENT_TYPE_JSON       = 'application/json',
          CONTENT_TYPE_PLAIN_TEXT = 'application/x-www-form-urlencoded; charset=UTF-8';

    /**
     * GCM sender service endpoint
     */
    const ENDPOINT_SEND = 'https://fcm.googleapis.com/fcm/send';

    /**
     * @var string authorization key
     */
    private $authorizationKey = '';

    /**
     * @var null|Request HTTP request instance
     */
    private $Request = null;

    /**
     * Authorization key setter
     * @param string $authorizationKey authorization key
     * @return Client self
     */
    public function setAuthorizationKey($authorizationKey) {
        $this->authorizationKey = (string) $authorizationKey;
        $this->getRequest()->addHeader('Authorization', 'key=' . $this->getAuthorizationKey());
        return $this;
    }

    /**
     * Authorization key getter
     * @return string authorization key
     */
    public function getAuthorizationKey() {
        return $this->authorizationKey;
    }

    /**
     * GCM service request instance getter
     * @return Request GCM service request instance
     */
    public function getRequest() {
        if (is_null($this->Request)) {
            $this->Request = new Request();
            $this->Request->setTransport(Request::TRANSPORT_CURL);
            $this->Request->setUrl(self::ENDPOINT_SEND);
        }
        return $this->Request;
    }

    /**
     * Send GCM message method
     * @param PayloadMessage $Message GCM message instance
     * @return Response sent response instance
     * @throws GCMFormatException when request or response format was incorrect
     * @throws GCMUnauthorizedException when was incorrect authorization key
     * @throws GCMServerError when something wrong on the GCM server
     * @throws GCMUnrecoverableError when GCM server is not available
     */
    public function send(PayloadMessage $Message) {
        switch ($Message->getType()) {
            case PayloadMessage::TYPE_PLAIN:
                $this->getRequest()->setContentTypeCode(Request::CONTENT_TYPE_UNDEFINED);
                $this->getRequest()->addHeader('Content-Type', self::CONTENT_TYPE_PLAIN_TEXT);
                break;
            case PayloadMessage::TYPE_JSON:
                $this->getRequest()->setContentTypeCode(Request::CONTENT_TYPE_JSON);
                $this->getRequest()->addHeader('Content-Type', self::CONTENT_TYPE_JSON);
                break;
            default:
                throw new GCMFormatException('unsupported request format code \'' . $Message->getType() . '\'');
        }
        $this->getRequest()->setPostData($Message->export());
        try {
            $result = $this->getRequest()->send();
            Response::$type = $Message->getType();
            return Response::initializeByString($result);
        } catch (HttpClientErrorCodeException $Ex) {
            switch ($Ex->getCode()) {
                case '400':
                    throw new GCMFormatException('invalid JSON request with message \'' . $Ex->getMessage() . '\'');
                case '401':
                    throw new GCMUnauthorizedException('invalid authorization key \'' . $this->getAuthorizationKey() . '\'');
                default:
                    throw $Ex;
            }
        } catch (HttpServerErrorCodeException $Ex) {
            switch ($Ex->getCode()) {
                case '500':
                    throw new GCMUnrecoverableError('unrecoverable GCM server error');
                default:
                    $headers = $this->getRequest()->getResponseHeaders();
                    throw new GCMServerError(@$headers['Retry-After']);
            }
        }
    }
}
