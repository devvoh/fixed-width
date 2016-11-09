<?php
/**
 * @package     FixedWidth
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\FixedWidth\Schema;

class Field
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var int
     */
    protected $length;

    /**
     * @var string
     */
    protected $padCharacter = ' ';

    /**
     * @var int
     */
    protected $padPlacement = STR_PAD_RIGHT;

    /**
     * @var callable|null
     */
    protected $callback;

    /**
     * @var array|null
     */
    protected $validCharacters;

    /**
     * @var array
     */
    protected $allowedPlacements = [STR_PAD_LEFT, STR_PAD_RIGHT, STR_PAD_BOTH];

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param int $length
     * @return $this
     */
    public function setLength($length)
    {
        $this->length = (int)$length;
        return $this;
    }

    /**
     * @return string
     */
    public function getPadCharacter()
    {
        return $this->padCharacter;
    }

    /**
     * @param string $padCharacter
     * @return $this
     */
    public function setPadCharacter($padCharacter)
    {
        $this->padCharacter = (string)$padCharacter;
        return $this;
    }

    /**
     * @return int
     */
    public function getPadPlacement()
    {
        return $this->padPlacement;
    }

    /**
     * @param int $padPlacement
     * @return $this
     */
    public function setPadPlacement($padPlacement)
    {
        if (in_array($padPlacement, $this->allowedPlacements)) {
            $this->padPlacement = $padPlacement;
        }
        return $this;
    }

    /**
     * @return callable|null
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param callable|null $callback
     * @return $this
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getValidCharacters()
    {
        return $this->validCharacters;
    }

    /**
     * @param array|null $validCharacters
     * @return $this
     */
    public function setValidCharacters($validCharacters)
    {
        $this->validCharacters = $validCharacters;
        return $this;
    }

}
