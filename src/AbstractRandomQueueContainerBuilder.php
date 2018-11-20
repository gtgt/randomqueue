<?php

namespace RandomQueue;

use RandomQueue\DependencyInjection\Compiler\LoggerAwarePass;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\ResolveEnvPlaceholdersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Dumper\XmlDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

abstract class AbstractRandomQueueContainerBuilder extends ContainerBuilder {

    private static $container;

    /**
     * AbstractRandomQueueContainerBuilder constructor.
     *
     * @throws \Exception
     */
    public function __construct() {
        parent::__construct(NULL);
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__.'/../.env');

        $loader = new YamlFileLoader($this, new FileLocator(__DIR__.'/../config'));
        $loader->load('parameters.yml');
        try {
            $loader->load('parameters_'.getenv('APP_ENV').'.yml');
        } catch (FileLocatorFileNotFoundException $e) {
            // no problem with this
        }
        $loader->load('services.yml');

        $this->addCompilerPass(new LoggerAwarePass());
        $this->addCompilerPass(new RegisterListenersPass());
        // should be the last one
        $this->addCompilerPass(new ResolveEnvPlaceholdersPass());
    }

    /**
     * @param bool $isDebug
     *
     * @return \ProjectServiceContainer|$this
     *
     * @throws \Exception
     */
    final public static function getContainer($isDebug = FALSE) {
        if (!self::$container) {
            $cacheFileBase = __DIR__ .'/../cache/'.basename(str_replace('\\', '/', static::class));
            if (!$isDebug && file_exists($cacheFileBase.'.php')) {
                require_once $cacheFileBase.'.php';
                self::$container = new \ProjectServiceContainer();
            } else {
                self::$container = new static();
                self::$container->compile();

                $phpDumper = new PhpDumper(self::$container);
                file_put_contents($cacheFileBase.'.php', $phpDumper->dump());

                // for ide
                $xmlDumper = new XmlDumper(self::$container);
                file_put_contents($cacheFileBase.'.xml', $xmlDumper->dump());
            }
        }
        // @TODO: maybe clear env here...
        return self::$container;
    }
}
