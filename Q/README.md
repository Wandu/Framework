Wandu Q
===

Very Simple Queue.

---

## Example

**Sender**

```php
use Wandu\Q\Adapter\SqsAdapter;
use Wandu\Q\Queue;
use Wandu\Q\Serializer\JsonSerializer;

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
use Wandu\Q\Adapter\SqsAdapter;
use Wandu\Q\Queue;
use Wandu\Q\Serializer\JsonSerializer;

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
