<?php
class ToIdTest extends PHPUnit_Framework_TestCase
{
    public function testToId()
    {
        $this->assertEquals('foo', toId('Foo'));
        $this->assertEquals('foobar', toId(' Foo Bar    '));
        $this->assertEquals('paodeacucar', toId('Pão de Açucar'));
        $this->assertEquals('paodeacucar', toId('Pão de Açucar!@$%*'));
        $this->assertEquals('sandyejunior', toId('Sandy&Junior'));
        $this->assertEquals('sandyejunior', toId('Sandy & Junior'));
        $this->assertEquals('teste', toId('♥teste'));
    }
    
    public function testToSlug()
    {
        $this->assertEquals('foo', toSlug('Foo'));
        $this->assertEquals('foo-bar', toSlug(' Foo  Bar   '));
        $this->assertEquals('pao-de-acucar', toSlug('Pão de Açucar'));
        $this->assertEquals('pao-de-acucar', toSlug('Pão de Açucar!@$%*'));
        $this->assertEquals('sandyejunior', toSlug('Sandy&Junior'));
        $this->assertEquals('sandy-e-junior', toSlug('Sandy & Junior'));
        $this->assertEquals('teste', toSlug('♥teste'));
        $this->assertEquals('teste', toSlug('♥teste£™¢'));
        $this->assertEquals('foo-bar', toSlug('Foo/Bar'));
        $this->assertEquals('foo-bar', toSlug('Foo-----Bar'));
        $this->assertEquals('foo-bar', toSlug('Foo-/--/--Bar     '));
        $this->assertEquals('anu-ncio', toSlug('Anu´ncio'));
        $this->assertEquals('anuncio', toSlug('Anúncio'));
        // Looks like an accute, but its not
        $this->assertEquals('anu-ncio', toSlug('Anúncio'));
    }
}
