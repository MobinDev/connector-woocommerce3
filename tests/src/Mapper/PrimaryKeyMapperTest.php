<?php

namespace JtlWooCommerceConnector\Tests\Mapper;

use Jtl\Connector\Core\Definition\IdentityType;
use JtlWooCommerceConnector\Mapper\PrimaryKeyMapper;
use JtlWooCommerceConnector\Tests\AbstractTestCase;
use JtlWooCommerceConnector\Utilities\SqlHelper;

class PrimaryKeyMapperTest extends AbstractTestCase
{
    public function testGetHostId(): void
    {
        $db = $this->createDbMock(['queryOne']);
        $db->expects($this->once())->method('queryOne')->willReturn(1);

        $sqlHelper = $this->createSqlHelperMock();

        $primaryKeyMapper = $this->createPrimaryKeyMapperMock([$db, $sqlHelper]);

        $result = $primaryKeyMapper->getHostId(IdentityType::CATEGORY, 'c_1');

        $this->assertEquals(1, $result);
    }

    public function testGetEndpointId(): void
    {
        $db = $this->createDbMock(['queryOne']);
        $db->expects($this->once())->method('queryOne')->willReturn('c_1');

        $sqlHelper = $this->createSqlHelperMock();

        $primaryKeyMapper = $this->createPrimaryKeyMapperMock([$db, $sqlHelper]);

        $result = $primaryKeyMapper->getEndpointId(IdentityType::CATEGORY, 1);

        $this->assertEquals('c_1', $result);
    }

    public function testSave(): void
    {
        $db = $this->createDbMock(['query']);
        $db->expects($this->once())->method('query')->willReturn([1]);

        $sqlHelper = $this->createSqlHelperMock();

        $primaryKeyMapper = $this->createPrimaryKeyMapperMock([$db, $sqlHelper]);

        $result = $primaryKeyMapper->save(IdentityType::CATEGORY, 'c_1',1);

        $this->assertTrue($result);
    }

    public function testDelete(): void
    {
        $db = $this->createDbMock(['query']);
        $db->expects($this->once())->method('query')->willReturn(1);

        $sqlHelper = $this->createSqlHelperMock();

        $primaryKeyMapper = $this->createPrimaryKeyMapperMock([$db, $sqlHelper]);

        $result = $primaryKeyMapper->delete(IdentityType::CATEGORY, 'c_1',1);

        $this->assertTrue($result);
    }

    protected function createPrimaryKeyMapperMock(array $constructorArgs = [], array $onlyMethods = [])
    {
        return $this->getMockBuilder(PrimaryKeyMapper::class)
            ->setConstructorArgs($constructorArgs)
            ->onlyMethods($onlyMethods)
            ->getMock();
    }

    protected function createSqlHelperMock()
    {
        return $this->getMockBuilder(SqlHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}