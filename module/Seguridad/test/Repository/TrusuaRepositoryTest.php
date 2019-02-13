<?php

namespace SeguridadTest\Repository;

use Seguridad\Entity\Trusua;
use Seguridad\Repository\Impl\TrusuaRepository;
use Seguridad\Repository\ITrusuaRepository;
use Seguridad\Core\BaseRepository;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Zend\Db\Adapter\AdapterInterface;
use Interop\Container\ContainerInterface;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\TableGateway\TableGatewayInterface;
use Prophecy\Argument;

class TrusuaRepositoryTest extends TestCase
    {

    private $repository = null;
    private $baseRepository = null;

    protected function setUp()
    {
        $this->baseRepository = $this->prophesize(BaseRepository::class);
        $this->repository = new TrusuaRepository($this->baseRepository->reveal());
    }

    public function testFetchAllReturnsAll()
    {
        $resultSet = $this->prophesize(ResultSetInterface::class)->reveal();
        $this->baseRepository->fetchAll()->willReturn($resultSet);
        $this->assertSame($resultSet, $this->repository->fetchAll());
    }

    public function testCanDeleteId()
    {
        $this->baseRepository->delete(['russecu' => 999])->shouldBeCalled();
        $this->repository->delete(999);
    }

    public function testSaveWillInsertNewIfTheyDontAlreadyHaveAnId()
    {
        $data = [
            'rpesecu' => '2',
            'rusucod' => 'In My Dreams'
        ];
        $trusua = new Trusua();
        $trusua->exchangeArray($data);

        $this->baseRepository->insert($data)->shouldBeCalled();
        $this->repository->save($data);
    }

    public function testSaveWillUpdateExistingIfTheyAlreadyHaveAnId()
    {
        $data = [
            'russecu' => 123,
            'rpesecu' => 3,
            'rusucod' => 'codigo',
        ];
        $trusua = new Trusua();
        $trusua->exchangeArray($data);

        $resultSet = $this->prophesize(ResultSetInterface::class);
        $resultSet->current()->willReturn($data);

        $this->baseRepository
                ->find(['russecu' => 123])
                ->willReturn($resultSet->reveal());
        $this->baseRepository
                ->update(
                        array_filter($data, function ($key)
                        {
                            return in_array($key, ['russecu', 'rpesecu', 'rusucod']);
                        }, ARRAY_FILTER_USE_KEY), ['russecu' => 123]
                )->shouldBeCalled();

        $this->repository->save($data);
    }

//    public function testExceptionIsThrownWhenGettingNonExistent()
//    {
//        $resultSet = $this->prophesize(ResultSetInterface::class);
//        $resultSet->current()->willReturn(null);
//
//        $this->baseRepository
//                ->find(['russecu' => 123])
//                ->willReturn($resultSet->reveal());
//
//        $this->expectException(RuntimeException::class);
//        $this->expectExceptionMessage('Could not find row with identifier 123');
//        $this->repository->find(123);
//    }

    }
