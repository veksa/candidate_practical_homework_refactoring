<?php
namespace Language\Tests\Cache;

use DateTime;
use DateInterval;
use Language\Cache\CacheItem;

class CacheItemTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateNullValue()
    {
        $item = new CacheItem('my_key');
        $this->assertNull($item->get());
    }

    public function testNewItemIsCacheMiss()
    {
        $item = new CacheItem('my_key');
        $this->assertFalse($item->isHit());
    }

    public function testSetString()
    {
        $item = new CacheItem('my_key');
        $item->set('value');
        $this->assertSame('value', $item->get());
    }

    public function testSetInt()
    {
        $item = new CacheItem('my_key');
        $item->set(1);
        $this->assertSame(1, $item->get());
    }

    public function testSetArray()
    {
        $item = new CacheItem('my_key');
        $item->set([1 => 'hello', 'world' => 2]);
        $this->assertSame([1 => 'hello', 'world' => 2], $item->get());
    }

    public function testSetObject()
    {
        $item = new CacheItem('my_key');
        $item->set(new \stdClass());
        $this->assertEquals(new \stdClass(), $item->get());
    }

    public function testSetOverwriteValue()
    {
        $item = new CacheItem('my_key');
        $item->set(new \stdClass());
        $item->set('overwrite');
        $this->assertSame('overwrite', $item->get());
    }

    public function testConstructorExpiresAfterSeconds()
    {
        $item = new CacheItem('my_key');
        $item->set(new \stdClass());
        $item->expiresAfter(10);
        $this->assertTrue($item->isHit());
    }

    public function testExpiresAfterDateInterval()
    {
        $item = new CacheItem('my_key');
        $item->set(new \stdClass());
        $oneDay = DateInterval::createFromDateString('1 day');
        $item->expiresAfter($oneDay);
        $this->assertTrue($item->isHit());
    }

    public function testExpiresAtTomorrowIsHit()
    {
        $item = new CacheItem('my_key');
        $item->set(new \stdClass());
        $tomorrow = (new DateTime('now'))->add(DateInterval::createFromDateString('1 day'));
        $item->expiresAt($tomorrow);
        $this->assertTrue($item->isHit());
    }

    public function testExpiresAtYesterdayIsNotHit()
    {
        $item = new CacheItem('my_key');
        $item->set(new \stdClass());
        $yesterday = (new DateTime('now'))->add(DateInterval::createFromDateString('-1 day'));
        $item->expiresAt($yesterday);
        $this->assertFalse($item->isHit());
    }

    public function testExpiresAfterIsHit()
    {
        $item = new CacheItem('my_key');
        $item->set(new \stdClass());
        $item->expiresAfter(60);
        $this->assertTrue($item->isHit());
    }

    public function testExpiresAfter0IsNotHit()
    {
        $item = new CacheItem('my_key');
        $item->set(new \stdClass());
        $item->expiresAfter(0);
        $this->assertFalse($item->isHit());
    }

    public function testExpiresAfterDefaultIsHit()
    {
        $item = new CacheItem('my_key');
        $item->set(new \stdClass());
        $item->expiresAfter(null);
        $this->assertTrue($item->isHit());
    }
}