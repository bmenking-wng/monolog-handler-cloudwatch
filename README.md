
# Project Title

A Monolog handler for sending messages to AWS CloudWatchLogs.




## Installation

Install monolog-handler-cloudwatch with composer

Edit composer.json, add or edit the following section:

```
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/World-News-Group/monolog-handler-cloudwatch"
        },
        ...
    ]
```

and install with:

```bash
  composer require worldnewsgroup/monolog-handler-cloudwatch
```

    
## License

[MIT](https://choosealicense.com/licenses/mit/)


## Usage/Examples

```php
<?php
require('./vendor/autoload.php');

use Monolog\Logger;
use Aws\CloudWatchLogs\CloudWatchLogsClient;

$client = new CloudWatchLogsClient([
    'region'=>'us-east-1',
    'version'=>'latest'
]);

$logger = new Logger('test');
$logger->pushHandler(new CloudWatchLogHandler($client, [
    'logGroupName'=>'my-log-group-name-that-already-exists'
]));

$logger->info("This is an informational message");

```


## Feedback

If you have any feedback, please reach out to us at world.developers@gwpub.com.


## Running Tests

To run tests, run the following command

We use phpunit version 9.  Using version 10 may throw errors.

Copy env.example to .env and fill out any relevant information for your environment.

```bash
    composer update
    phpunit tests/*
```

