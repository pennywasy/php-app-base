<?php
// define('C_REST_CLIENT_ID','local.5c8bb1b0891cf2.87252039');//Application ID
// define('C_REST_CLIENT_SECRET','SakeVG5mbRdcQet45UUrt6q72AMTo7fkwXSO7Y5LYFYNCRsA6f');//Application key
// or
if (!defined('C_REST_WEB_HOOK_URL')) {
    define('C_REST_WEB_HOOK_URL', $_ENV['BITRIX_WEBHOOK_URL']); //url on creat Webhook
}

if (!defined('C_REST_CURRENT_ENCODING')) {
    define('C_REST_CURRENT_ENCODING', 'utf-8');

}

if (!defined('C_REST_IGNORE_SSL')) {
    define('C_REST_IGNORE_SSL', true); //turn off validate ssl by curl

}

if (!defined('C_REST_LOG_TYPE_DUMP')) {
    define('C_REST_LOG_TYPE_DUMP', true); //logs save var_export for viewing convenience

}

if (!defined('C_REST_BLOCK_LOG')) {
    define('C_REST_BLOCK_LOG', true); //turn off default logs

}

if (!defined('C_REST_LOGS_DIR')) {
    define('C_REST_LOGS_DIR', __DIR__ . "/logs/"); //directory path to save the log

}