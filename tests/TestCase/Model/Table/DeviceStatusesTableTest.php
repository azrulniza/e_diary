<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DeviceStatusesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DeviceStatusesTable Test Case
 */
class DeviceStatusesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\DeviceStatusesTable
     */
    public $DeviceStatuses;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.device_statuses',
        'app.devices',
        'app.clients',
        'app.client_statuses',
        'app.client_histories',
        'app.client_history_types',
        'app.resellers',
        'app.reseller_statuses',
        'app.reseller_types',
        'app.subscriptions',
        'app.subscription_types',
        'app.users',
        'app.roles',
        'app.users_roles',
        'app.users_resellers',
        'app.plugins',
        'app.clients_plugins',
        'app.users_clients'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('DeviceStatuses') ? [] : ['className' => 'App\Model\Table\DeviceStatusesTable'];
        $this->DeviceStatuses = TableRegistry::get('DeviceStatuses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DeviceStatuses);

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

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
