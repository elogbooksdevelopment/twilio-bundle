<?php

/*
 * This file is part of the SixpathsTwilioBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Sixpaths\TwilioBundle\Components\Message;

use Sixpaths\ComponentBundle\ParameterBag;
use Symfony\Component\Filesystem\Filesystem;
use Twilio\Rest\Client as TwilioClient;

class MessageList implements MessageListInterface
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

    public function __construct(array $parameters, TwilioClient $twilioClient)
    {
        $this->parameters = new ParameterBag($parameters);
        $this->messages = [];
        $this->twilioClient = $twilioClient;
    }

    /**
     * @return MessageInterface
     */
    public function create(
        string $to,
        array $options
    ): void
    {
        if ($this->parameters->getParameter('spool.enabled')) {
            $message = new Message($to, $options);
            $spoolType = $this->parameters->getParameter('spool.type');

            switch ($spoolType) {
                case 'file':
                    $this->spoolToFile($message);
                    break;
            }

            return;
        }

        $this->twilioClient->messages->create($to, $options);
    }

    protected function spoolToFile(Message $message): void
    {
        $filesystem = new Filesystem;

        $fileName = uniqid($message->getTo(), true);
        $filePath = $this->parameters->getParameter('spool.directory');

        $filesystem->dumpFile(rtrim($filePath, '/') . '/' . $fileName, json_encode($message));
    }
}
