<?php

/*
 * This file is part of the SixpathsTwilioBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sixpaths\TwilioBundle;

use Sixpaths\ComponentBundle\ParameterBag;
use Sixpaths\TwilioBundle\Components\Message\MessageList;
use Sixpaths\TwilioBundle\Components\Message\MessageListInterface;
use Twilio\Rest\Client as TwilioClient;

class Client implements TwilioInterface
{
    /**
     * @var ParameterBag
     */
    protected $parameters;

    /**
     * @var array
     */
    protected $messages;

    /**
     * @var TwilioClient
     */
    protected $twilioClient;

    const DISALLOWED_ACCESS = [
        'parameters',
    ];

    public function __construct(array $parameters)
    {
        $this->parameters = new ParameterBag($parameters);
        $this->twilioClient = new TwilioClient(
            $this->parameters->getParameter('username'),
            $this->parameters->getParameter('password')
        );
        $this->messages = new MessageList($parameters, $this->twilioClient);

    }

    /**
     * Gets the value of messages.
     *
     * @return MessageListInterface
     */
    public function getMessages(): MessageListInterface
    {
        return $this->messages;
    }

    /**
     * Gets the value of parameters
     *
     * @return ParameterBag
     */
    public function getParameters(): ParameterBag
    {
        return $this->parameters;
    }

    public function __get(string $property)
    {
        if (!in_array($property, self::DISALLOWED_ACCESS) &&
            property_exists($this, $property) &&
            method_exists($this, 'get' . ucwords(strtolower($property)))) {
            return $this->property;
        }

        throw new \InvalidArgumentException('Undefined property: ' . $property);
    }
}
