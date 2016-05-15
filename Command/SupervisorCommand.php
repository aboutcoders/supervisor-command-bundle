<?php
/*
* This file is part of the supervisor-command-bundle package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\Bundle\SupervisorCommandBundle\Command;

use Supervisor\Process;
use Supervisor\Supervisor;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use YZ\SupervisorBundle\Manager\SupervisorManager;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
abstract class SupervisorCommand extends ContainerAwareCommand
{

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->addOption('instance', 'i', InputOption::VALUE_OPTIONAL, 'The instance name');
        $this->addOption('group', 'g', InputOption::VALUE_OPTIONAL, 'The name of the process group');
        $this->addOption('process', 'p', InputOption::VALUE_OPTIONAL, 'The name of the process');
    }

    /**
     * @return SupervisorManager
     */
    protected function getManager()
    {
        return $this->getContainer()->get('supervisor.manager');
    }

    /**
     * @param Process $process
     * @return string|null
     */
    protected function getStatus(Process $process)
    {
        $processInfo = $process->getProcessInfo();

        return isset($processInfo['statename']) ? $processInfo['statename'] : null;
    }


    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @throws \Exception If connection to instance fails
     * @return array|\Supervisor\Supervisor[]
     */
    protected function findInstances(InputInterface $input, OutputInterface $output)
    {
        $instanceName = $input->getOption('instance');
        $instances    = array();

        foreach($this->getManager()->getSupervisors() as $supervisor)
        {
            /** @var Supervisor $supervisor */

            if(!$supervisor->checkConnection())
            {
                throw new \Exception(sprintf('Failed to connect to supervisor instance "%s", check the configuration of the YZSupervisorBundle in app/config.yml', $supervisor->getName()));
            }
            else
            {
                if($instanceName != null && $instanceName == $supervisor->getName())
                {
                    $instances[] = $supervisor;
                }
                elseif($instanceName == null)
                {
                    $instances[] = $supervisor;
                }
            }
        }

        if($instanceName != null && count($instances) == 0)
        {
            $output->writeln('<error>No instance found with the name "' . $instanceName . '"</error>');
        }

        return $instances;
    }


    /**
     * @param Supervisor      $supervisor
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return array|\Supervisor\Process[]
     */
    protected function findProcesses(Supervisor $supervisor, InputInterface $input, OutputInterface $output)
    {
        $processes = $supervisor->getProcesses();
        if($input->getOption('group') != null)
        {
            $processes = $this->filterByGroupName($processes, $input->getOption('group'));
            if(count($processes) == 0)
            {
                $output->writeln('<error>No processes found with the group ' . $input->getOption('group') . '</error>');
            }
        }
        if($input->getOption('process') != null)
        {
            $processes = $this->filterByProcessName($processes, $input->getOption('process'));
            if(count($processes) == 0)
            {
                $output->writeln('<error>No processes found with the name ' . $input->getOption('process') . '</error>');
            }
        }

        return $processes;
    }

    /**
     * @param array  $processes
     * @param string $groupName
     * @return array|\Supervisor\Process[]
     */
    protected function filterByGroupName(array $processes, $groupName)
    {
        $filtered = array();
        foreach($processes as $process)
        {
            /** @var Process $process */
            if($groupName == $process->getGroup())
            {
                $filtered[] = $process;
            }
        }

        return $filtered;
    }

    /**
     * @param array  $processes
     * @param string $processName
     * @return array|\Supervisor\Process[]
     */
    protected function filterByProcessName(array $processes, $processName)
    {
        $filtered = array();
        foreach($processes as $process)
        {
            /** @var Process $process */
            if($processName == $process->getName())
            {
                $filtered[] = $process;
            }
        }

        return $filtered;
    }
}