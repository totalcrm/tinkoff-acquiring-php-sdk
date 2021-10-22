<?php

namespace TotalCRM\TinkoffAcquiring\Core;

interface Parser
{
    /**
     * @param $raw
     * @return mixed
     */
    public function parse($raw);
}