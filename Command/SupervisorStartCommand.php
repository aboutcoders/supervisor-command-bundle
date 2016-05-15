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
class SupervisorStartCommand extends SupervisorCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        parent::configure();

        $this->setName('abc:supervisor:start');
        $this->setDescription('Starts the supervisor processes');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>' . 'Starting processes' . '</comment>');

        $instances = $this->findInstances($input, $output);

        if($instances > 0)
        {
            $output->writeln('');

            foreach($instances as $supervisor)
            {
                /** @var Supervisor $supervisor */
                $output->writeln('<comment>Instance: '.$supervisor->getName() .'</comment>');

                foreach($this->findProcesses($supervisor, $input, $output) as $process)
                {
                    $this->startProcess($process, $output);
                }
            }
        }
    }

    /**
     * @param Process         $process
     * @param OutputInterface $output
     */
    protected function startProcess(Process $process, OutputInterface $output)
    {
        /** @var Process $process */
        $status = $this->getStatus($process);
        if(!in_array($status, array('STARTING', 'RUNNING')))
        {
            $output->write('  <info>' . 'Starting ' . $process->getName() . ' ... </info>');
            $process->startProcess(true);
            $output->write('<info>succeeded</info>');
        }
        else
        {
            $output->write('  <info>' . sprintf('Skipped %s, process is %s', $process->getName(), $status) . '</info>');
        }

        $output->writeln('');
    }
} 