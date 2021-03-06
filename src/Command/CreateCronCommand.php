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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateCronCommand extends Command
{

    const OPTION_NAME = "name";
    const OPTION_DESCRIPTION = "description";
    const OPTION_COMMAND = "command";
    const OPTION_SCHEDULE = "schedule";
    const OPTION_ENABLED = "enabled";
    const OPTION_MAX_CONCURRENT = "max_concurrent";


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
        $this->setName('cron:job:create')
            ->setDescription('Create a cron job')
            ->addOption(static::OPTION_NAME, null, InputOption::VALUE_REQUIRED, 'The job name')
            ->addOption(static::OPTION_COMMAND, null, InputOption::VALUE_REQUIRED, 'The job command')
            ->addOption(static::OPTION_SCHEDULE, null, InputOption::VALUE_REQUIRED, 'The job schedule')
            ->addOption(static::OPTION_DESCRIPTION, null, InputOption::VALUE_REQUIRED, 'The job description')
            ->addOption(static::OPTION_ENABLED, null, InputOption::VALUE_REQUIRED, 'Is job enabled')
            ->addOption(static::OPTION_MAX_CONCURRENT, null, InputOption::VALUE_REQUIRED, 'The maximum number of this job concurrent executions');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $job = $this->jobManager->createEmpty();
            $job->setName($input->getOption(static::OPTION_NAME))
                ->setDescription($input->getOption(static::OPTION_DESCRIPTION))
                ->setCommand($input->getOption(static::OPTION_COMMAND))
                ->setEnabled($input->getOption(static::OPTION_ENABLED))
                ->setSchedule($input->getOption(static::OPTION_SCHEDULE))
                ->setMaxConcurrent($input->getOption(static::OPTION_MAX_CONCURRENT));
            $this->jobManager->save($job);
            $output->writeln("<info>Job saved.</info>");
            return Command::SUCCESS;
        } catch (Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
        }
        return Command::FAILURE;
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->getDefinition()->getOptions() as $option) {

            if (!$input->getOption($option->getName()) && $option->isValueRequired()) {
                $question = new Question("Please fill a {$option->getName()} :");
                $question->setValidator(function ($value) {
                    if (empty($value)) {
                        throw new \Exception("This field can not be empty");
                    }
                    return $value;
                });
                $answer = $this->getHelper('question')->ask($input, $output, $question);

                $input->setOption($option->getName(), $answer);
            }
        }
    }
}
