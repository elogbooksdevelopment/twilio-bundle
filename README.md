# Twilio Bundle

## Installation

### Manual Installation
Add this to your composer.json
```json
"require": {
    "six-paths/twilio-bundle": "dev-master"
}
```

### Automated Installation
composer require six-paths/twilio-bundle@dev-master

Activate the bundle in `app/AppKernel.php`

```php
$bundles = array(
    // ...
    new Sixpaths\TwilioBundle\SixpathsTwilioBundle(),
);
```

## Configuration
Add or include this in your `config.yml`

```yaml
sixpaths:
    twilio:
        # Required: Account SID
        username: ~
        # Required: Auth Token
        password: ~

        # Optional: Spool
        spool:
            # Optional: Whether or not to spool messages or send immediately
            enabled: false
            # Optional: Which spool type to use [file|memory]
            type: file
            # Optional: Spool directory
            directory: "%kernel.root_dir%/../app/twilio/spool/"
            # Optional: Retain after sending
            retain: false
```

## Usage

It is advised to inject this service in to listeners or other services that may need to consume it rather than using $this->get('...'); instead of a controller; however, that option is still available

As part of a controller
```php
class SomeController extends Controller
{
    public function someAction(/* ...$arguments */)
    {
        $twilio = $this->get('sixpaths.twilio');
        $messages = $twilio->messages;

        $message = $messages->create(
            '01234567890', // Send a message to this number
            [
                'from' => 09876543210', // Send the message from this number
                'body' => 'Message Body', // The message to send
            ]
        );

    }
}
```

As part of a console command
```php
class SomeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('some:command')
            ->setDescription('A command');
    }

    protected function execute(InterInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $twilio = $container->get('sixpaths.twilio');
        $messages = $twilio->messages;

        $message = $messages->create(
            '01234567890', // Send a message to this number
            [
                'from' => '09876543210', // Send the message from this number,
                'body' => 'Message Body', // The message to send
            ]
        );
    }
}
```
