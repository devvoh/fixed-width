<?php
/**
 * @license     Unlicense <https://unlicense.org>
 * @author      Robin de Graaf <hello@devvoh.com>
 */

namespace Devvoh\FixedWidth;

class Reader
{
    /**
     * @var \Devvoh\FixedWidth\Schema
     */
    protected $schema;

    /**
     * @var string
     */
    protected $string;

    /**
     * @param \Devvoh\FixedWidth\Schema $schema
     * @return static
     */
    public function setSchema(\Devvoh\FixedWidth\Schema $schema)
    {
        $this->schema = $schema;
        return $this;
    }

    /**
     * @param string $string
     * @return array|null
     */
    public function readLine($string)
    {
        if ($this->schema->getTotalLineLength() !== strlen($string)) {
            return null;
        }

        $data = [];
        $lastPosition = 0;
        foreach ($this->schema->getFields() as $field) {
            $strPart = substr($string, $lastPosition, $field->getLength());

            if ($this->schema->getDelimiter() && strpos($strPart, $this->schema->getDelimiter()) !== false) {
                return null;
            }

            if ($field->getValidCharacters() && !in_array($strPart, $field->getValidCharacters())) {
                return null;
            }

            $data[$field->getKey()] = $strPart;
            $lastPosition += $field->getLength();

            if ($this->schema->getDelimiter()) {
                // Check if there's a valid delimiter at the start of the next string part
                $isDelimiter = substr($string, $lastPosition, strlen($this->schema->getDelimiter()));
                if ($isDelimiter !== $this->schema->getDelimiter()) {
                    return null;
                }
                $lastPosition += strlen($this->schema->getDelimiter());
            }
        }
        return $data;
    }

    /**
     * @param string $string
     * @return array
     */
    public function readLines($string)
    {
        $lines = explode(PHP_EOL, $string);
        $data = [];
        foreach ($lines as $line) {
            if (!empty($line)) {
                $data[] = $this->readLine($line);
            }
        }
        return $data;
    }
}
