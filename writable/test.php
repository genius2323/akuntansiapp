<?php
file_put_contents(__DIR__ . '/test.txt', date('Y-m-d H:i:s') . "\n", FILE_APPEND);
echo 'OK';
