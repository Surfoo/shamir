<?php

namespace TQ\Shamir\Random;

use Exception;
use OutOfRangeException;
use RuntimeException;

/**
 * Class PhpGenerator
 *
 * @package TQ\Shamir\Random
 */
class PhpGenerator implements Generator
{
    /**
     * The maximum random number
     *
     * @var int
     */
    protected $max = PHP_INT_MAX;

    /**
     * The minimum random number
     *
     * @var int
     */
    protected $min = 1;

    /**
     * @param  int|float  $max  The maximum random number
     * @param  int|float  $min  The minimum random number (must be positive)
     */
    public function __construct(int|float $max = PHP_INT_MAX, int|float $min = 1)
    {
        $min = (int)$min;
        $max = (int)$max;

        if ($min < 1) {
            throw new OutOfRangeException('The min number must be a positive integer.');
        }

        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @inheritdoc
     */
    public function getRandomInt()
    {
        try {
            $random = random_int($this->min, $this->max);
        } catch (Exception $e) {
            throw new RuntimeException(
                'Random number generator algorithm failed.', 0, $e
            );
        }

        return $random;
    }
}
