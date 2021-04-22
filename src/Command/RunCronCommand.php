<?php
/*
 * Created on Mon Apr 12 2021
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2021 Tangkoko
 */

/**
 * This file is part of the SymfonyCronBundle package.
 *
 * (c) Dries De Peuter <dries@nousefreak.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nicoren\CronBundle\Command;

use Exception;
use Nicoren\CronBundle\Crontab\RunnerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Dries De Peuter <dries@nousefreak.be>
 */
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
    protected function configure()
    {
        $this->setName('cron:run');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->crontabRunner->run();
            $output->writeln("<info>Jobs Executed successfully.</info>");
            return Command::SUCCESS;
        } catch (Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
        }
        return Command::FAILURE;
    }
}
