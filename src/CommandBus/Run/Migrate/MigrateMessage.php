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
 * <http://www.doctrine-project.org>.
 */

namespace Baleen\Cli\CommandBus\Run\Migrate;

use Baleen\Cli\CommandBus\Run\AbstractRunMessage;
use Baleen\Cli\Exception\CliException;
use Baleen\Migrations\Migration\Options;
use Baleen\Migrations\Migration\Options\Direction;
use Baleen\Migrations\Timeline;
use Baleen\Migrations\Version\VersionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MigrateMessage.
 *
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
class MigrateMessage extends AbstractRunMessage
{
    const ARG_TARGET = 'target';
    const OPT_STRATEGY = 'strategy';
    const OPT_PROGRESS = 'progress';
    const OPT_REPOSITORY = 'repository';

    const STRATEGY_UP = 'up';
    const STRATEGY_DOWN = 'down';
    const STRATEGY_CONVERGE = 'converge';

    /** @var array */
    private $strategies = [
        self::STRATEGY_UP,
        self::STRATEGY_DOWN,
        self::STRATEGY_CONVERGE,
    ];

    /**
     * @inheritdoc
     */
    public static function configure(Command $command)
    {
        parent::configure($command);

        $command
            ->setName('run:migrate')
            ->setAliases(['migrate'])
            ->setDescription('Migrates all versions up (or down) to and including the specified target.')
            ->addArgument(self::ARG_TARGET, InputArgument::OPTIONAL, 'The target version to migrate to.', 'latest')
            ->addOption(
                self::OPT_PROGRESS,
                null,
                InputOption::VALUE_NONE,
                'Show a progress bar.'
            )->addOption(
                self::OPT_STRATEGY,
                's',
                InputOption::VALUE_REQUIRED,
                'Strategy to migrate with (up/down/both).',
                Direction::UP // 'up'
            )->addOption(
                self::OPT_REPOSITORY,
                'r',
                InputOption::VALUE_OPTIONAL,
                'If present, executes action only on the specified repository.'
            );
    }

    /**
     * isDryRun
     * @return bool
     */
    public function isDryRun()
    {
        return (bool) $this->getInput()->getOption(MigrateMessage::OPT_DRY_RUN);
    }

    /**
     * isNoStorage
     * @return bool
     */
    public function isNoStorage()
    {
        return (bool) $this->getInput()->getOption(MigrateMessage::OPT_NO_STORAGE);
    }

    /**
     * showProgress
     *
     * @return bool
     */
    public function showProgress()
    {
        return (bool) $this->getInput()->getOption(MigrateMessage::OPT_PROGRESS);
    }

    /**
     * shouldSaveChanges
     *
     * @return bool
     */
    public function shouldSaveChanges()
    {
        return !$this->isNoStorage() && !$this->isDryRun();
    }

    /**
     * shouldTrackProgress
     *
     * @return bool
     */
    public function shouldTrackProgress()
    {
        return ($this->getOutput()->getVerbosity() !== OutputInterface::VERBOSITY_QUIET) && $this->showProgress();
    }

    /**
     * @return string
     *
     * @throws CliException
     */
    public function getStrategy()
    {
        $strategy = strtolower($this->getInput()->getOption(MigrateMessage::OPT_STRATEGY));
        if (!in_array($strategy, $this->strategies)) {
            throw new CliException(sprintf(
                'Unknown strategy "%s". Must be one of: %s',
                $strategy,
                implode(', ', $this->strategies)
            ));
        }

        return $strategy;
    }

    /**
     * getDirection
     * @return null|static
     * @throws CliException
     */
    public function getDirection()
    {
        $strategy = $this->getStrategy();
        $direction = null;
        if ($strategy == self::STRATEGY_UP) {
            $direction = Direction::up();
        } elseif ($strategy == self::STRATEGY_DOWN) {
            $direction = Direction::down();
        }
        return $direction;
    }
}