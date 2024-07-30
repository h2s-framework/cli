<?php

namespace Siarko\Cli\App;

use Siarko\Cli\Api\App\ErrorHandlerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ErrorHandler implements ErrorHandlerInterface
{

    /**
     * @param OutputInterface $output
     */
    public function __construct(
        private readonly OutputInterface $output
    )
    {
    }

    /**
     * @param \Throwable $exception
     * @return void
     */
    public function handle(\Throwable $exception): void
    {
        switch ($exception->getCode()) {
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                $this->output->writeln("<comment>Deprecated: " . $exception->getMessage() . "</comment>");
                break;
            case E_WARNING:
            case E_USER_WARNING:
                $this->output->writeln("<info>Warning: " . $exception->getMessage() . "</info>");
                break;
            case E_ERROR:
            case E_USER_ERROR:
                $this->output->writeln("<error>Error: " . $exception->getMessage() . "</error>");
                break;
            default:
                $this->output->writeln("<info>Error: " . $exception->getMessage() . "</info>");
        }
    }

}