<?php

namespace TotalCRM\TinkoffAcquiring\Core\Http;

class Headers
{

    public array $valuesByName = [];

    /**
     * @param Header $header
     */
    public function add(Header $header): void
    {
        $values = $this->valuesByName[$header->getName()] ?? [];
        $values[] = $header->getValue();

        $this->valuesByName[$header->getName()] = $values;
    }

    /**
     * @param Header $header
     */
    public function set(Header $header): void
    {
        $this->valuesByName[$header->getName()] = [$header->getValue()];
    }

    /**
     * @param string $name
     */
    public function del(string $name): void
    {
        $name = (new Header($name))->getName();
        unset($this->valuesByName[$name]);
    }

    /**
     * @param string $name
     * @return array|null
     */
    public function get(string $name): ?array
    {
        $name = (new Header($name))->getName();

        return $this->valuesByName[$name] ?? null;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->valuesByName;
    }

    /**
     * @return array
     */
    public function getAllStrings(): array
    {
        $headers = [];

        foreach ($this->valuesByName as $name => $values) {
            foreach ($values as $value) {
                $headers[] = "$name: $value";
            }
        }

        return $headers;
    }

}