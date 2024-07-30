<?php

namespace Siarko\Cli\Api\App;

interface ErrorHandlerInterface
{

    /**
     * @param \Throwable $exception
     * @return void
     */
    public function handle(\Throwable $exception): void;
}