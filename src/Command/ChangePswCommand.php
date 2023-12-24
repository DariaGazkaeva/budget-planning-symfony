<?php

namespace App\Command;

use App\Service\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:change-psw',
    description: 'Changes user password',
)]
class ChangePswCommand extends Command
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        parent::__construct();
        $this->userService = $userService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('password', InputArgument::REQUIRED, 'New password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $psw = $input->getArgument('password');

        if ($email && $psw) {
            $res = $this->userService->changePassword($email, $psw);
            if ($res == null) {
                $io->error(sprintf('User with email %s does not exist', $email));
            } else {
                $io->success(sprintf('The password has been changed for the user with email %s', $email));
            }
        }
        return Command::SUCCESS;
    }
}
