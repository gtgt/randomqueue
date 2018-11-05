<?php


namespace RandomQueue;


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
        $loader->load('services.yml');
        $this->addCompilerPass(new AddConsoleCommandPass());
        $this->addCompilerPass(new ResolveEnvPlaceholdersPass());
        $this->addCompilerPass(new RegisterListenersPass());
    }
}