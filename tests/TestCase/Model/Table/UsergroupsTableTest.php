<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UsergroupsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\UsergroupsTable Test Case
 */
class UsergroupsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\UsergroupsTable
     */
    public $Usergroups;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.usergroups',
        'app.cmsmenus_permission',
        'app.packages',
        'app.client_packages',
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
        'app.client_subscriptions',
        'app.subscription_package_clients',
        'app.client_cmscameras',
        'app.client_cmses',
        'app.plugins',
        'app.clients_plugins',
        'app.subscription_resellers',
        'app.subscription_package_resellers',
        'app.cmsmenus',
        'app.packages_cmsmenus',
        'app.cmsplugins',
        'app.cmsplugintypes',
        'app.packages_cmsplugintypes',
        'app.packages_cmsplugins',
        'app.slottextstyles',
        'app.packages_slottextstyles',
        'app.slottypes',
        'app.packages_slottypes',
        'app.templates',
        'app.packages_templates'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Usergroups') ? [] : ['className' => 'App\Model\Table\UsergroupsTable'];
        $this->Usergroups = TableRegistry::get('Usergroups', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Usergroups);

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
