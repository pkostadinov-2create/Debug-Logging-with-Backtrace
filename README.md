# Debug-Logging-with-Backtrace

Include this file in your wp-config.php, for example add it under WP_DEBUG:

```
define('WP_DEBUG', false);
require_once('theme-only-debug.php');
```

__PS:__ No longer need this kind of hacks for debugging, since I am now using PhpStorm.
