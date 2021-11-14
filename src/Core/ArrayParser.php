<?php

namespace TotalCRM\TinkoffAcquiring\Core;

use Exception;
use RuntimeException;

class ArrayParser implements Parser
{

    private Parser $itemParser;

    /**
     * ArrayParser constructor.
     * @param Parser $itemParser
     */
    public function __construct(Parser $itemParser)
    {
        $this->itemParser = $itemParser;
    }

    /**
     * @param array|null $raw
     * @return array
     * @throws Exception
     */
    public function parse($raw): ?array
    {
        $result = [];

        if (!is_array($raw)) {
            throw new RuntimeException('wrong array: ' . json_encode($raw, JSON_THROW_ON_ERROR));
        }

        foreach ($raw as $item) {
            $result[] = $this->itemParser->parse($item);
        }

        return $result;
    }

}