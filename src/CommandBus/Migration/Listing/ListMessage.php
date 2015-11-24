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

namespace Baleen\Cli\CommandBus\Migration\Listing;

use Baleen\Cli\CommandBus\AbstractMessage;
use Baleen\Cli\CommandBus\Migration\AbstractMigrationMessage;
use Baleen\Cli\Config\ConfigInterface;
use Baleen\Cli\Repository\MigrationMapperService;
use Baleen\Cli\Repository\MigrationMapperServiceFactory;
use Baleen\Migrations\Migration\Repository\Mapper\MigrationMapperInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class ListMessage.
 *
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
class ListMessage extends AbstractMessage
{
    /** @var MigrationMapperServiceFactory */
    private $migrationMapper;

    /**
     * ListMessage constructor.
     *
     * @param ConfigInterface $config
     * @param MigrationMapperService $migrationMapper
     */
    public function __construct(ConfigInterface $config, MigrationMapperService $migrationMapper)
    {
        $this->migrationMapper = $migrationMapper;
        parent::__construct($config);
    }

    /**
     * getMigrationRepositoryMapper
     *
     * @return MigrationMapperService
     */
    final public function getMigrationMapper()
    {
        return $this->migrationMapper;
    }

    /**
     * configure.
     *
     * @param Command $command
     */
    public static function configure(Command $command)
    {
        $command->setName('migrations:list')
            ->setAliases(['all'])
            ->setDescription('Prints version IDs for all available migrations ordered incrementally.')
            ->addOption(
                'newest-first',
                null,
                InputOption::VALUE_NONE,
                'Sort list in reverse order (newest first)'
            );
    }
}