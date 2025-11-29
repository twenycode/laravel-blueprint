<?php

namespace TwenyCode\LaravelBlueprint\Helpers;

use Carbon\Carbon;
use DateTime;
class DateHelper
{
    // Convert a date and time string from one format to another
    public static function dateTimeConversion($date1, $date2 = 'd M Y H:i:s')
    {
        if (!is_null($date1)) {
            return Carbon::parse($date1)->format($date2);
        }
        return null;
    }

    // Calculate number of days between two dates
    public static function numberOfDays($date1, $date2): float
    {
        $date1 = strtotime($date1);
        $date2 = strtotime($date2);
        $difference = $date2 - $date1;
        return round($difference / (60 * 60 * 24));
    }

    // Calculate the age of a record in days from a given date to today
    public static function calculateAge($date)
    {
        if ($date != null) {
            $dob = new DateTime($date);
            $today = new DateTime(date('Y-m-d'));
            $interval = $today->diff($dob);
            return $interval->days;
        }
        return null;
    }

    // Get difference between two dates in human-readable format
    public static function dateDifference($start_date, $end_date): string
    {
        $fromDate = new DateTime($start_date);
        $curDate = new DateTime($end_date);
        $months = $curDate->diff($fromDate);

        if ($months->format('%y') > 0) {
            return $months->format('%y years %m months');
        }

        return $months->format('%m months');
    }

    // Calculate number of days remaining from now to a future date
    public static function calculateRemainingDays($date): float
    {
        $now = Carbon::now();
        return max($now->diffInDays($date), 0);
    }

    // Format date range in a readable format
    public static function formatDateDuration($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Same month and year
        if ($start->month == $end->month && $start->year == $end->year) {
            return $start->format('F d') . '-' . $end->format('d') . ', ' . $start->format(' Y');
        }

        // Different months but same year
        if ($start->year == $end->year) {
            return $start->format('d M') . ' - ' . $end->format('d M, Y');
        }

        // Different years
        return $start->format('d M Y') . ' - ' . $end->format('d M Y');
    }

    // Format time as a human-readable "time ago" string
    public static function formatTimeAgo($timestamp)
    {
        $time_ago = strtotime($timestamp);
        $current_time = time();
        $timeDifference = $current_time - $time_ago;

        $intervals = [
            31553280 => ['year', 'years'],
            2629440  => ['month', 'months'],
            604800   => ['week', 'weeks'],
            86400    => ['day', 'days'],
            3600     => ['hour', 'hours'],
            60       => ['minute', 'minutes'],
            1        => ['second', 'seconds']
        ];

        foreach ($intervals as $seconds => list($singular, $plural)) {
            $count = floor($timeDifference / $seconds);
            if ($count > 0) {
                return $count === 1 ? "1 $singular ago" : "$count $plural ago";
            }
        }

        return "Just now";
    }
}