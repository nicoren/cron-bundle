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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
class DeleteCronCommand extends Command
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
        $this->setName('cron:job:delete')
            ->setDescription('Delete a cron job')
            ->addArgument(static::OPTION_ID, InputArgument::REQUIRED, 'The job id');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $job = $this->jobManager->findOneBy(["id" => $input->getArgument(static::OPTION_ID)]);
            if ($job) {
                $this->jobManager->delete($job);
                $output->writeln("<info>Job deleted.</info>");
            } else {
                $message = sprintf("Job with id '%s' don't exist", $input->getArgument(static::OPTION_ID));
                $output->writeln("<error>$message</error>");
            }
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
        foreach ($this->getDefinition()->getArguments() as $argument) {
            if (!$input->getArgument($argument->getName()) && $argument->isRequired()) {
                $question = new Question("Please fill a(n) {$argument->getName()} :");
                $question->setValidator(function ($value) {
                    if (empty($value)) {
                        throw new \Exception("This field can not be empty");
                    }
                    return $value;
                });
                $answer = $this->getHelper('question')->ask($input, $output, $question);

                $input->setArgument($argument->getName(), $answer);
            }
        }
    }
}
