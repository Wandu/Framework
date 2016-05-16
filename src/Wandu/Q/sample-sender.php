<?php
use Pheanstalk\Pheanstalk;
use Wandu\Q\Adapter\BeanstalkdAdapter;
use Wandu\Q\Adapter\SqsAdapter;
use Wandu\Q\Queue;
use Wandu\Q\Serializer\JsonSerializer;

require __DIR__ . '/../../../vendor/autoload.php';

// SQS Example
//$sender = new Queue(new JsonSerializer(), new SqsAdapter(
//    'AKIAICPQCHQBUDOUZ5FQ',
//    'LVnCpV03Np3mI2h1k9RELuJCZ7FgeSyKZLcZYU4U',
//    'ap-northeast-1',
//    'https://sqs.ap-northeast-1.amazonaws.com/205122141336/gs-queue'
//));
//$sender->enqueue([
//    'body' => 'kkk',
//    '333' => 'halelleknflaksdf',
//]);

$sender = new Queue(
    new JsonSerializer(),
    new BeanstalkdAdapter(new Pheanstalk('127.0.0.1'))
);
$sender->enqueue("???");
