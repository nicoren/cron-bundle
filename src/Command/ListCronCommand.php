<?php
/*
 * Created on Mon Apr 12 2021
 * @author : Nicolas RENAULT <nicoren44@gmail.com>
 * @copyright (c) 2021
 */

namespace Nicoren\CronBundle\Command;

use Exception;
use Nicoren\CronBundle\Doctrine\JobManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class ListCronCommand extends Command
{



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
        $this->setName('cron:job:list')
            ->setDescription('List cron jobs');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        try {
            $jobs = $this->jobManager->find();
            $io->title("Jobs List :");
            $cells = [];
            foreach ($jobs as  $job) {
                $cells[] = [$job->getId(), $job->getName(), $job->getDescription(), $job->getCommand(), $job->getSchedule(), $job->getEnabled() ? "Enabled" : "Disabled"];
            }
            $io->table(['Id', 'Name', 'Description', "Command", "Schedule", "Status"], $cells);
            return Command::SUCCESS;
        } catch (Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
        }
        return Command::FAILURE;
    }
}
