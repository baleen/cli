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

use Baleen\Cli\CommandBus\AbstractMessage;
use Baleen\Cli\CommandBus\Repository\AbstractRepositoriesMessage;
use Baleen\Cli\CommandBus\Util\RepositoriesAwareInterface;
use Baleen\Cli\Repository\RepositoryCollectionInterface;
use Baleen\Migrations\Version;
use BaleenTest\Cli\BaseTestCase;
use League\Flysystem\Filesystem;
use Mockery as m;

/**
 * Class AbstractRepositoryMessageTest
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
class AbstractRepositoryMessageTest extends BaseTestCase
{
    /** @var m\Mock|AbstractRepositoriesMessage */
    protected $instance;

    /**
     * setUp
     */
    public function setUp()
    {
        parent::setUp();
        $this->instance = m::mock(AbstractRepositoriesMessage::class)
            ->makePartial();
    }

    /**
     * testConstructor
     */
    public function testConstructor()
    {
        $this->assertInstanceOf(AbstractMessage::class, $this->instance);
        $this->assertInstanceOf(RepositoriesAwareInterface::class, $this->instance);
    }

    /**
     * testGetSetRepository
     */
    public function testGetSetRepository()
    {
        /** @var m\Mock|RepositoryCollectionInterface $repository */
        $repository = m::mock(RepositoryCollectionInterface::class);
        $this->instance->setRepositories($repository);
        $this->assertSame($repository, $this->instance->getRepositories());
    }

    /**
     * testGetSetFilesystem
     */
    public function testGetSetFilesystem()
    {
        /** @var m\Mock|Filesystem $filesystem */
        $filesystem = m::mock(Filesystem::class);
        $this->instance->setFilesystem($filesystem);
        $this->assertSame($filesystem, $this->instance->getFilesystem());
    }
}
