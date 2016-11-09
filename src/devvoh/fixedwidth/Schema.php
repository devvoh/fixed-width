<?php
/**
 * @package     FixedWidth
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\FixedWidth;

class Schema
{
    /**
     * @var string
     */
    protected $delimiter;

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @param $delimiter
     * @return $this
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
        return $this;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * @param string        $key
     * @param int           $length
     * @param string        $padCharacter
     * @param int           $padPlacement
     * @param null|callable $callback
     * @param null|array    $validCharacters
     * @return static
     */
    public function setField(
        $key,
        $length,
        $padCharacter = ' ',
        $padPlacement = STR_PAD_RIGHT,
        $callback = null,
        $validCharacters = null
    ) {
        $field = new \Devvoh\FixedWidth\Schema\Field();
        $field->setKey($key);
        $field->setLength($length);
        $field->setPadCharacter($padCharacter);
        $field->setPadPlacement($padPlacement);
        $field->setCallback($callback);
        $field->setValidCharacters($validCharacters);

        $this->fields[$key] = $field;
        return $this;
    }

    /**
     * @param array $fields
     * @return static
     */
    public function setFields(array $fields)
    {
        foreach ($fields as $field) {
            if (isset($field['type']) && $field['type'] == 'numeric') {
                $field['padCharacter'] = '0';
                $field['padPlacement'] = STR_PAD_LEFT;
            }

            $this->setField(
                $field['key'],
                $field['length'],
                isset($field['padCharacter']) ? $field['padCharacter'] : ' ',
                isset($field['padPlacement']) ? $field['padPlacement'] : STR_PAD_RIGHT,
                isset($field['callback']) ? $field['callback'] : null,
                isset($field['validCharacters']) ? $field['validCharacters'] : null
            );
        }
        return $this;
    }

    /**
     * @param string $key
     * @return array|null
     */
    public function getField($key)
    {
        if (isset($this->fields[$key])) {
            return $this->fields[$key];
        }
        return null;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return int
     */
    public function getTotalLineLength()
    {
        $length = strlen($this->getDelimiter()) * count($this->getFields());
        foreach ($this->getFields() as $field) {
            $length += $field->getLength();
        }
        return $length;
    }

    /**
     * @param $json
     * @return Schema
     */
    public static function createFromJson($json)
    {
        $array = json_decode($json, true);
        return static::createFromArray($array);
    }

    /**
     * @param $array
     * @return static
     */
    public static function createFromArray($array)
    {
        $schema = new static();

        $schemaData = [];

        foreach ($array as $item) {
            foreach ($item as $key => $value) {
                $existingValueLength = isset($schemaData[$key]) ? $schemaData[$key]['length'] : 0;
                $currentValueLength  = strlen((string)$value);

                $schemaData[$key] = [
                    'key'          => $key,
                    'length'       => ($currentValueLength > $existingValueLength) ? $currentValueLength : $existingValueLength,
                    'padCharacter' => ' ',
                    'padPlacement' => STR_PAD_RIGHT,
                    'callback'     => null,
                ];
            }
        }

        $schema->setFields($schemaData);
        return $schema;
    }
}
