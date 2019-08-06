<?php
namespace App\Test\TestCase\Controller;

use App\Controller\PageTemplatesClientsController;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\PageTemplatesClientsController Test Case
 */
class PageTemplatesClientsControllerTest extends IntegrationTestCase
{

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
