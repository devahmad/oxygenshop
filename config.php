<?php
// تنظیمات دیتابیس
define('DB_HOST', 'localhost');
define('DB_NAME', 'shopmn');
define('DB_USER', 'root');
define('DB_PASS', 'root');

// تنظیمات سایت
define('SITE_TITLE', 'فروشگاه اکسیژن');
define('BASE_URL', 'http://localhost:8000'); // آدرس لوکال

function shamsi($date)
{
    if (!$date)
        return '-';
    $time = strtotime($date);
    $formatter = new IntlDateFormatter(
        "fa_IR@calendar=persian",
        IntlDateFormatter::FULL,
        IntlDateFormatter::FULL,
        'Asia/Tehran',
        IntlDateFormatter::TRADITIONAL,
        "yyyy/MM/dd HH:mm"
    );
    return $formatter->format($time);
}
