<?php

/*
 * This file is part of the SixpathsTwilioBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sixpaths\TwilioBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Sixpaths\TwilioBundle\DTO\TwilioMessageOptions;

/**
 * @ORM\MappedSuperclass
 */
abstract class TwilioMessage
{

    /**
     * @ORM\Column(name = "to", type = "string", length = 20, nullable = false)
     *
     * @var string
     */
    protected $to;

    /**
     * @ORM\Column(name = "options", type = "json", nullable = false)
     *
     * @var json
     */
    protected $options;

    /**
     * @ORM\Column(name = "is_processed", type = "boolean")
     *
     * @var bool
     */
    protected $isProcessed = false;

    /**
     * @param string $to
     * @param array $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        string $to,
        array $options
    )
    {
        // $this->validatePhoneNumber('to', $to);
        // $this->validatePhoneNumber('from', $from);
        // $this->validateMessage($message);

        $this->to = $to;
        $this->options = new TwilioMessageOptions($options);
    }

    /**
     * Gets the value of to.
     *
     * @return string
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * Gets the value of options.
     *
     * @return TwilioMessageOptions
     */
    public function getOptions(): TwilioMessageOptions
    {
        return $this->options;
    }

    /**
     * Gets the value of isProcessed.
     *
     * @return bool
     */
    public function isProcessed(): bool
    {
        return $this->isProcessed;
    }

    /**
     * Gets the value of isProcessed.
     *
     * @return bool
     */
    public function getIsProcessed(): bool
    {
        return $this->isProcessed;
    }

    /**
     * Sets the value of isProcessed.
     *
     * @param bool $isProcessed
     *
     * @return $this
     */
    public function setIsProcessed(bool $isProcessed): TwilioMessage
    {
        $this->isProcessed = $isProcessed;

        return $this;
    }

    /**
     * Sets the value of isProcessed to true.
     *
     * @return $this
     */
    public function process(): TwilioMessage
    {
        $this->isProcessed = true;

        return $this;
    }

    // /**
    //  * @param string $property
    //  * @param string $value
    //  *
    //  * @return void
    //  * @throws \InvalidArgumentException
    //  */
    // private function validatePhoneNumber(string $property, string $value): void
    // {
    //     if (strlen($value) === 0 ||
    //         strlen($value) > 15) {
    //         throw \InvalidArgumentException('`' . $property . '` must be between 1 and 15 characters.');
    //     }

    //     if (substr($value, 0, 1) !== '+') {
    //         throw \InvalidArgumentException('`' . $property . '` must be an E.164 formatted string.');
    //     }
    // }

    // /**
    //  * @param string $value
    //  *
    //  * @return void
    //  * @throws \InvalidArgumentException
    //  */
    // private function validateMessage(string $value): void
    // {
    //     if (strlen($message) === 0 ||
    //         strlen($message) > 1600) {
    //         throw \InvalidArgumentException('`message` must be between 1 and 1,600 characters in length.');
    //     }
    // }
}
