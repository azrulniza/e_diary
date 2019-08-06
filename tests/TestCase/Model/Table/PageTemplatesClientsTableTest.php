<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PageTemplatesClientsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PageTemplatesClientsTable Test Case
 */
class PageTemplatesClientsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\PageTemplatesClientsTable
     */
    public $PageTemplatesClients;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.page_templates_clients',
        'app.page_templates',
        'app.page_slots',
        'app.users',
        'app.roles',
        'app.users_roles',
        'app.menus',
        'app.menu_groups',
        'app.menus_permissions',
        'app.clients',
        'app.client_statuses',
        'app.client_histories',
        'app.client_history_types',
        'app.resellers',
        'app.reseller_statuses',
        'app.reseller_types',
        'app.subscriptions',
        'app.subscription_types',
        'app.users_resellers',
        'app.users_clients',
        'app.client_subscriptions',
        'app.subscription_package_clients',
        'app.client_cmses',
        'app.plugins',
        'app.clients_plugins',
        'app.page_templates_resellers'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('PageTemplatesClients') ? [] : ['className' => 'App\Model\Table\PageTemplatesClientsTable'];
        $this->PageTemplatesClients = TableRegistry::get('PageTemplatesClients', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PageTemplatesClients);

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
