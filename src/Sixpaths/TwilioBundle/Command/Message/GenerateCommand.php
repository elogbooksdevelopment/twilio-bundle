<?php

/*
 * This file is part of the SixpathsTwilioBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sixpaths\TwilioBundle\Command\Message;

use Sixpaths\Components\ParameterBag;
use Sixpaths\TwilioBundle\Client;
use Sixpaths\TwilioBundle\Components\Message;
use Sixpaths\TwilioBundle\Service\TwilioInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

class GenerateCommand extends ContainerAwareCommand
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
     * @var ParameterBag
     */
    protected $parameters;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('sixpaths:twilio:spool:message:generate')
            ->setDescription('Generates messages.')
            ->setDefinition([
                new InputOption('username', null, InputOption::VALUE_OPTIONAL, 'The Twilio username (account sid) to use.'),
                new InputOption('password', null, InputOption::VALUE_OPTIONAL, 'The Twilio password (auth token) to use.'),
                new InputOption('spool', null, InputOption::VALUE_OPTIONAL, 'Whether or not to spool.'),
                new InputOption('spoolType', 'file', InputOption::VALUE_OPTIONAL, 'The spool type (if spooling).'),
                new InputOption('spoolDirectory', null, InputOption::VALUE_OPTIONAL, 'The spool directory (if spoolType is file).'),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->container = $this->getContainer();
        $this->questionHelper = $this->getHelper('question');

        $this->twilio = $this->container->get('sixpaths.twilio');

        $parameters = $this->getArguments();
        $this->parameters = new ParameterBag($parameters);

        $client = new Client($parameters);
        $messages = $client->messages;
        $message = $messages->create($this->parameters->getParameter('defaults.to'), ['from' => $this->parameters->getParameter('from'), 'body' => 'Test SMS ' . mt_rand()]);
    }

    private function getArguments(): array
    {
        $parameters = [
            'username' => $this->container->getParameter('sixpaths.twilio.username'),
            'password' => $this->container->getParameter('sixpaths.twilio.password'),
            'from' => $this->container->getParameter('sixpaths.twilio.from'),
            'defaults' => [
                'to' => $this->container->getParameter('sixpaths.twilio.defaults.to'),
            ],
            'spool' => false
        ];

        if (($spool = $parameters['spool']['enabled'] = $this->validateSpool())) {
            $parameters['spool']['type'] = $this->validateSpoolType($parameters['spool']['enabled']);
            $parameters['spool']['directory'] = $this->validateSpoolDirectory($parameters['spool']['type']);
        } else {
            // $parameters['username'] = $this->validateUsername();
            // $parameters['password'] = $this->validatePassword();
        }

        return $parameters;
    }

    private function validateUsername(): string
    {
        $value = $this->input->getOption('username');
        if ($value === null) {
            $value = $this->ask($this->getStringQuestion('Please enter the username (account sid) to use'));
        }

        return $value;
    }

    private function validatePassword(): string
    {
        $value = $this->input->getOption('password');
        if ($value === null) {
            $value = $this->ask($this->getStringQuestion('Please enter the password (auth token) to use'));
        }

        return $value;
    }

    private function validateSpool(): bool
    {
        $value = $this->input->getOption('spool');
        if ($value === null) {
            $value = $this->ask($this->getBooleanQuestion('Is spooling enabled?'));
        }

        return $value;
    }

    private function validateSpoolType(bool $spool): ?string
    {
        if ($spool) {
            $value = $this->input->getOption('spoolType');
            if ($value === null) {
                $value = $this->ask($this->getStringQuestion('Which spool method to use (file)'), ['file']);
            }

            return $value;
        }

        return null;
    }

    private function validateSpoolDirectory(string $spoolDirectory): ?string
    {
        $value = $this->input->getOption('spoolDirectory');
        if ($value === null) {
            $defaultDirectory = $this->container->getParameter('kernel.root_dir') . '/../app/twilio/spool/';

            $value = $this->ask($this->getStringQuestion('Which spool directory to use (' . $defaultDirectory . ')', [$defaultDirectory]));
        }

        return $value;
    }

    private function getStringQuestion(string $questionText, array $validOptions = []): Question\Question
    {
        $question = $this->getQuestion($questionText);
        $question->setValidator(function ($value) {
            if (empty($value)) {
                throw new \Exception('You must enter a value.');
            } else if (!empty($validOptions) && !in_array($value, $validOptions)) {
                throw new \InvalidArgumentException('You must enter a valid option from [' . implode(', ', $validOptions) . ']');
            }

            return $value;
        });

        return $question;
    }

    private function getBooleanQuestion(string $questionText): Question\Question
    {
        $question = $this->getQuestion($questionText);
        $question->setValidator(function ($value) {
            if (!in_array($value, [true, false, 'true', 'false'], true)) {
                throw new \Exception('You must enter either true or false.');
            }

            return in_array($value, [true, 'true'], true);
        });

        return $question;
    }

    private function getQuestion(string $questionText): Question\Question
    {
        $question = new Question\Question($questionText, null);
        $question->setMaxAttempts(2);

        return $question;
    }

    private function ask($question)
    {
        return $this->questionHelper->ask($this->input, $this->output, $question);
    }
}
