<?php

/*
 * This file is part of the SixpathsTwilioBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sixpaths\TwilioBundle\Command\Message;

use Sixpaths\ComponentBundle\ParameterBag;
use Sixpaths\TwilioBundle\Components\Message;
use Sixpaths\TwilioBundle\Model\TwilioMessage;
use Sixpaths\TwilioBundle\Service\TwilioInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

class SendCommand extends ContainerAwareCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var TwilioInterface
     */
    protected $twilio;

    /**
     * @var Finder
     */
    protected $finder;

    /**
     * @var ParameterBag
     */
    protected $parameters;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('sixpaths:twilio:spool:message:send')
            ->setDescription('Sends spooled messages.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container = $this->getContainer();

        $this->twilio = $this->container->get('sixpaths.twilio');
        $this->parameters = $this->twilio->getParameters();
        $this->finder = new Finder;

        if ($this->parameters->getParameter('spool.enabled')) {
            $this->processFileSpool();
        }
    }

    private function processFileSpool(): void
    {
        if ($this->parameters->getParameter('spool.type') !== 'file') {
            return;
        }

        $this->finder->files()->in($this->parameters->getParameter('spool.directory'));

        foreach ($finder as $file) {
            $rawContent = $file->getContent();
            $decodedContent = json_decode($rawContent);

            $message = new Message(
                $decodedContent->to,
                $decodedContent->options
            );
        }
    }

    private function sendMessage(Message $message): bool
    {
        $messages = $this->twilio->twilioClient->messages;
        $message = $messages->create(
            $message->getTo(),
            $message->getOptions()
        );
    }
}
