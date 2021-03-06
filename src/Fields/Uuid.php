<?php
/**
 * Define an uuid field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Ramsey\Uuid\Uuid as UuidGenerator;
use Ramsey\Uuid\Lazy\LazyUuidFromString;
use Ramsey\Uuid\Exception\InvalidUuidStringException;

class Uuid extends BaseAttribute
{
    /**
     * Version used for generation.
     *
     * @var integer
     */
    protected $version;

    /**
     * Version 1 (time-based) UUID object constant identifier.
     *
     * @var integer
     */
    const VERSION_1 = UuidGenerator::UUID_TYPE_TIME;

    /**
     * Version 3 (name-based and hashed with MD5) UUID object constant identifier.
     *
     * @var integer
     */
    const VERSION_3 = UuidGenerator::UUID_TYPE_HASH_MD5;

    /**
     * Version 4 (random) UUID object constant identifier.
     *
     * @var integer
     */
    const VERSION_4 = UuidGenerator::UUID_TYPE_RANDOM;

    /**
     * Version 5 (name-based and hashed with SHA1) UUID object constant identifier.
     *
     * @var integer
     */
    const VERSION_5 = UuidGenerator::UUID_TYPE_HASH_SHA1;

    /**
     * Dry the value in a simple format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function dry($value)
    {
        if ($value instanceof UuidGenerator) {
            return $value->getBytes();
        }

        return $value;
    }

    /**
     * Hydrate the value in the correct format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function hydrate($value)
    {
        if (\is_null($value)) {
            return $value;
        }

        if (\is_string($value) || $value instanceof LazyUuidFromString) {
            try {
                return UuidGenerator::fromString($value);
            } catch (InvalidUuidStringException $_) {
                return UuidGenerator::fromBytes($value);
            }
        }

        throw new \Exception('The given value is not an uuid');
    }

    /**
     * Cast the value in the correct format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function cast($value)
    {
        if (\is_null($value) || ($value instanceof UuidGenerator)) {
            return $value;
        }

        if (\is_string($value) || $value instanceof LazyUuidFromString) {
            try {
                return UuidGenerator::fromString($value);
            } catch (InvalidUuidStringException $_) {
                return UuidGenerator::fromBytes($value);
            }
        }

        throw new \Exception('The given value is not an uuid');
    }

    /**
     * Serialize the value for outputs.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function serialize($value)
    {
        return (string) $value;
    }

    /**
     * Return a new generated uuid.
     *
     * @return \Ramsey\Uuid\Lazy\LazyUuidFromString
     */
    public function generate(): LazyUuidFromString
    {
        return $this->cast(UuidGenerator::{'uuid'.$this->version}(...($this->factoryParameters ?: [])));
    }

    /**
     * Define default value as auto generated.
     *
     * @return self
     */
    public function autoGenerate()
    {
        $this->default([$this, 'generate']);

        return $this;
    }
}
