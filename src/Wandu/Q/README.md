Wandu Q
===

[![Latest Stable Version](https://poser.pugx.org/wandu/q/v/stable.svg)](https://packagist.org/packages/wandu/q)
[![Latest Unstable Version](https://poser.pugx.org/wandu/q/v/unstable.svg)](https://packagist.org/packages/wandu/q)
[![Total Downloads](https://poser.pugx.org/wandu/q/downloads.svg)](https://packagist.org/packages/wandu/q)
[![License](https://poser.pugx.org/wandu/q/license.svg)](https://packagist.org/packages/wandu/q)

Very Simple Queue.

## Websites

- [wandu.github.io](https://wandu.github.io)

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
