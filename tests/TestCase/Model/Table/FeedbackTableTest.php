<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FeedbackTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FeedbackTable Test Case
 */
class FeedbackTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\FeedbackTable
     */
    public $Feedback;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.feedback',
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
        'app.master_reseller_subscriptions',
        'app.packages',
        'app.client_subscriptions',
        'app.cmsmenus_permission',
        'app.cmsmenus',
        'app.packages_cmsmenus',
        'app.usergroups',
        'app.reseller_subscriptions',
        'app.subscription_resellers',
        'app.subscription_package_resellers',
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
        'app.product_key_subscriptions',
        'app.devices',
        'app.device_statuses',
        'app.device_status_logs',
        'app.device_locations',
        'app.device_location_logs',
        'app.remote_commands',
        'app.remote_command_types',
        'app.users_clients',
        'app.client_cmscameras',
        'app.client_cmses',
        'app.plugins',
        'app.clients_plugins'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Feedback') ? [] : ['className' => 'App\Model\Table\FeedbackTable'];
        $this->Feedback = TableRegistry::get('Feedback', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Feedback);

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
