<?php

namespace Abc\Bundle\SupervisorCommandBundle\Command;

use Supervisor\Process;
use Supervisor\Supervisor;
use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * SupervisorListCommand.
 *
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class SupervisorListCommand extends SupervisorCommand
{

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        parent::configure();

        $this->setName('abc:supervisor:list');
        $this->setDescription('Lists the supervisor instances');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Supervisor configuration:</comment>');

        $output->writeln('');

        $instances = $this->findInstances($input, $output);

        if(count($instances) > 0)
        {
            $output->writeln('');
        }

        foreach($instances as $supervisor)
        {
            /** @var Supervisor $supervisor */
            $output->writeln('<comment>Instance: '.$supervisor->getName() .'</comment>');

            $statusArray = $supervisor->getState();
            $status = isset($statusArray['statename']) ? $statusArray['statename'] : 'unknown';

            $output->writeln('  <info>Status: ' . $status .'</info>');

            $processes = $this->findProcesses($supervisor, $input, $output);

            if(count($processes) == 0)
            {
                $output->writeln('  <info>Processes: no processes found</info>');
            }
            else
            {
                $output->writeln('  <info>Processes:</info>');
                foreach($processes as $process)
                {
                    /** @var Process $process */

                    $output->writeln('    <info>'.$process->getName() . '</info>');

                    foreach($process->getProcessInfo() as $name => $value)
                    {
                        $output->writeln('      <info>'. $name .':' .$value . '</info>');
                    }
                }
            }
        }
    }
} 