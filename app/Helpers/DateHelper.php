<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Format a date to the users local timezone with an optional format
     *
     * @param Carbon|string $date
     * @param string $format
     *
     * @return mixed
     */
    static function localDate($date, string $format = 'Y-m-d H:i:s')
    {
        if (!$date instanceof Carbon) {
            $date = new Carbon($date);
        }

        if (auth()->check()) {
            $date->setTimezone(auth()->user()->timezone);
        }

        return $date->format($format);
    }

    /**
     * Format a date from a defined timezone to UTC. Used when saving in database
     *
     * @param $date
     * @param $timezone
     * @param string $format
     * @return string
     */
    static function toUTCDate($date, $from_timezone, string $format = 'Y-m-d H:i:s')
    {
        if (!$date instanceof Carbon) {
            $date = new Carbon($date);
        }

        $date->setTimezone($from_timezone);
        return $date->format($format);

    }

}
