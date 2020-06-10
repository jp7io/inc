<?php

class EncryptTest extends PHPUnit\Framework\TestCase
{
    public function test()
    {
        $data = [];
        foreach (range(1,5) as $_) {
            $data[] = mt_rand();
        }
        $encrypted = jp7_encrypt(implode('&', $data));
        $this->assertEquals($data, explode('&', jp7_decrypt($encrypted)));
    }
}
