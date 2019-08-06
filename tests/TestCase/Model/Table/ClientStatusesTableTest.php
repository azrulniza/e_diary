<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ClientStatusesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ClientStatusesTable Test Case
 */
class ClientStatusesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ClientStatusesTable
     */
    public $ClientStatuses;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.client_statuses',
        'app.client_histories',
        'app.client_history_types',
        'app.clients',
        'app.resellers',
        'app.reseller_statuses',
        'app.reseller_types',
        'app.users',
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
        $config = TableRegistry::exists('ClientStatuses') ? [] : ['className' => 'App\Model\Table\ClientStatusesTable'];
        $this->ClientStatuses = TableRegistry::get('ClientStatuses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ClientStatuses);

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
