<?php

/**
 * Created on Mon Apr 12 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @copyright (c) 2021
 */


namespace Nicoren\CronBundle\Command;

use Exception;
use Nicoren\CronBundle\Doctrine\JobManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DisableCronCommand extends Command
{

    const OPTION_ID = "id";


    /**
     *
     * @var JobManagerInterface
     */
    protected $jobManager;


    public function __construct(
        JobManagerInterface $jobManager

    ) {
        parent::__construct();
        $this->jobManager = $jobManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cron:job:disable')
            ->setDescription('Disable a cron job')
            ->addArgument(static::OPTION_ID, null, InputArgument::REQUIRED, 'The job id');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $job = $this->jobManager->findOneBy(["id" => $input->getArgument(static::OPTION_ID)]);
            if ($job) {
                $job->setEnabled(false);
                $this->jobManager->save($job);
                $output->writeln("<info>Job disabled.</info>");
            } else {
                $message = sprintf("Job with id %s don't exist", $input->getArgument(static::OPTION_ID));
                $output->writeln("<error>$message</error>");
            }
            return Command::SUCCESS;
        } catch (Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
        }
        return Command::FAILURE;
    }
}
