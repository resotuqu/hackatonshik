<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Organizer show page: vertical navigation (desktop)
    |--------------------------------------------------------------------------
    |
    | When true, desktop layout uses a sticky sidebar for hackaton sections;
    | horizontal tabs remain for small screens.
    |
    */
    'organizer_show_sidebar' => env('ORGANIZER_SHOW_SIDEBAR', true),

    /*
    |--------------------------------------------------------------------------
    | Organizer readiness checklist
    |--------------------------------------------------------------------------
    */
    'organizer_readiness_min_accepted_applications' => (int) env('ORGANIZER_READINESS_MIN_ACCEPTED_APPLICATIONS', 3),
];
