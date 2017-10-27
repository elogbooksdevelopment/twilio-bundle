<?php

/*
 * This file is part of the SixpathsTwilioBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sixpaths\TwilioBundle\Components\Message;

class Message implements MessageInterface
{
    /**
     * @var string
     */
    protected $to;

    /**
     * @var array
     */
    protected $options;

    public function __construct(string $to, array $options)
    {
        $this->to = $to;
        $this->options = $options;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
