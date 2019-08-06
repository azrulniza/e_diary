<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\NotificationEmailTemplatesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\NotificationEmailTemplatesTable Test Case
 */
class NotificationEmailTemplatesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\NotificationEmailTemplatesTable
     */
    public $NotificationEmailTemplates;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.notification_email_templates',
        'app.notification_types'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('NotificationEmailTemplates') ? [] : ['className' => 'App\Model\Table\NotificationEmailTemplatesTable'];
        $this->NotificationEmailTemplates = TableRegistry::get('NotificationEmailTemplates', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->NotificationEmailTemplates);

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
