<?php
// One-time OPcache reset helper — delete this file after use
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache cleared. Now delete this file.";
} else {
    echo "OPcache not enabled or not available.";
}
