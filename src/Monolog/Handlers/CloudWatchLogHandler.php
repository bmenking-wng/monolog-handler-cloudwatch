<?php

namespace WorldNewsGroup\Monolog\Handlers;

use Monolog\Level;
use Monolog\LogRecord;
use Monolog\Handler\AbstractHandler;
use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Aws\CloudWatchLogs\Exception\CloudWatchLogsException;

class CloudWatchLogHandler extends AbstractHandler {
    protected $client;
    protected array $options;
    protected $sequence_token;
    protected $lastException = null;

    /**
     * @param   string              $logGroupName existing log group name
     * @param   string              $logStreamName if null, will create log stream name in format YYYY/MM/DD[$LATEST]<hash>
     * @param   int|string          $level      
     * @param   boolean             $bubble
     */
    public function __construct(CloudWatchLogsClient $client, array $options = [], $level = Level::Debug, bool $bubble = true) {
        parent::__construct($level, $bubble);

        if( !isset($options['logGroupName'])) {
            throw new MissingLogGroupName();
        }

        $this->client = $client;
        $this->options = $options;

        if( !isset($this->options['logStreamName']) ) {
            $this->options['logStreamName'] = date('Y/m/d') . '[LATEST]' . md5($this->options['logGroupName'] . microtime(true));
        }

        try {
            $this->client->createLogStream([
                'logGroupName'=>$this->options['logGroupName'],
                'logStreamName'=>$this->options['logStreamName']
            ]);
        }
        catch(CloudWatchLogsException $e) {
            if( $e->getAwsErrorCode() === 'ResourceAlreadyExistsException') {
                $result = $this->client->describeLogStreams([
                    'logGroupName' => $this->options['logGroupName']
                ]);
                $this->sequence_token = $result['nextToken'];
            }
            else {
                throw $e;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function handle(LogRecord $record): bool {
        if( strlen($record['message']) == 0 ) return false;
        
        $params = [
            'logEvents'=>[
                [
                    'message'=>$record['message'],
                    'timestamp'=>round(microtime(true) * 1000)
                ]
            ],
            'logGroupName'=>$this->options['logGroupName'],
            'logStreamName'=>$this->options['logStreamName']
        ];

        if( !is_null($this->sequence_token) ) $params['sequenceToken'] = $this->sequence_token;

        try {
            $result = $this->client->putLogEvents($params);
            $this->sequence_token = $result['nextSequenceToken'];            
        }
        catch(CloudWatchLogsException $e) {
            if( $e->getAwsErrorCode() === 'InvalidSequenceTokenException' || $e->getAwsErrorCode() === 'DataAlreadyAcceptedException') {
                $params['sequenceToken'] = $e['expectedSequenceToken'];
                $result = $this->client->putLogEvents($params);
                $this->sequence_token = $result['nextSequenceToken'];   
            }
            else {
                $this->lastException = $e;
                return false;
            }
        }

        $this->lastException = null;
        return true;
    }
   
}