Wandu Queue
===

Very Simple Queue.

---

## Example

**Sender**

```php
use Wandu\Queue\Adapter\SqsAdapter;
use Wandu\Queue\Queue;
use Wandu\Queue\Serializer\JsonSerializer;

$sender = new Queue(new JsonSerializer(), new SqsAdapter(
    'xxxxxxxxxxxx', // key
    'xxxxxxxxxxxx', // secret
    'ap-northeast-1', // region
    'https://sqs.ap-northeast-1.amazonaws.com/000000000000/queue-name' // queue url
));
$sender->enqueue([
    'body' => 'kkk',
    '333' => 'halelleknflaksdf',
]);
```

**Receiver**

```php
use Wandu\Queue\Adapter\SqsAdapter;
use Wandu\Queue\Queue;
use Wandu\Queue\Serializer\JsonSerializer;

$sender = new Queue(new JsonSerializer(), new SqsAdapter(
    'xxxxxxxxxxxx', // key
    'xxxxxxxxxxxx', // secret
    'ap-northeast-1', // region
    'https://sqs.ap-northeast-1.amazonaws.com/000000000000/queue-name' // queue url
));
while (true) {
    $job = $sender->dequeue();
    if ($job) {
        print_r($job->read());
        $job->delete();
    }
    sleep(3);
}
```
