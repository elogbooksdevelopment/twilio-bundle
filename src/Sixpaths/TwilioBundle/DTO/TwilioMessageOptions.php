<?php

/*
 * This file is part of the SixpathsTwilioBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sixpaths\TwilioBundle\DTO;

class TwilioMessageOptions
{
    /**
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $message;

    public function __construct(array $options)
    {
        foreach ($options as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function __toString(): string
    {
        return json_encode($this);
    }

    public function __get(string $property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        throw new \InvalidArgumentException('Undefined property: ' . $property);
    }
}
