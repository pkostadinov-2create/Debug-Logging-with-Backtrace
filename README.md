# Debug-Logging-with-Backtrace

Include this file in your wp-config.php, for example add it under WP_DEBUG:

```
define('WP_DEBUG', false);
require_once('theme-only-debug.php');
```