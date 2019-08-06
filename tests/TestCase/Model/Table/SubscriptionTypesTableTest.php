<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SubscriptionTypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SubscriptionTypesTable Test Case
 */
class SubscriptionTypesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\SubscriptionTypesTable
     */
    public $SubscriptionTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.subscription_types',
        'app.subscriptions',
        'app.clients',
        'app.client_statuses',
        'app.client_histories',
        'app.client_history_types',
        'app.resellers',
        'app.reseller_statuses',
        'app.reseller_types',
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
        $config = TableRegistry::exists('SubscriptionTypes') ? [] : ['className' => 'App\Model\Table\SubscriptionTypesTable'];
        $this->SubscriptionTypes = TableRegistry::get('SubscriptionTypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->SubscriptionTypes);

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
