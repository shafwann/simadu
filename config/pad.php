<?php

return [
    'python' => env('PAD_PYTHON', 'python'),
    'repository' => env('PAD_REPOSITORY') ?: base_path('resources/pad/silent-face'),
    'real_threshold' => (float) env('PAD_REAL_THRESHOLD', 0.70),
    'required_real_frames' => (int) env('PAD_REQUIRED_REAL_FRAMES', 2),
];
