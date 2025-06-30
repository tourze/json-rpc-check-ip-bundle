<?php

namespace Tourze\JsonRPCCheckIPBundle\Tests\Integration\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Tourze\JsonRPCCheckIPBundle\EventSubscriber\CheckIpSubscriber;

class CheckIpSubscriberTest extends TestCase
{
    public function testSubscriberIsRegistered(): void
    {
        $subscriber = new CheckIpSubscriber(new \Symfony\Component\HttpFoundation\RequestStack());
        
        $this->assertInstanceOf(CheckIpSubscriber::class, $subscriber);
        
        $reflection = new \ReflectionClass($subscriber);
        $method = $reflection->getMethod('beforeMethodApply');
        $attributes = $method->getAttributes(\Symfony\Component\EventDispatcher\Attribute\AsEventListener::class);
        
        $this->assertNotEmpty($attributes, 'beforeMethodApply method should have AsEventListener attribute');
    }
}