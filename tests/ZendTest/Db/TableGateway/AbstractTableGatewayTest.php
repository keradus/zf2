<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZendTest\Db\TableGateway;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql;
use Zend\Db\ResultSet\ResultSet;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-03-01 at 21:02:22.
 */
class AbstractTableGatewayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_Generator
     */
    protected $mockAdapter = null;

    /**
     * @var \PHPUnit_Framework_MockObject_Generator
     */
    protected $mockSql = null;

    /**
     * @var AbstractTableGateway
     */
    protected $table;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        // mock the adapter, driver, and parts
        $mockResult = $this->getMock('Zend\Db\Adapter\Driver\ResultInterface');
        $mockResult->expects($this->any())->method('getAffectedRows')->will($this->returnValue(5));

        $mockStatement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');
        $mockStatement->expects($this->any())->method('execute')->will($this->returnValue($mockResult));

        $mockConnection = $this->getMock('Zend\Db\Adapter\Driver\ConnectionInterface');
        $mockConnection->expects($this->any())->method('getLastGeneratedValue')->will($this->returnValue(10));

        $mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue($mockStatement));
        $mockDriver->expects($this->any())->method('getConnection')->will($this->returnValue($mockConnection));

        $this->mockAdapter = $this->getMock('Zend\Db\Adapter\Adapter', null, array($mockDriver));
        $this->mockSql = $this->getMock('Zend\Db\Sql\Sql', array('select', 'insert', 'update', 'delete'), array($this->mockAdapter, 'foo'));
        $this->mockSql->expects($this->any())->method('select')->will($this->returnValue($this->getMock('Zend\Db\Sql\Select', array('where', 'getRawSate'), array('foo'))));
        $this->mockSql->expects($this->any())->method('insert')->will($this->returnValue($this->getMock('Zend\Db\Sql\Insert', array('prepareStatement', 'values'), array('foo'))));
        $this->mockSql->expects($this->any())->method('update')->will($this->returnValue($this->getMock('Zend\Db\Sql\Update', array('where'), array('foo'))));
        $this->mockSql->expects($this->any())->method('delete')->will($this->returnValue($this->getMock('Zend\Db\Sql\Delete', array('where'), array('foo'))));

        $this->table = $this->getMockForAbstractClass(
            'Zend\Db\TableGateway\AbstractTableGateway'
            //array('getTable')
        );
        $tgReflection = new \ReflectionClass('Zend\Db\TableGateway\AbstractTableGateway');
        foreach ($tgReflection->getProperties() as $tgPropReflection) {
            $tgPropReflection->setAccessible(true);
            switch ($tgPropReflection->getName()) {
                case 'table':
                    $tgPropReflection->setValue($this->table, 'foo');
                    break;
                case 'adapter':
                    $tgPropReflection->setValue($this->table, $this->mockAdapter);
                    break;
                case 'resultSetPrototype':
                    $tgPropReflection->setValue($this->table, new ResultSet());
                    break;
                case 'sql':
                    $tgPropReflection->setValue($this->table, $this->mockSql);
                    break;
            }
        }
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Zend\Db\TableGateway\AbstractTableGateway::getTable
     */
    public function testGetTable()
    {
        $this->assertEquals('foo', $this->table->getTable());
    }

    /**
     * @covers Zend\Db\TableGateway\AbstractTableGateway::getAdapter
     */
    public function testGetAdapter()
    {
        $this->assertSame($this->mockAdapter, $this->table->getAdapter());
    }

    /**
     * @covers Zend\Db\TableGateway\AbstractTableGateway::getSql
     */
    public function testGetSql()
    {
        $this->assertInstanceOf('Zend\Db\Sql\Sql', $this->table->getSql());
    }

    /**
     * @covers Zend\Db\TableGateway\AbstractTableGateway::getResultSetPrototype
     */
    public function testGetSelectResultPrototype()
    {
        $this->assertInstanceOf('Zend\Db\ResultSet\ResultSet', $this->table->getResultSetPrototype());
    }

    /**
     * @covers Zend\Db\TableGateway\AbstractTableGateway::select
     * @covers Zend\Db\TableGateway\AbstractTableGateway::selectWith
     * @covers Zend\Db\TableGateway\AbstractTableGateway::executeSelect
     */
    public function testSelectWithNoWhere()
    {
        $resultSet = $this->table->select();

        // check return types
        $this->assertInstanceOf('Zend\Db\ResultSet\ResultSet', $resultSet);
        $this->assertNotSame($this->table->getResultSetPrototype(), $resultSet);
    }

    /**
     * @covers Zend\Db\TableGateway\AbstractTableGateway::select
     * @covers Zend\Db\TableGateway\AbstractTableGateway::selectWith
     * @covers Zend\Db\TableGateway\AbstractTableGateway::executeSelect
     */
    public function testSelectWithWhereString()
    {
        $mockSelect = $this->mockSql->select();

        $mockSelect->expects($this->any())
            ->method('getRawState')
            ->will($this->returnValue(array(
                'table' => $this->table->getTable(),
                ))
            );

        // assert select::from() is called
        $mockSelect->expects($this->once())
            ->method('where')
            ->with($this->equalTo('foo'));

        $this->table->select('foo');
    }

    /**
     * @covers Zend\Db\TableGateway\AbstractTableGateway::select
     * @covers Zend\Db\TableGateway\AbstractTableGateway::selectWith
     * @covers Zend\Db\TableGateway\AbstractTableGateway::executeSelect
     *
     * This is a test for the case when a valid $select is built using an aliased table name, then used
     * with AbstractTableGateway::selectWith (or AbstractTableGateway::select).
     *
     * $myTable = new MyTable(...);
     * $sql = new \Zend\Db\Sql\Sql(...);
     * $select = $sql->select()->from(array('t' => 'mytable'));
     *
     * // Following fails, with Fatal error: Uncaught exception 'RuntimeException' with message
     * 'The table name of the provided select object must match that of the table' unless fix is provided.
     * $myTable->selectWith($select);
     *
     */
    public function testSelectWithArrayTable()
    {
        // Case 1

        $select1 = $this->getMock('Zend\Db\Sql\Select', array('getRawState'));
        $select1->expects($this->once())
            ->method('getRawState')
            ->will($this->returnValue(array(
                'table' => 'foo',               // Standard table name format, valid according to Select::from()
                'columns' => null,
            )));
        $return = $this->table->selectWith($select1);
        $this->assertNotNull($return);

        // Case 2

        $select1 = $this->getMock('Zend\Db\Sql\Select', array('getRawState'));
        $select1->expects($this->once())
            ->method('getRawState')
            ->will($this->returnValue(array(
                'table' => array('f' => 'foo'), // Alias table name format, valid according to Select::from()
                'columns' => null,
            )));
        $return = $this->table->selectWith($select1);
        $this->assertNotNull($return);
    }

    /**
     * @covers Zend\Db\TableGateway\AbstractTableGateway::insert
     * @covers Zend\Db\TableGateway\AbstractTableGateway::insertWith
     * @covers Zend\Db\TableGateway\AbstractTableGateway::executeInsert
     */
    public function testInsert()
    {
        $mockInsert = $this->mockSql->insert();

        $mockInsert->expects($this->once())
            ->method('prepareStatement')
            ->with($this->mockAdapter);

        $mockInsert->expects($this->once())
            ->method('values')
            ->with($this->equalTo(array('foo' => 'bar')));

        $affectedRows = $this->table->insert(array('foo' => 'bar'));
        $this->assertEquals(5, $affectedRows);
    }

    /**
     * @covers Zend\Db\TableGateway\AbstractTableGateway::update
     * @covers Zend\Db\TableGateway\AbstractTableGateway::updateWith
     * @covers Zend\Db\TableGateway\AbstractTableGateway::executeUpdate
     */
    public function testUpdate()
    {
        $mockUpdate = $this->mockSql->update();

        // assert select::from() is called
        $mockUpdate->expects($this->once())
            ->method('where')
            ->with($this->equalTo('id = 2'));

        $affectedRows = $this->table->update(array('foo' => 'bar'), 'id = 2');
        $this->assertEquals(5, $affectedRows);
    }

    /**
     * @covers Zend\Db\TableGateway\AbstractTableGateway::update
     * @covers Zend\Db\TableGateway\AbstractTableGateway::updateWith
     * @covers Zend\Db\TableGateway\AbstractTableGateway::executeUpdate
     */
    public function testUpdateWithNoCriteria()
    {
        $mockUpdate = $this->mockSql->update();

        $affectedRows = $this->table->update(array('foo' => 'bar'));
        $this->assertEquals(5, $affectedRows);
    }

    /**
     * @covers Zend\Db\TableGateway\AbstractTableGateway::delete
     * @covers Zend\Db\TableGateway\AbstractTableGateway::deleteWith
     * @covers Zend\Db\TableGateway\AbstractTableGateway::executeDelete
     */
    public function testDelete()
    {
        $mockDelete = $this->mockSql->delete();

        // assert select::from() is called
        $mockDelete->expects($this->once())
            ->method('where')
            ->with($this->equalTo('foo'));

        $affectedRows = $this->table->delete('foo');
        $this->assertEquals(5, $affectedRows);
    }

    /**
     * @covers Zend\Db\TableGateway\AbstractTableGateway::getLastInsertValue
     */
    public function testGetLastInsertValue()
    {
        $this->table->insert(array('foo' => 'bar'));
        $this->assertEquals(10, $this->table->getLastInsertValue());
    }

    /**
     * @covers Zend\Db\TableGateway\AbstractTableGateway::__get
     */
    public function test__get()
    {
        $this->table->insert(array('foo')); // trigger last insert id update

        $this->assertEquals(10, $this->table->lastInsertValue);
        $this->assertSame($this->mockAdapter, $this->table->adapter);
        //$this->assertEquals('foo', $this->table->table);
    }

    /**
     * @covers Zend\Db\TableGateway\AbstractTableGateway::__clone
     */
    public function test__clone()
    {
        $cTable = clone $this->table;
        $this->assertSame($this->mockAdapter, $cTable->getAdapter());
    }
}
