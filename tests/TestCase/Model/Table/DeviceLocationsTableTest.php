<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DeviceLocationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DeviceLocationsTable Test Case
 */
class DeviceLocationsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\DeviceLocationsTable
     */
    public $DeviceLocations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.device_locations'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('DeviceLocations') ? [] : ['className' => 'App\Model\Table\DeviceLocationsTable'];
        $this->DeviceLocations = TableRegistry::get('DeviceLocations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DeviceLocations);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
