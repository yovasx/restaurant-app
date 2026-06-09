<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloud Upload
    |--------------------------------------------------------------------------
    */
    'cloud_enabled' => env('BACKUP_CLOUD_ENABLED', false),
    'cloud_disk' => env('BACKUP_CLOUD_DISK', 'r2'),
    'cloud_prefix' => env('BACKUP_CLOUD_PREFIX', 'dev'),

    /*
    |--------------------------------------------------------------------------
    | Scheduled Backup
    |--------------------------------------------------------------------------
    */
    'schedule_enabled' => env('BACKUP_SCHEDULE_ENABLED', true),
    'schedule_time' => env('BACKUP_SCHEDULE_TIME', '02:00'),

    /*
    |--------------------------------------------------------------------------
    | Local Retention
    |--------------------------------------------------------------------------
    */
    'local_retention_days' => env('BACKUP_LOCAL_RETENTION_DAYS', 7),

];
