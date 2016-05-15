<?php


namespace Abc\Bundle\SupervisorCommandBundle\Tests\Command;

use Abc\Bundle\SupervisorCommandBundle\Command\SupervisorListCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use YZ\SupervisorBundle\Manager\SupervisorManager;

class SupervisorListCommandTest extends WebTestCase
{
    /** @var  SupervisorManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $supervisorManager;
    /** @var Application */
    private $application;

    /**
     * @before
     */
    public function setupSubject()
    {

        $this->supervisorManager = $this->getMockBuilder('YZ\SupervisorBundle\Manager\SupervisorManager')->disableOriginalConstructor()->getMock();

        self::bootKernel();

        static::$kernel->getContainer()
            ->set('supervisor.manager', $this->supervisorManager);

        $this->application = new Application(static::$kernel);
        $this->application->setAutoExit(false);
        $this->application->setCatchExceptions(false);
    }

    public function testExecute()
    {
        $supervisor = $this->getMockBuilder('Supervisor\Supervisor')->disableOriginalConstructor()->getMock();
        $supervisor->expects($this->any())
            ->method('checkConnection')
            ->willReturn(true);
        $supervisor->expects($this->any())
            ->method('getName')
            ->willReturn('niceNameFoo');

        $this->supervisorManager->expects($this->any())
            ->method('getSupervisors')
            ->willReturn(array($supervisor));

        $this->application->add(new SupervisorListCommand());

        $command       = $this->application->find('abc:supervisor:list');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp('/Instance: niceNameFoo/', $commandTester->getDisplay());
    }

}
 