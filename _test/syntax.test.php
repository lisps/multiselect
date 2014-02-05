<?php
/**
 * @group plugin_multiselect
 * @group plugins
 */
class plugin_multiselect_syntax_test extends DokuWikiTest {

    public function setup() {
        $this->pluginsEnabled[] = 'multiselect';
        $this->pluginsEnabled[] = 'ajaxedit';
        parent::setup();
    }

    
    public function test_basic_syntax() {
        global $INFO;
        $INFO['id'] = 'test:plugin_multiselect:syntax';
        saveWikiText('test:plugin_multiselect:syntax','<multiselect a b c d>','test');
        
        $xhtml = p_wiki_xhtml('test:plugin_multiselect:syntax');

        $doc = phpQuery::newDocument($xhtml);
        
        $mselector = pq("span.multiselector",$doc);
        $this->assertTrue($mselector->length === 1);
        $this->assertTrue(pq("span",$mselector)->length === 4);
        $this->assertEquals('a',trim(pq("span",$mselector)->eq(0)->text()));
        $this->assertEquals('b',trim(pq("span",$mselector)->eq(1)->text()));
        $this->assertEquals('c',trim(pq("span",$mselector)->eq(2)->text()));
        $this->assertEquals('d',trim(pq("span",$mselector)->eq(3)->text()));
        
        $this->assertEquals('a',trim(pq("span.multismiley",$doc)->text()));
        
    }
    
    public function test_basic2_syntax() {
        global $INFO;
        $INFO['id'] = 'test:plugin_multiselect:syntax2';
        saveWikiText('test:plugin_multiselect:syntax2','<multiselect "a a" "üöäß" ä_ :-)>','test');
        
        $xhtml = p_wiki_xhtml('test:plugin_multiselect:syntax2');
        //echo $xhtml;
        $doc = phpQuery::newDocument($xhtml);
        
        $mselector = pq("span.multiselector",$doc);
        $this->assertTrue($mselector->length === 1);
        $this->assertTrue(pq("span",$mselector)->length === 4);
        $this->assertEquals('a a',trim(pq("span",$mselector)->eq(0)->text()));
        $this->assertEquals('üöäß',trim(pq("span",$mselector)->eq(1)->text()));
        $this->assertEquals('ä_',trim(pq("span",$mselector)->eq(2)->text()));
        $this->assertEquals(':-)',trim(pq('img',pq("span",$mselector)->eq(3))->attr('alt')));
        
        $this->assertEquals('a a',trim(pq("span.multismiley",$doc)->text()));
        
    }
}
