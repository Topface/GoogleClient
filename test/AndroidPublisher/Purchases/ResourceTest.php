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

namespace alxmsl\Test\Google\AndroidPublisher\Purchases;

use alxmsl\Google\AndroidPublisher\Purchases\Products\Resource as PurchasesResource;
use alxmsl\Google\AndroidPublisher\Purchases\Subscriptions\Resource as SubscriptionsResource;
use PHPUnit_Framework_TestCase;

/**
 * Android Publisher Resources test class
 * @author alxmsl
 */
final class ResourceTest extends PHPUnit_Framework_TestCase {
    public function testProductsResourceInitialState() {
        $Resource = new PurchasesResource();
        $this->assertEquals(0, $Resource->getConsumptionState());
        $this->assertEmpty($Resource->getDeveloperPayload());
        $this->assertEmpty($Resource->getKind());
        $this->assertEquals(PurchasesResource::ORDER_UNKNOWN, $Resource->getPurchaseState());
        $this->assertEquals(0, $Resource->getPurchaseTimeMillis());
        $this->assertFalse($Resource->isCancelled());
        $this->assertFalse($Resource->isConsumed());
        $this->assertFalse($Resource->isPurchased());
    }

    public function testProductsResourceInitialization() {
        $Resource1 = PurchasesResource::initializeByString('{
 "kind": "androidpublisher#productPurchase",
 "purchaseTimeMillis": "1434571586008",
 "purchaseState": 0,
 "consumptionState": 1,
 "developerPayload": "{\"codeVersion\":30700,\"hash\":862428815}"
}');
        $this->assertEquals(PurchasesResource::STATE_CONSUMED, $Resource1->getConsumptionState());
        $this->assertEquals('{"codeVersion":30700,"hash":862428815}', $Resource1->getDeveloperPayload());
        $this->assertEquals('androidpublisher#productPurchase', $Resource1->getKind());
        $this->assertEquals(PurchasesResource::ORDER_PURCHASED, $Resource1->getPurchaseState());
        $this->assertSame(1434571586008, $Resource1->getPurchaseTimeMillis());
        $this->assertFalse($Resource1->isCancelled());
        $this->assertTrue($Resource1->isConsumed());
        $this->assertTrue($Resource1->isPurchased());
        $this->assertEquals('    consumptionState:   consumed
    developerPayload:   {"codeVersion":30700,"hash":862428815}
    kind:               androidpublisher#productPurchase
    purchaseState:      purchased
    purchaseTimeMillis: 2015-06-17 23:06:26', (string) $Resource1);

        $Resource2 = PurchasesResource::initializeByString('{
 "kind": "androidpublisher#productPurchase",
 "purchaseTimeMillis": "1434571586008",
 "purchaseState": 0,
 "consumptionState": 0,
 "developerPayload": "{\"codeVersion\":30700,\"hash\":862428815}"
}');
        $this->assertEquals(PurchasesResource::STATE_YET_CONSUMED, $Resource2->getConsumptionState());
        $this->assertEquals('{"codeVersion":30700,"hash":862428815}', $Resource2->getDeveloperPayload());
        $this->assertEquals('androidpublisher#productPurchase', $Resource2->getKind());
        $this->assertEquals(PurchasesResource::ORDER_PURCHASED, $Resource2->getPurchaseState());
        $this->assertSame(1434571586008, $Resource2->getPurchaseTimeMillis());
        $this->assertFalse($Resource2->isCancelled());
        $this->assertFalse($Resource2->isConsumed());
        $this->assertTrue($Resource2->isPurchased());
        $this->assertEquals('    consumptionState:   yet to be consumed
    developerPayload:   {"codeVersion":30700,"hash":862428815}
    kind:               androidpublisher#productPurchase
    purchaseState:      purchased
    purchaseTimeMillis: 2015-06-17 23:06:26', (string) $Resource2);

        $Resource3 = PurchasesResource::initializeByString('{
 "kind": "androidpublisher#productPurchase",
 "purchaseTimeMillis": "1434571586008",
 "purchaseState": 1,
 "consumptionState": 0,
 "developerPayload": "{\"codeVersion\":30700,\"hash\":862428815}"
}');
        $this->assertEquals(PurchasesResource::STATE_YET_CONSUMED, $Resource3->getConsumptionState());
        $this->assertEquals('{"codeVersion":30700,"hash":862428815}', $Resource3->getDeveloperPayload());
        $this->assertEquals('androidpublisher#productPurchase', $Resource3->getKind());
        $this->assertEquals(PurchasesResource::ORDER_CANCELLED, $Resource3->getPurchaseState());
        $this->assertSame(1434571586008, $Resource3->getPurchaseTimeMillis());
        $this->assertTrue($Resource3->isCancelled());
        $this->assertFalse($Resource3->isConsumed());
        $this->assertFalse($Resource3->isPurchased());
        $this->assertEquals('    consumptionState:   yet to be consumed
    developerPayload:   {"codeVersion":30700,"hash":862428815}
    kind:               androidpublisher#productPurchase
    purchaseState:      cancelled
    purchaseTimeMillis: 2015-06-17 23:06:26', (string) $Resource3);

        $Resource4 = PurchasesResource::initializeByString('{
 "kind": "androidpublisher#productPurchase",
 "purchaseTimeMillis": "1434571586008",
 "purchaseState": 1,
 "consumptionState": 1,
 "developerPayload": "{\"codeVersion\":30700,\"hash\":862428815}"
}');
        $this->assertEquals(PurchasesResource::STATE_CONSUMED, $Resource4->getConsumptionState());
        $this->assertEquals('{"codeVersion":30700,"hash":862428815}', $Resource4->getDeveloperPayload());
        $this->assertEquals('androidpublisher#productPurchase', $Resource4->getKind());
        $this->assertEquals(PurchasesResource::ORDER_CANCELLED, $Resource4->getPurchaseState());
        $this->assertSame(1434571586008, $Resource4->getPurchaseTimeMillis());
        $this->assertTrue($Resource4->isCancelled());
        $this->assertTrue($Resource4->isConsumed());
        $this->assertFalse($Resource4->isPurchased());
        $this->assertEquals('    consumptionState:   consumed
    developerPayload:   {"codeVersion":30700,"hash":862428815}
    kind:               androidpublisher#productPurchase
    purchaseState:      cancelled
    purchaseTimeMillis: 2015-06-17 23:06:26', (string) $Resource4);
    }
    
    public function testSubscriptionsResourceInitialState() {
        $Resource = new SubscriptionsResource();
        $this->assertEquals(0, $Resource->getExpiryTime());
        $this->assertEquals(0, $Resource->getExpiryTimeMillis());
        $this->assertEmpty($Resource->getKind());
        $this->assertEquals(0, $Resource->getStartTimeMillis());
        $this->assertFalse($Resource->isAutoRenewing());
        $this->assertTrue($Resource->isExpired());
    }

    public function testSubscriptionsResourceInitialization() {
        $Resource1 = SubscriptionsResource::initializeByString('{
 "kind": "androidpublisher#subscriptionPurchase",
 "startTimeMillis": "1434573608845",
 "expiryTimeMillis": "100000",
 "autoRenewing": true
}');
        $this->assertSame(100, $Resource1->getExpiryTime());
        $this->assertSame(100000, $Resource1->getExpiryTimeMillis());
        $this->assertEquals('androidpublisher#subscriptionPurchase', $Resource1->getKind());
        $this->assertSame(1434573608845, $Resource1->getStartTimeMillis());
        $this->assertTrue($Resource1->isAutoRenewing());
        $this->assertTrue($Resource1->isExpired());

        $Resource2 = SubscriptionsResource::initializeByString('{
 "kind": "androidpublisher#subscriptionPurchase",
 "startTimeMillis": "1434573608845",
 "expiryTimeMillis": "100000",
 "autoRenewing": false
}');
        $this->assertSame(100, $Resource2->getExpiryTime());
        $this->assertSame(100000, $Resource2->getExpiryTimeMillis());
        $this->assertEquals('androidpublisher#subscriptionPurchase', $Resource2->getKind());
        $this->assertSame(1434573608845, $Resource2->getStartTimeMillis());
        $this->assertFalse($Resource2->isAutoRenewing());
        $this->assertTrue($Resource2->isExpired());
        $this->assertEquals('    started at:    2015-06-17 23:40:08
    expired at:    1970-01-01 03:01:40
    kind:          androidpublisher#subscriptionPurchase
    auto-renewing: disabled', (string) $Resource2);

        $Resource3 = SubscriptionsResource::initializeByString('{
 "kind": "androidpublisher#subscriptionPurchase",
 "startTimeMillis": "1434573608845",
 "expiryTimeMillis": "4434573608845",
 "autoRenewing": true
}');
        $this->assertSame(4434573608.845, $Resource3->getExpiryTime());
        $this->assertSame(4434573608845, $Resource3->getExpiryTimeMillis());
        $this->assertEquals('androidpublisher#subscriptionPurchase', $Resource3->getKind());
        $this->assertSame(1434573608845, $Resource3->getStartTimeMillis());
        $this->assertTrue($Resource3->isAutoRenewing());
        $this->assertFalse($Resource3->isExpired());
        $this->assertEquals('    started at:    2015-06-17 23:40:08
    expired at:    2110-07-12 05:00:08
    kind:          androidpublisher#subscriptionPurchase
    auto-renewing: enabled', (string) $Resource3);
    }
}
