<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ResellerStatusesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ResellerStatusesTable Test Case
 */
class ResellerStatusesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ResellerStatusesTable
     */
    public $ResellerStatuses;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.reseller_statuses',
        'app.resellers',
        'app.reseller_types',
        'app.clients',
        'app.client_statuses',
        'app.plugins',
        'app.clients_plugins',
        'app.users',
        'app.users_clients',
        'app.users_resellers'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('ResellerStatuses') ? [] : ['className' => 'App\Model\Table\ResellerStatusesTable'];
        $this->ResellerStatuses = TableRegistry::get('ResellerStatuses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ResellerStatuses);

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
