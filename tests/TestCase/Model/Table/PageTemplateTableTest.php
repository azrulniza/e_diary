<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PageTemplateTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PageTemplateTable Test Case
 */
class PageTemplateTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\PageTemplateTable
     */
    public $PageTemplate;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.page_template',
        'app.page_slot'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('PageTemplate') ? [] : ['className' => 'App\Model\Table\PageTemplateTable'];
        $this->PageTemplate = TableRegistry::get('PageTemplate', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PageTemplate);

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
