<?php

/*
 * This file is part of the SixpathsTwilioBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sixpaths\TwilioBundle\Command\Message;

use Sixpaths\Components\ParameterBag;
use Sixpaths\TwilioBundle\Components\Message\Message;
use Sixpaths\TwilioBundle\Service\TwilioInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

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

        $this->finder->files()->in($this->parameters->getParameter('spool.directory'))->depth('== 0');

        foreach ($this->finder as $file) {
            $rawContent = $file->getContents();
            $message = unserialize($rawContent);

            $this->sendMessage($message);
            $this->retainFile($file);
        }
    }

    private function sendMessage(Message $message): void
    {
        $messages = $this->twilio->twilioClient->messages;
        $message = $messages->create(
            $message->getTo(),
            $message->getOptions()
        );
    }

    private function retainFile(SplFileInfo $file): void
    {
        $filesystem = new Filesystem;

        if ($this->parameters->getParameter('spool.retain')) {
            $filesystem->mkdir($file->getPath() . '/retained/');
            $filesystem->rename($file->getPathname(), $file->getPath() . '/retained/' . $file->getFilename());
            return;
        }

        $filesystem->remove([$file->getPathname()]);
        return;
    }
}
