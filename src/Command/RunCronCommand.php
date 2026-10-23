<?php

/**
 * Created on Mon Apr 12 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @copyright (c) 2021
 */



namespace Nicoren\CronBundle\Command;

use Exception;
use Nicoren\CronBundle\Crontab\JobProcess;
use Nicoren\CronBundle\Crontab\RunnerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RunCronCommand extends Command
{

    const OPTION_NAME = "name";
    const OPTION_DESCRIPTION = "description";
    const OPTION_COMMAND = "command";
    const OPTION_SCHEDULE = "schedule";
    const OPTION_ENABLED = "enabled";
    const OPTION_MAX_CONCURRENT = "max_concurrent";


    /**
     *
     * @var RunnerInterface
     */
    protected $crontabRunner;


    public function __construct(
        RunnerInterface $crontabRunner

    ) {
        parent::__construct();
        $this->crontabRunner = $crontabRunner;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('cron:run');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            $this->crontabRunner->run();

            while ($this->crontabRunner->isRunning()) {
                /**
                 * Wait all tasks are finished
                 */
                usleep(200000);
            }
            $io->title("Jobs result :");
            $cells = [];
            foreach ($this->crontabRunner->getProcesses() as $jobProcess) {
                /**
                 * @var JobProcess $jobProcess
                 */
                $cells[] = [$jobProcess->getPid(), $jobProcess->getJob()->getId(), $jobProcess->getJob()->getName(), $jobProcess->getStatus()];
            }
            $io->table(['Pid', 'Job Id', 'Job name', 'status'], $cells);
            return Command::SUCCESS;
        } catch (Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
        }
        return Command::FAILURE;
    }
}
