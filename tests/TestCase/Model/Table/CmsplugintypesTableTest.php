<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CmsplugintypesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CmsplugintypesTable Test Case
 */
class CmsplugintypesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CmsplugintypesTable
     */
    public $Cmsplugintypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.cmsplugintypes',
        'app.cmsplugins',
        'app.packages',
        'app.packages_cmsplugins',
        'app.packages_cmsplugintypes'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Cmsplugintypes') ? [] : ['className' => 'App\Model\Table\CmsplugintypesTable'];
        $this->Cmsplugintypes = TableRegistry::get('Cmsplugintypes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Cmsplugintypes);

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
