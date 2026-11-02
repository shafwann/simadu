<?php

return [
    // Keep false in normal use. Enable locally only while testing attendance on Sunday.
    'allow_sunday_testing' => (bool) env('ATTENDANCE_ALLOW_SUNDAY_TESTING', false),
];
