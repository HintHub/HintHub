<?php

namespace App\Command;

use App\Service\FehlerService;
use App\Repository\CommentRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FehlerEscalateCommand extends Command
{
    private $fehlerService;

    protected static $defaultName = 'app:escalate';

    public function __construct(FehlerService $fehlerService)
    {
        $this->fehlerService = $fehlerService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Escalates Fehler instances after 4 days')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this -> fehlerService -> escalateFehler();
        return 1;
    }
}