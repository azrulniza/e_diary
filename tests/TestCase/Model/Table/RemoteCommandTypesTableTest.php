<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\RemoteCommandTypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\RemoteCommandTypesTable Test Case
 */
class RemoteCommandTypesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\RemoteCommandTypesTable
     */
    public $RemoteCommandTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.remote_command_types',
        'app.remote_commands',
        'app.devices',
        'app.device_statuses',
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
        'app.menus',
        'app.menu_groups',
        'app.menus_permissions',
        'app.users_clients',
        'app.users_resellers',
        'app.subscription_clients',
        'app.subscription_package_clients',
        'app.client_cmses',
        'app.plugins',
        'app.clients_plugins',
        'app.device_status_logs',
        'app.device_locations',
        'app.device_location_logs',
        'app.product_keys',
        'app.product_key_batches'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('RemoteCommandTypes') ? [] : ['className' => 'App\Model\Table\RemoteCommandTypesTable'];
        $this->RemoteCommandTypes = TableRegistry::get('RemoteCommandTypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->RemoteCommandTypes);

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
