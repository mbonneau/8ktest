This project can be used to demonstrate the issues with
having an 8k chunk size as PHP < 8 has.

In some environments, this can cause very substantial slow downs
if reading and writing messages larger than 8k.

The slowdown (at least as far as I have observed) is caused by a wait 
for the ACK from the first 8k chunk before the underlying 
network sends the next fragment.

Example from a vagrant instance:
```text
Connection opened...
With message size 8192, got 100 responses in 0.025506973266602 seconds.
With message size 8193, got 100 responses in 8.0555248260498 seconds.
Connection closed...
```

To use:
```shell script
composer install
```

In one shell, run the server:
```shell script
php 8ktest.php
```

In another shell, run the client:
```shell script
php 8ktestclient.php
```

Fixes this issue for PHP 8:
https://github.com/php/php-src/commit/5cbe5a538c92d7d515b0270625e2f705a1c02b18

Possibly related:
https://bugs.php.net/bug.php?id=73262  