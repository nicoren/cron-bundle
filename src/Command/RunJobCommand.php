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
use Nicoren\CronBundle\Doctrine\JobManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RunJobCommand extends Command
{

    const OPTION_ID = "id";


    /**
     *
     * @var RunnerInterface
     */
    protected $crontabRunner;

    /**
     *
     * @var JobManagerInterface
     */
    protected $jobManager;



    public function __construct(
        RunnerInterface $crontabRunner,
        JobManagerInterface $jobManager

    ) {
        parent::__construct();
        $this->crontabRunner = $crontabRunner;
        $this->jobManager = $jobManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cron:job:run')
            ->setDescription('Run a cron job')
            ->addArgument(static::OPTION_ID, null, InputArgument::REQUIRED, 'The job id');;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        try {
            $job = $this->jobManager->findOneBy(["id" => $input->getArgument(static::OPTION_ID)]);
            $this->crontabRunner->run($job);

            while ($this->crontabRunner->isRunning()) {
                /**
                 * Wait all tasks are finished
                 */
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
            echo ($e->getTraceAsString());
            $output->writeln("<error>{$e->getMessage()}</error>");
        }
        return Command::FAILURE;
    }
}
