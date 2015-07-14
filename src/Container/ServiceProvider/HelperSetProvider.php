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

namespace Baleen\Baleen\Container\ServiceProvider;

use Baleen\Baleen\Command\Storage\StorageCommand;
use Baleen\Baleen\Config\AppConfig;
use Baleen\Baleen\Exception\CliException;
use Baleen\Baleen\Helper\ConfigHelper;
use Baleen\Migrations\Storage\FileStorage;
use League\Container\ServiceProvider;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;

/**
 * Class HelperSetProvider
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
class HelperSetProvider extends ServiceProvider
{

    const SERVICE_HELPERSET = 'helper-set';

    protected $provides = [
        self::SERVICE_HELPERSET,
        QuestionHelper::class,
        ConfigHelper::class,
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        $container = $this->getContainer();
        $container->singleton(self::SERVICE_HELPERSET, function() use ($container) {
                $helperSet = new HelperSet();
                $helperSet->set($container->get(QuestionHelper::class), 'question');
                $helperSet->set($container->get(ConfigHelper::class));
                return $helperSet;
            })
            ->withArgument(AppConfigProvider::SERVICE_CONFIG);

        $container->add(QuestionHelper::class);
        $container->add(ConfigHelper::class)
            ->withArgument(AppConfigProvider::SERVICE_CONFIG);
    }
}