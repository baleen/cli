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

namespace BaleenTest\Cli\CommandBus\Repository;

use Baleen\Cli\CommandBus\Repository\AbstractRepositoryMessage;
use Baleen\Cli\CommandBus\Repository\CreateMessage;
use BaleenTest\Cli\CommandBus\MessageTestCase;
use Mockery as m;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class CreateMessageTest
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
class CreateMessageTest extends MessageTestCase
{
    /**
     * getClassName must return a string with the FQN of the command class being tested
     * @return string
     */
    protected function getClassName()
    {
        return CreateMessage::class;
    }

    /**
     * testConstructor
     */
    public function testConstructor()
    {
        $instance = new CreateMessage();
        $this->assertInstanceOf(AbstractRepositoryMessage::class, $instance);
    }

    /**
     * @inheritDoc
     */
    protected function getExpectations()
    {
        return [
            [   'name' => 'setName',
                'with' => 'migrations:create',
            ],
            [   'name' => 'setAliases',
                'with' => [['create']],
            ],
            [   'name' => 'setDescription',
                'with' => [m::type('string')],
            ],
            [   'name' => 'addArgument',
                'with' => ['title', m::any(), m::type('string'), m::any()]
            ],
            [   'name' => 'addOption',
                'with' => ['namespace', m::any(), InputOption::VALUE_OPTIONAL, m::type('string'), m::any()]
            ],
            [   'name' => 'addOption',
                'with' => ['editor-cmd', m::any(), InputOption::VALUE_OPTIONAL, m::type('string')]
            ],
        ];
    }


}
