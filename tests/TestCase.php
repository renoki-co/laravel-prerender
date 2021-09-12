<?php

namespace RenokiCo\LaravelPrerender\Test;

use Orchestra\Testbench\TestCase as Orchestra;
use RenokiCo\Clusteer\ClusteerServer;
use Symfony\Component\Process\Process;

abstract class TestCase extends Orchestra
{
    /**
     * The Clusteer server process.
     *
     * @var \Symfony\Component\Process\Process
     */
    protected $server;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $command = ClusteerServer::create(3000)
            ->nodeJsPath('$(which node)')
            ->jsFilePath(__DIR__.'/../vendor/renoki-co/clusteer/server.js')
            ->configureServer()
            ->buildCommand();

        $this->server = Process::fromShellCommandline($command)
            ->setTimeout(600);

        $this->server->start();

        sleep(2);

        dump($this->server->getOutput());
        dump($this->server->getErrorOutput());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $this->server->stop();

        sleep(2);
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app)
    {
        return [
            \Jenssegers\Agent\AgentServiceProvider::class,
            \RenokiCo\Clusteer\ClusteerServiceProvider::class,
            \RenokiCo\LaravelPrerender\LaravelPrerenderServiceProvider::class,
            TestServiceProvider::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'wslxrEFGWY6GfGhvN9L3wH3KSRJQQpBD');
    }
}
