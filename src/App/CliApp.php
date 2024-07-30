<?php

namespace Siarko\Cli\App;

use Siarko\Bootstrap\Api\AppInterface;
use Siarko\Bootstrap\Exception\ApplicationStartupException;
use Siarko\Cli\Api\App\ErrorHandlerInterface;
use Siarko\Cli\CommandRegistry;
use Siarko\DependencyManager\Exceptions\CouldNotResolveNamespace;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class CliApp implements AppInterface
{

    /**
     * @param Application $consoleApplication
     * @param CommandRegistry $commandRegistry
     * @param ArgvInput $input
     * @param ConsoleOutput $output
     * @param ErrorHandlerInterface $errorHandler
     */
    public function __construct(
        private readonly Application $consoleApplication,
        private readonly CommandRegistry $commandRegistry,
        private readonly ArgvInput $input,
        private readonly ConsoleOutput $output,
        private readonly ErrorHandlerInterface $errorHandler
    )
    {
    }

    /**
     * Start application
     *
     * @return void
     * @throws CouldNotResolveNamespace
     */
    public function start(): void
    {
        $this->commandRegistry->register($this->consoleApplication);
        $this->consoleApplication->run($this->input, $this->output);
    }

    /**
     * Run sanity checks to ensure that application is properly configured
     *
     * @return void
     */
    public function runSanityChecks(): void
    {
        if(php_sapi_name() !== 'cli'){
            throw new ApplicationStartupException("Incorrect Application Run Mode - can run only with CLI");
        }
    }

    /**
     * Handle errors
     *
     * @param \Throwable $exception
     * @return void
     */
    public function handleErrors(\Throwable $exception): void
    {
        $this->errorHandler->handle($exception);
    }
}