<?php


namespace RandomQueue;


use RandomQueue\DependencyInjection\Compiler\LoggerAwarePass;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;
use Symfony\Component\DependencyInjection\Compiler\ResolveEnvPlaceholdersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

class RandomQueueContainer extends ContainerBuilder {
    /**
     * RandomQueueContainer constructor.
     *
     * @param ParameterBagInterface|NULL $parameterBag
     *
     * @throws \Exception
     */
    public function __construct(ParameterBagInterface $parameterBag = NULL) {
        parent::__construct($parameterBag);
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
        $this->addCompilerPass(new AddConsoleCommandPass());
        $this->addCompilerPass(new ResolveEnvPlaceholdersPass());
        $this->addCompilerPass(new RegisterListenersPass());
        $this->compile(TRUE);
        // @TODO: maybe clear env here...
    }
}
