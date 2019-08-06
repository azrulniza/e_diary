<?php
namespace App\Test\TestCase\Controller;

use App\Controller\TransfersController;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\TransfersController Test Case
 */
class TransfersControllerTest extends IntegrationTestCase
{

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
        'app.users',
        'app.roles',
        'app.users_roles',
        'app.menus',
        'app.menu_groups',
        'app.menus_permissions',
        'app.users_resellers',
        'app.client_subscriptions',
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
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testView()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
