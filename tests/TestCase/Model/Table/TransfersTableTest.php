<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TransfersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TransfersTable Test Case
 */
class TransfersTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\TransfersTable
     */
    public $Transfers;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.transfers',
        'app.resellers',
        'app.reseller_statuses',
        'app.reseller_types',
        'app.clients',
        'app.client_statuses',
        'app.client_histories',
        'app.client_history_types',
        'app.users_clients',
        'app.client_subscriptions',
        'app.users',
        'app.roles',
        'app.users_roles',
        'app.menus',
        'app.menu_groups',
        'app.menus_permissions',
        'app.users_resellers',
        'app.packages',
        'app.cmsmenus_permission',
        'app.cmsmenus',
        'app.packages_cmsmenus',
        'app.usergroups',
        'app.subscription_resellers',
        'app.subscription_package_resellers',
        'app.subscription_types',
        'app.cmsplugins',
        'app.cmsplugintypes',
        'app.packages_cmsplugintypes',
        'app.packages_cmsplugins',
        'app.slottextstyles',
        'app.packages_slottextstyles',
        'app.slottypes',
        'app.packages_slottypes',
        'app.templates',
        'app.packages_templates',
        'app.product_keys',
        'app.product_key_batches',
        'app.product_keys_logs',
        'app.product_key_subscriptions',
        'app.devices',
        'app.device_statuses',
        'app.device_status_logs',
        'app.device_locations',
        'app.device_location_logs',
        'app.remote_commands',
        'app.remote_command_types',
        'app.product_key_packages',
        'app.client_cmscameras',
        'app.client_cmses',
        'app.plugins',
        'app.clients_plugins',
        'app.subscriptions',
        'app.transfer_detail'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Transfers') ? [] : ['className' => 'App\Model\Table\TransfersTable'];
        $this->Transfers = TableRegistry::get('Transfers', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Transfers);

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
