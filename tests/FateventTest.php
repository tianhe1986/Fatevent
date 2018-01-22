<?php

namespace Fatevent\Tests;

use PHPUnit\Framework\TestCase;
use Fatevent\Fatevent;

class FatevnetTest extends TestCase
{
    public function setUp()
    {
        Fatevent::offAll();
    }

    public function testAddEvent()
    {
        Fatevent::on('test1', function($data, $defaultData){}, 'ok');
        Fatevent::on('test1', function($data, $defaultData){}, 'ok2');
        Fatevent::on('test2', function($data, $defaultData){}, 'ok3');

        $events = Fatevent::getEvents();
        $this->assertEquals(2, count($events));

        $this->assertEquals(2, count($events['test1']));
        $this->assertEquals('ok', $events['test1'][0][1]);
        $this->assertEquals('ok2', $events['test1'][1][1]);

        $this->assertEquals(1, count($events['test2']));
        $this->assertEquals('ok3', $events['test2'][0][1]);
    }

    public function handleEvent($data, $defaultData)
    {
        
    }

    public function testRemoveEvent()
    {
        Fatevent::on('test', [$this, 'handlerEvent'], 'ok');
        Fatevent::on('test', function($data, $defaultData){}, 'ok2');
        Fatevent::on('test', function($data, $defaultData){}, 'ok3');

        $events = Fatevent::getEvents();
        $this->assertEquals(3, count($events['test']));
        $this->assertEquals('ok', $events['test'][0][1]);
        $this->assertEquals('ok2', $events['test'][1][1]);
        $this->assertEquals('ok3', $events['test'][2][1]);

        Fatevent::off('test', [$this, 'handlerEvent']);

        $events = Fatevent::getEvents();
        $this->assertEquals(2, count($events['test']));
        $this->assertEquals('ok2', $events['test'][0][1]);
        $this->assertEquals('ok3', $events['test'][1][1]);
    }

    public function testRemoveAllEvent()
    {
        Fatevent::on('test', [$this, 'handlerEvent'], 'ok');
        Fatevent::on('test', function($data, $defaultData){}, 'ok2');
        Fatevent::on('test', function($data, $defaultData){}, 'ok3');

        Fatevent::off('test');

        $events = Fatevent::getEvents();
        $this->assertEmpty($events);
    }

    public function testTriggerEvent()
    {
        $count = 0;
        Fatevent::on('test', function($data, $defaultData) use (&$count) {$count += 1; return true;});
        Fatevent::on('test', function($data, $defaultData) use (&$count) {$count += 2; return true;});
        Fatevent::on('test', function($data, $defaultData) use (&$count) {$count += 4; return true;});

        Fatevent::trigger('test');

        $this->assertEquals(7, $count);
    }

    
    public function testTriggerOnceEvent()
    {
        $count = 0;
        Fatevent::once('test', function($data, $defaultData) use (&$count) {$count += 1; return true;}, 'ok');
        Fatevent::on('test', function($data, $defaultData) use (&$count) {$count += 2; return true;}, 'ok2');
        Fatevent::once('test', function($data, $defaultData) use (&$count) {$count += 4; return true;}, 'ok3');

        $events = Fatevent::getEvents();
        $this->assertEquals(3, count($events['test']));
        $this->assertEquals('ok', $events['test'][0][1]);
        $this->assertEquals('ok2', $events['test'][1][1]);
        $this->assertEquals('ok3', $events['test'][2][1]);

        Fatevent::trigger('test');

        $this->assertEquals(7, $count);

        $events = Fatevent::getEvents();
        $this->assertEquals(1, count($events['test']));
        $this->assertEquals('ok2', $events['test'][0][1]);
    }

    public function testTriggerEventBreak()
    {
        $count = 0;
        Fatevent::on('test', function($data, $defaultData) use (&$count) {$count += 1; return true;});
        Fatevent::on('test', function($data, $defaultData) use (&$count) {$count += 2; return false;});
        Fatevent::on('test', function($data, $defaultData) use (&$count) {$count += 4; return true;});

        Fatevent::trigger('test');

        $this->assertEquals(3, $count);
    }
}