<?php

namespace Siarko\Cli;

use Siarko\DependencyManager\ClassNameResolver;
use Siarko\DependencyManager\DependencyManager;
use Siarko\DependencyManager\Exceptions\CouldNotResolveNamespace;
use Siarko\Paths\Provider\AbstractPathProvider;
use Siarko\Files\Path\PathInfo;
use Siarko\Paths\Provider\Pool\PathProviderPool;
use Symfony\Component\Console\Application;

class CommandRegistry
{

    public const PATH_PROVIDER_POOL_TYPE = 'command';

    /**
     * @param PathProviderPool $pathProviderPool
     * @param PathInfo $pathInfo
     * @param ClassNameResolver $classNameResolver
     * @param DependencyManager $dependencyManager
     */
    public function __construct(
        protected readonly PathProviderPool  $pathProviderPool,
        protected readonly PathInfo          $pathInfo,
        protected readonly ClassNameResolver $classNameResolver,
        protected readonly DependencyManager $dependencyManager
    )
    {
    }

    /**
     * @return array
     * @throws CouldNotResolveNamespace
     */
    protected function getFileList(): array
    {
        $result = [];
        foreach ($this->pathProviderPool->getProviders(self::PATH_PROVIDER_POOL_TYPE) as $commandPathProvider) {
            $pathInfo = $this->pathInfo->read($commandPathProvider->getConstructedPath());
            $files = $pathInfo->readDirectoryFiles('/^.*\.php$/');
            $result = array_merge(array_map(function ($filePath) {
                return $this->classNameResolver->resolveFromFilePath($filePath);
            }, $files), $result);
        }
        return $result;
    }

    /**
     * @param Application $application
     * @return void
     * @throws CouldNotResolveNamespace
     */
    public function register(Application $application)
    {
        foreach ($this->getFileList() as $commandClassType) {
            $application->add($this->dependencyManager->get($commandClassType));
        }
    }

}