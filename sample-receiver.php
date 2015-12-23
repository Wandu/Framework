<?php
use Wandu\Queue\Adapter\SqsAdapter;
use Wandu\Queue\Queue;
use Wandu\Queue\Serializer\JsonSerializer;

require __DIR__ . '/vendor/autoload.php';

$sender = new Queue(new JsonSerializer(), new SqsAdapter(
    'AKIAICPQCHQBUDOUZ5FQ',
    'LVnCpV03Np3mI2h1k9RELuJCZ7FgeSyKZLcZYU4U',
    'ap-northeast-1',
    'https://sqs.ap-northeast-1.amazonaws.com/205122141336/gs-queue'
));

while (true) {
    $job = $sender->dequeue();
    if (isset($job)) {
        echo "Receive!\n";
        print_r($job->read());
        echo "\n";
        $job->delete();
    }
    sleep(3);
}
