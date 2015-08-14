<?php

use Dormilich\Core\ArrayObject    as XArray;
use Dormilich\Core\ArrayInterface as XAInterface;

class HistoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException UnderflowException
     */
    public function testCallOnEmptyHistoryFails()
    {
        $xao = new XArray;
        $xao->back();
    }

    public function testGetPreviousObject()
    {
        $xao = new XArray([1,2,3]);
        $obj = $xao->map(function ($value) {
            return 2 * $value;
        });
        $this->assertSame($xao, $obj->back());
    }
}
