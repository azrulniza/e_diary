<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PageTemplatesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PageTemplatesTable Test Case
 */
class PageTemplatesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\PageTemplatesTable
     */
    public $PageTemplates;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.page_templates',
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
        $config = TableRegistry::exists('PageTemplates') ? [] : ['className' => 'App\Model\Table\PageTemplatesTable'];
        $this->PageTemplates = TableRegistry::get('PageTemplates', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PageTemplates);

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
