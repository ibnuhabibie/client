<?php

namespace Laracatch\Client\Storage;

class Storage
{
    /** @var int */
    protected $retention;

    public function __construct()
    {
        $this->retention = config()->get('laracatch.storage.retention', 24);
    }

    /**
     * @return string
     */
    protected function generateIdentifier(): string
    {
        // http://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid
        // by Andrew Moore (http://www.php.net/manual/en/function.uniqid.php#94959)
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
