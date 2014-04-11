<?php

namespace Siriru\GSBundle\Form\DataTransformer;

use Siriru\GSBundle\Utility;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class GSTimeToNumberTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an number to a string
     *
     * @param  float $time
     * @return string
     */
    public function transform($time)
    {
        if (null === $time) {
            return "00:00:00";
        }

        return Utility::separateFloat($time);
    }

    /**
     * Transforms a string to an number
     *
     * @param  string $number
     * @return string
     * @throws TransformationFailedException if object is not found.
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        return $this->createFloat($number);
    }

    private function createFloat($string)
    {
        $parts        = explode(':', $string);
        $to_float     = ($parts[0]*60 + $parts[1]).'.'.$parts[2];

        return floatval($to_float);
    }
}