<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ResellersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ResellersTable Test Case
 */
class ResellersTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ResellersTable
     */
    public $Resellers;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.resellers',
        'app.reseller_statuses',
        'app.reseller_types',
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
        $config = TableRegistry::exists('Resellers') ? [] : ['className' => 'App\Model\Table\ResellersTable'];
        $this->Resellers = TableRegistry::get('Resellers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Resellers);

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
