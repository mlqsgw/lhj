<?php
require('carrot.union.php');

class MyUnion extends CarrotUnion
{
    protected function _init()
    {
        $this->_union_id = CARROT_UNION_ID;
        $this->_union_key = CARROT_UNION_KEY;
    }
}