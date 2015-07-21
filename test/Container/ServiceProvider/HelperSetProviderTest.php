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

namespace BaleenTest\Baleen\Container\ServiceProvider;

use Baleen\Cli\Config\AppConfig;
use Baleen\Cli\Container\ServiceProvider\AppConfigProvider;
use Baleen\Cli\Container\ServiceProvider\HelperSetProvider;
use Baleen\Cli\Helper\ConfigHelper;
use League\Container\Container;
use Mockery as m;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;

/**
 * Class HelperSetProviderTest
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
class HelperSetProviderTest extends ServiceProviderTestCase
{

    public function testRegister()
    {
        $self = $this;
        $container = $this->container;
        $this->container->shouldReceive('withArgument')->with(AppConfigProvider::SERVICE_CONFIG)->once();
        $this->container->shouldReceive('add')->with(QuestionHelper::class)->once();

        $configHelperExpectation = m::mock();
        $configHelperExpectation->shouldReceive('withArgument')->with(AppConfigProvider::SERVICE_CONFIG)->once();
        $this->container->shouldReceive('add')->with(ConfigHelper::class)->once()->andReturn($configHelperExpectation);

        $this->setInstance(m::mock(HelperSetProvider::class)->makePartial());

        $this->container->shouldReceive('get')->with(QuestionHelper::class)
            ->andReturn(new QuestionHelper());
        $this->container->shouldReceive('get')->with(ConfigHelper::class)
            ->andReturn(new ConfigHelper(m::mock(AppConfig::class)));

        $this->assertSingletonProvided(
            HelperSetProvider::SERVICE_HELPERSET,
            function() use ($self, $container) {
                list(, $callback) = func_get_args();
                $self->assertInstanceOf(HelperSet::class, $callback());
                return $container;
            }
        );
        $this->instance->register();
    }
}
