<?php declare(strict_types=1);

require('./vendor/autoload.php');

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Aws\CloudWatchLogs\Exception\CloudWatchLogsException;
use PHPUnit\Framework\TestCase;
use Monolog\Logger;
use WorldNewsGroup\Monolog\Handlers\CloudWatchLogHandler;
use WorldNewsGroup\Monolog\Handlers\MissingLogGroupName;
use Dotenv\Dotenv;

final class CloudwatchHandlerTest extends TestCase {

    public static function setUpBeforeClass(): void {
        Dotenv::createMutable('.')->load();
    }

    public function testSendDebug(): void {
        $client = new CloudWatchLogsClient([
            'region'=>'us-east-1',
            'version'=>'latest'
        ]);

        $logger = new Logger('test');
        $logger->pushHandler(new CloudWatchLogHandler($client, ['logGroupName'=>$_ENV['AWS_CW_LOG_GROUPNAME']]));

        $result = $logger->debug("This is a debug message");

        $this->assertNotFalse($result);
    }

    public function testGroupNameNotGiven(): void {
        $client = new CloudWatchLogsClient([
            'region'=>'us-east-1',
            'version'=>'latest'
        ]);

        $logger = new Logger('test');

        $this->expectException(MissingLogGroupName::class);

        $logger->pushHandler(new CloudWatchLogHandler($client));

        $result = $logger->debug("We should not get here!");

        $this->assertNotFalse($result);    
    }

    public function testNonexistantLogGroupName(): void {
        $client = new CloudWatchLogsClient([
            'region'=>'us-east-1',
            'version'=>'latest'
        ]);

        $logger = new Logger('test');

        $this->expectException(CloudWatchLogsException::class);

        $logger->pushHandler(new CloudWatchLogHandler($client, [
            'logGroupName'=>'thisbetternotExistanywhere'
        ]));

        $result = $logger->debug("We should not get here!");

        $this->assertNotFalse($result);    
    }
}