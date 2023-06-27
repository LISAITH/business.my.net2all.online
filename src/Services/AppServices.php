<?php

namespace App\Services;

class AppServices
{
    public function getBpayServerAddress()
    {
        $bpayServerAddress = $_ENV['bpayLink'];
        return $bpayServerAddress;
    }
}