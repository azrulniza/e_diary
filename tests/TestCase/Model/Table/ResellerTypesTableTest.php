<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ResellerTypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ResellerTypesTable Test Case
 */
class ResellerTypesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ResellerTypesTable
     */
    public $ResellerTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.reseller_types',
        'app.resellers',
        'app.reseller_statuses',
        'app.clients',
        'app.client_statuses',
        'app.client_histories',
        'app.client_history_types',
        'app.subscriptions',
        'app.subscription_types',
        'app.users',
        'app.plugins',
        'app.clients_plugins',
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
        $config = TableRegistry::exists('ResellerTypes') ? [] : ['className' => 'App\Model\Table\ResellerTypesTable'];
        $this->ResellerTypes = TableRegistry::get('ResellerTypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ResellerTypes);

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
