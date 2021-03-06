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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class SupervisorStopCommand extends SupervisorCommand
{

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        parent::configure();

        $this
            ->setName('abc:supervisor:stop')
            ->setDescription('Stops the supervisor processes');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>' . 'Stopping processes' . '</comment>');

        $instances = $this->findInstances($input, $output);

        if($instances > 0)
        {
            $output->writeln('');

            foreach($instances as $supervisor)
            {
                /** @var Supervisor $supervisor */
                $output->writeln('<comment>' . $supervisor->getName() .'</comment>');

                foreach($this->findProcesses($supervisor, $input, $output) as $process)
                {
                    $this->stopProcess($process, $output);
                }
            }
        }
    }

    /**
     * @param \Supervisor\Process $process
     * @param OutputInterface     $output
     */
    protected function stopProcess(Process $process, OutputInterface $output)
    {
        /** @var Process $process */
        $status = $this->getStatus($process);
        if(!in_array($status, array('NOT_RUNNING', 'STOPPED', 'EXITED', 'FATAL')))
        {
            $output->write('  <info>' . 'Stopping ' . $process->getName() . ' ... </info>');
            $process->stopProcess();
            $output->write('<info>succeeded</info>');
        }
        else
        {
            $output->write('  <info>' . sprintf('Skipped %s, process is %s', $process->getName(), $status) . '</info>');
        }

        $output->writeln('');
    }
} 