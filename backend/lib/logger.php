<?php

class Logger {
    private static $logDir = __DIR__ . '/../logs/';

    public static function log($message, $level = 'INFO') {
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0777, true);
        }
        $date = date('Y-m-d H:i:s');
        $formattedMessage = "[$date] [$level] $message" . PHP_EOL;
        file_put_contents(self::$logDir . 'app.log', $formattedMessage, FILE_APPEND);
    }

    public static function error($message) {
        self::log($message, 'ERROR');
    }
}
