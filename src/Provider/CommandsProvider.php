<?php

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <https://github.com/baleen/migrations>.
 */

namespace Baleen\Cli\Provider;

use Baleen\Cli\BaseCommand;
use Baleen\Cli\CommandBus\CliBus;
use Baleen\Cli\CommandBus\Config\Init\InitHandler;
use Baleen\Cli\CommandBus\Config\Init\InitMessage;
use Baleen\Cli\CommandBus\Migration\Status\StatusHandler;
use Baleen\Cli\CommandBus\Migration\Status\StatusMessage;
use Baleen\Cli\CommandBus\MappableContainerLocator;
use Baleen\Cli\CommandBus\Migration\Create\CreateHandler;
use Baleen\Cli\CommandBus\Migration\Create\CreateMessage;
use Baleen\Cli\CommandBus\Migration\Latest\LatestHandler as RepositoryLatestHandler;
use Baleen\Cli\CommandBus\Migration\Latest\LatestMessage as RepositoryLatestCommand;
use Baleen\Cli\CommandBus\Migration\Listing\ListHandler;
use Baleen\Cli\CommandBus\Migration\Listing\ListMessage;
use Baleen\Cli\CommandBus\Run\Migrate\MigrateMessage;
use Baleen\Cli\CommandBus\Storage\Latest\LatestHandler as StorageLatestHandler;
use Baleen\Cli\CommandBus\Storage\Latest\LatestMessage as StorageLatestCommand;
use Baleen\Cli\CommandBus\Run\Execute\ExecuteHandler;
use Baleen\Cli\CommandBus\Run\Execute\ExecuteMessage;
use Baleen\Cli\CommandBus\Run\Migrate\MigrateHandler;
use Baleen\Cli\Exception\CliException;
use League\Container\ServiceProvider;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use League\Tactician\Plugins\LockingMiddleware;

/**
 * Class CommandsProvider.
 *
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
class CommandsProvider extends AbstractServiceProvider
{
    protected $provides = [
        Services::COMMANDS,
        Services::COMMAND_BUS,
    ];

    /** @var array */
    protected $commands = [
        Services::CMD_CONFIG_INIT => [
            'message' => InitMessage::class,
            'handler' => InitHandler::class,
        ],
        Services::CMD_CONFIG_STATUS => [
            'message' => StatusMessage::class,
            'handler' => StatusHandler::class,
        ],
        Services::CMD_REPOSITORY_CREATE => [
            'message' => CreateMessage::class,
            'handler' => CreateHandler::class,
        ],
        Services::CMD_REPOSITORY_LATEST => [
            'message' => RepositoryLatestCommand::class,
            'handler' => RepositoryLatestHandler::class,
        ],
        Services::CMD_MIGRATIONS_LIST => [
            'message' => ListMessage::class,
            'handler' => ListHandler::class,
        ],
        Services::CMD_STORAGE_LATEST => [
            'message' => StorageLatestCommand::class,
            'handler' => StorageLatestHandler::class,
        ],
        Services::CMD_RUN_EXECUTE => [
            'message' => ExecuteMessage::class,
            'handler' => ExecuteHandler::class,
        ],
        Services::CMD_RUN_MIGRATE => [
            'message' => MigrateMessage::class,
            'handler' => MigrateHandler::class,
        ],
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     */
    public function register()
    {
        $container = $this->getContainer();

        $commands = $this->commands;

        // setup the command domainBus to know which handler to use for each message class
        $container->share(
            Services::COMMAND_BUS,
            function () use ($commands) {
                $map = [];
                foreach ($commands as $alias => $config) {
                    $message = $config['message'];
                    $handler = $config['handler'];
                    $map[$message] = $handler;
                }

                $handlerMiddleware = new CommandHandlerMiddleware(
                    new ClassNameExtractor(),
                    new MappableContainerLocator($this->getContainer(), $map),
                    new HandleInflector()
                );
                $lockingMiddleware = new LockingMiddleware();

                return new CliBus([$lockingMiddleware, $handlerMiddleware]);
            }
        );

        // create a service (that's just an array) that has a list of all the commands for the app
        $container->add(
            Services::COMMANDS,
            function (CliBus $bus) use ($commands) {
                $commandList = [];
                foreach ($commands as $config) {
                    $serviceClass = $config['message'];
                    if (!$this->getContainer()->has($serviceClass)) {
                        throw new CliException(sprintf(
                            'The container cannot provide class "%s". Make sure the class exists and can be retrieved by ' .
                            'the container.',
                            $serviceClass
                        ));
                    }
                    $commandList[] = new BaseCommand($bus, $serviceClass);
                }

                return $commandList;
            }
        )->withArguments([Services::COMMAND_BUS]);
    }
}
