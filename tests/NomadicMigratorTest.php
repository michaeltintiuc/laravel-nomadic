<?php

namespace ChrisHalbert\LaravelNomadic;

use Illuminate\Filesystem\Filesystem;
use SebastianBergmann\PeekAndPoke\Proxy;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\Migrations\Migration;

class NomadicMigratorTest extends \PHPUnit_Framework_TestCase
{
    protected $repository;

    protected $migrator;

    protected $nomadicMigrator;

    public static $resolver;

    public static function setUpBeforeClass()
    {
        function app()
        {
            return new DatabaseNomadicRepository(self::$resolver, '');
        }
    }

    public function setUp()
    {
        self::$resolver = $this->getMockBuilder(ConnectionResolver::class)
            ->disableOriginalConstructor()
            ->getMock();

        $files = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder(DatabaseNomadicRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['log'])
            ->getMock();

        $this->nomadicMigrator = $this->getMockBuilder(NomadicMigrator::class)
            ->setConstructorArgs([$this->repository, self::$resolver, $files])
            ->setMethods(['pretendToRun', 'runMigration', 'note'])
            ->getMock();

        $this->migrator = new Proxy($this->nomadicMigrator);
    }

    public function testResolveReturnsNomadicMigration()
    {
        $fileName = '2018_04_04_000000_NomadicMockMigration';
        require_once __DIR__."/files/$fileName.php";
        $this->assertInstanceOf(NomadicMigration::class, $this->migrator->resolve($fileName));
    }

    public function testResolveReturnsStockMigration()
    {
        $fileName = '2018_04_04_000000_StandardMigration';
        require_once __DIR__."/files/$fileName.php";
        $this->assertInstanceOf(Migration::class, $test = $this->migrator->resolve($fileName));
    }

    public function testRunUpWithPretend()
    {
        $this->nomadicMigrator->expects($this->once())
            ->method('pretendToRun')
            ->willReturn(true);

        $this->assertTrue($this->migrator->runUp('2018_04_04_000000_StandardMigration', 1, true));
    }

    public function testRunUpWithoutPretend()
    {
        $this->nomadicMigrator->expects($this->never())
            ->method('pretendToRun');

        $this->nomadicMigrator->expects($this->exactly(2))
            ->method('note');

        $this->repository->expects($this->once())
            ->method('log')
            ->with('2018_04_04_000000_NomadicMockMigration', 1, ['property' => 'value']);

        $this->migrator->runUp('2018_04_04_000000_NomadicMockMigration', 1, false);
    }

    public function tearDown()
    {
        unset($this->repository);
        unset($this->migrator);
    }
}
