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
Add or include this in your `config.yml` parameters

```yaml
    sixpaths.twilio.username: <account sid>
    sixpaths.twilio.password: <auth token>
    sixpaths.twilio.spool.enabled: true
    sixpaths.twilio.spool.type: file
    sixpaths.twilio.spool.directory: '%kernel.root_dir%/../app/twilio/spool/'
    sixpaths.twilio.spool.retain: true

    sixpaths.twilio.from: '<phone number>'
    sixpaths.twilio.defaults.to: '<phone number>'
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
            '+441234567890', // Send a message to this number
            [
                'from' => '+449876543210', // Send the message from this number
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
            '+441234567890', // Send a message to this number
            [
                'from' => '+449876543210', // Send the message from this number,
                'body' => 'Message Body', // The message to send
            ]
        );
    }
}
```

## Commands

This bundle comes with two commands packaged.

```bash
sixpaths:twilio:spool:message:generate
```

This will allow you to generate messages - spooled or otherwise - to confirm settings. These will be sent to the default phone number (`sixpaths.twilio.defaults.to`)

```bash
sixpaths:twilio:spool:message:send
```

This will send any spooled messages and either remove or retain them.

