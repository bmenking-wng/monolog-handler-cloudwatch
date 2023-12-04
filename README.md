
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
# AWS Permissions

This module requires the following permissions (IAM format):

```
{
	"Version": "2012-10-17",
	"Statement": [
		{
			"Sid": "VisualEditor0",
			"Effect": "Allow",
			"Action": [
				"logs:CreateLogStream",
				"logs:PutLogEvents"
			],
			"Resource": "arn:aws:logs:*:<org>:log-group:*:<log stream>"
		},
		{
			"Sid": "VisualEditor1",
			"Effect": "Allow",
			"Action": "logs:DescribeLogStreams",
			"Resource": "arn:aws:logs:*:<org>:log-group:*"
		}
	]
}
```

Be sure to adjust the permissions according to your environment's need and security policies.
    
## Usage/Examples

```php
<?php
require('./vendor/autoload.php');

use Monolog\Logger;
use Aws\CloudWatchLogs\CloudWatchLogsClient;
use WorldNewsGroup\Monolog\Handlers\CloudWatchLogHandler;

$client = new CloudWatchLogsClient([
    'region'=>'us-east-1',
    'version'=>'latest'
]);

$logger = new Logger('test');
$logger->pushHandler(new CloudWatchLogHandler($client, [
    'logGroupName'=>'my-log-group-name-that-already-exists'
    'logStreamName'=>'a-new-stream-name-or-an-existing-one'
]));

$logger->info("This is an informational message");

```

## Feedback

If you have any feedback, please reach out to us at world.developers@gwpub.com.

## License

[MIT](https://choosealicense.com/licenses/mit/)

## Running Tests

To run tests, run the following command

We use phpunit version 9.  Using version 10 may throw errors.

Copy env.example to .env and fill out any relevant information for your environment.

```bash
    composer update
    phpunit tests/*
```

### Attribution

README.md built at https://readme.so/
