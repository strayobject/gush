<?php

/**
 * This file is part of Gush package.
 *
 * (c) 2013-2014 Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gush\Tests\Subscriber;

use Gush\Subscriber\TableSubscriber;

class TableSubscriberTest extends \PHPUnit_Framework_TestCase
{
    protected $subscriber;
    protected $commandEvent;
    protected $consoleEvent;
    protected $command;
    protected $tableHelper;
    protected $input;

    public function setUp()
    {
        $this->commandEvent = $this->getMockBuilder(
            'Gush\Event\CommandEvent'
        )->disableOriginalConstructor()->getMock();

        $this->consoleEvent = $this->getMockBuilder(
            'Symfony\Component\Console\Event\ConsoleEvent'
        )->disableOriginalConstructor()->getMock();

        $this->command = $this->getMockBuilder(
            'Gush\Tests\Subscriber\TestTableCommand'
        )->disableOriginalConstructor()->getMock();

        $this->tableHelper = $this->getMock(
            'Gush\Helper\TableHelper'
        );

        $this->input = $this->getMock('Symfony\Component\Console\Input\InputInterface');

        $this->subscriber = new TableSubscriber();
    }

    /**
     * @test
     */
    public function decorates_a_definition()
    {
        $this->commandEvent->expects($this->once())
            ->method('getCommand')
            ->will($this->returnValue($this->command))
        ;
        $this->command->expects($this->at(0))
            ->method('addOption')
            ->will($this->returnValue($this->command))
        ;
        $this->command->expects($this->at(1))
            ->method('addOption')
            ->will($this->returnValue($this->command))
        ;
        $this->command->expects($this->at(2))
            ->method('addOption')
            ->will($this->returnValue($this->command))
        ;

        $this->subscriber->decorateDefinition($this->commandEvent);
    }

    public function provideInitialize()
    {
        return [
            ['default', true],
            ['borderless', true],
            ['compact', true],

            ['foobar', false],
        ];
    }

    /**
     * @test
     * @dataProvider provideInitialize
     */
    public function initializes($layoutName, $valid)
    {
        $this->consoleEvent->expects($this->once())
            ->method('getCommand')
            ->will($this->returnValue($this->command))
        ;
        $this->consoleEvent->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($this->input))
        ;
        $this->input->expects($this->once())
            ->method('getOption')
            ->with('table-layout')
            ->will($this->returnValue($layoutName))
        ;

        if (false === $valid) {
            $this->setExpectedException('InvalidArgumentException', 'must be passed one of');
        }

        $this->subscriber->initialize($this->consoleEvent);
    }
}
