<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SubscriptionPackageClientsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SubscriptionPackageClientsTable Test Case
 */
class SubscriptionPackageClientsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\SubscriptionPackageClientsTable
     */
    public $SubscriptionPackageClients;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.subscription_package_clients'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('SubscriptionPackageClients') ? [] : ['className' => 'App\Model\Table\SubscriptionPackageClientsTable'];
        $this->SubscriptionPackageClients = TableRegistry::get('SubscriptionPackageClients', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->SubscriptionPackageClients);

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
