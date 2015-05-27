<?php
/*
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace alxmsl\Google\AndroidPublisher\Purchases\Subscriptions;

use alxmsl\Google\InitializationInterface;

/**
 * Class of in-app product subscription resource
 * @author alxmsl
 */
final class Resource implements InitializationInterface {
    /**
     * @var string whether the subscription will automatically be renewed when it reaches its current expiry time
     */
    private $autoRenewing = '';

    /**
     * @var int time at which the subscription will expire, milliseconds
     */
    private $expiryTimeMillis = 0;

    /**
     * @var string represents an in-app purchase object in the android publisher service
     */
    private $kind = '';

    /**
     * @var int time at which the subscription was granted, milliseconds
     */
    private $startTimeMillis = 0;

    /**
     * @return int time at which the subscription will expire, milliseconds
     */
    public function getExpiryTimeMillis() {
        return $this->expiryTimeMillis;
    }

    /**
     * @return float time at which the subscription will expire, seconds
     */
    public function getExpiryTime() {
        return $this->getExpiryTimeMillis() / 1000;
    }

    /**
     * @return bool check if subscription is expired now
     */
    public function isExpired() {
        return $this->getExpiryTime() > time();
    }

    /**
     * @return string whether the subscription will automatically be renewed when it reaches its current expiry time
     */
    public function isAutoRenewing() {
        return $this->autoRenewing;
    }

    /**
     * @return string represents an in-app purchase object in the android publisher service
     */
    public function getKind() {
        return $this->kind;
    }

    /**
     * @return int time at which the subscription was granted, milliseconds
     */
    public function getStartTimeMillis() {
        return $this->startTimeMillis;
    }

    /**
     * @inheritdoc
     */
    public static function initializeByString($string) {
        $Object   = json_decode($string);
        $Resource = new Resource();

        $Resource->autoRenewing     = (bool) $Object->autoRenewing;
        $Resource->expiryTimeMillis = (int) $Object->expiryTimeMillis;
        $Resource->kind             = (string) $Object->kind;
        $Resource->startTimeMillis  = (int) $Object->startTimeMillis;
        return $Resource;
    }

    /**
     * @inheritdoc
     */
    public function __toString() {
        $format = <<<'EOD'
    started at:    %s
    expired at:    %s
    kind:          %s
    auto-renewing: %s
EOD;
        return sprintf($format
            , date('Y-m-d H:i:s', $this->getStartTimeMillis() / 1000)
            , date('Y-m-d H:i:s', $this->getExpiryTimeMillis() / 1000)
            , $this->getKind()
            , $this->isAutoRenewing() ? 'enabled' : 'disabled');
    }
}
