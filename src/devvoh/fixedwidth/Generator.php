<?php
/**
 * @license     Unlicense <https://unlicense.org>
 * @author      Robin de Graaf <hello@devvoh.com>
 */

namespace Devvoh\FixedWidth;

class Generator
{
    /**
     * @var \Devvoh\FixedWidth\Schema
     */
    protected $schema;

    /**
     * @var array
     */
    protected $data;

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
     * @param array $dataItem
     * @return static
     */
    public function addData(array $dataItem)
    {
        $this->data[] = $dataItem;
        return $this;
    }

    /**
     * @param array $data
     * @return static
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return $this
     */
    public function clearData()
    {
        $this->data = null;
        return $this;
    }

    /**
     * @param bool $trim
     * @return string
     */
    public function asString($trim = false)
    {
        $string = '';
        foreach ($this->asArray($trim) as $dataItem) {
            $string .= implode($this->schema->getDelimiter(), $dataItem) . $this->schema->getDelimiter() . PHP_EOL;
        }
        return rtrim($string, PHP_EOL);
    }

    /**
     * @param bool $trim
     * @return string
     */
    public function asJson($trim = false)
    {
        return json_encode($this->asArray($trim), JSON_PRETTY_PRINT);
    }

    /**
     * @param bool $trim
     * @return array
     */
    public function asArray($trim = false)
    {
        $dataItems = [];
        foreach ($this->data as $dataItem) {
            foreach ($this->schema->getFields() as $field) {
                /** @var \Devvoh\FixedWidth\Schema\Field $field */
                if (isset($dataItem[$field->getKey()])) {
                    $value = $dataItem[$field->getKey()];

                    if ($field->getValidCharacters() && !in_array($value, $field->getValidCharacters())) {
                        $dataItem = ['-- invalid data: ' . implode(', ', $dataItem)];
                        break;
                    }

                    // Check and force length
                    if (strlen($value) > $field->getLength()) {
                        $value = substr($value, 0, $field->getLength());
                    }

                    // Pad the string to the required length if need be
                    $value = str_pad($value, $field->getLength(), $field->getPadCharacter(), $field->getPadPlacement());

                    $callback = $field->getCallback();
                    // Check if there's a callable callback and apply it if there is
                    if ($callback && is_callable($callback)) {
                        $value = $callback($value);
                    }

                    // Trim if asked to do so
                    if ($trim) {
                        if ($field->getPadPlacement() == STR_PAD_LEFT) {
                            $value = ltrim($value, $field->getPadCharacter());
                        } elseif ($field->getPadPlacement() == STR_PAD_RIGHT) {
                            $value = rtrim($value, $field->getPadCharacter());
                        } elseif ($field->getPadPlacement() == STR_PAD_BOTH) {
                            $value = trim($value, $field->getPadCharacter());
                        }
                    }
                    $dataItem[$field->getKey()] = $value;
                } else {
                    $dataItem = ['-- invalid data: ' . implode(', ', $dataItem)];
                    break;
                }
            }
            $dataItems[] = $dataItem;
        }
        return $dataItems;
    }
}
