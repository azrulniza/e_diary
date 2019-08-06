<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PackagesTemplatesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PackagesTemplatesTable Test Case
 */
class PackagesTemplatesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\PackagesTemplatesTable
     */
    public $PackagesTemplates;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.packages_templates',
        'app.packages',
        'app.client_subscriptions',
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
        'app.client_cmscameras',
        'app.client_cmses',
        'app.plugins',
        'app.clients_plugins',
        'app.subscription_package_clients',
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
        'app.templates'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('PackagesTemplates') ? [] : ['className' => 'App\Model\Table\PackagesTemplatesTable'];
        $this->PackagesTemplates = TableRegistry::get('PackagesTemplates', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PackagesTemplates);

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
