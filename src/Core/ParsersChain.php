<?php

namespace TotalCRM\TinkoffAcquiring\Core;

class ParsersChain implements Parser
{

    private array $parsers;

    /**
     * ParsersChain constructor.
     * @param Parser ...$parsers
     */
    public function __construct(Parser ...$parsers)
    {
        $this->parsers = $parsers;
    }

    public function __destruct()
    {
        foreach ($this->parsers as $key => $value) {
            unset($this->parsers[$key]);
        }
    }

    /**
     * @param $raw
     * @return mixed
     */
    public function parse($raw)
    {
        foreach ($this->parsers as $parser) {
            $raw = $parser->parse($raw);
        }

        return $raw;
    }

}