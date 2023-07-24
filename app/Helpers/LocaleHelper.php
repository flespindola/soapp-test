<?php
namespace App\Helpers;

class LocaleHelper{

    static function getDropdownlocales() :  array
    {
        $locales = [];

        if(empty(config('app.locales'))){
            return $locales;
        }

        foreach (config('app.locales') as $key => $locale) {
            $locales[$key] = __('app.' . $locale) . " (" . strtoupper($key) . ")";
        }

        return $locales;
    }

    static function getTimezonesList(): array
    {
        $timezones_list = [];
        $timezones = \DateTimeZone::listIdentifiers();
        foreach ($timezones as $timezone) {

            $default = date("Y-m-d h:i:s A");// UTC
            $dateTimeZone = new \DateTime($default);
            $dateTimeZone->setTimezone(new \DateTimeZone($timezone));
            $other = $dateTimeZone->format('Y-m-d h:i:s A');
            $hours = (strtotime($other) - strtotime($default)) / 60 / 60;
            $timezones_list[$timezone] = $timezone . " (UTC " . ($hours > 0 ? "+" : "") . $hours . ":00)";

        }

        return $timezones_list;
    }

}


