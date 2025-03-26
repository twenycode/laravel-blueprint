<?php

namespace TwenyCode\LaravelCore\Helpers;

use Carbon\Carbon;
use DateTime;

class DateHelper
{
    /**
     * Convert a date and time string from one format to another.
     *
     * @param string|null $date1 The date to convert
     * @param string $date2 The target format
     * @return string|null
     */
    public static function dateTimeConversion($date1, $date2 = 'd M Y H:i:s')
    {
        if (!is_null($date1)) {
            return Carbon::parse($date1)->format($date2);
        }
        return null;
    }

    /**
     * Calculate number of days between two dates.
     *
     * @param string $date1 Start date
     * @param string $date2 End date
     * @return float
     */
    public static function numberOfDays($date1, $date2): float
    {
        $date1 = strtotime($date1);
        $date2 = strtotime($date2);
        $difference = $date2 - $date1;
        return round($difference / (60 * 60 * 24));
    }

    /**
     * Calculate the age of a record in days.
     *
     * @param string|null $date The date to calculate from
     * @return int|null
     */
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

    /**
     * Get difference between two dates in human-readable format.
     *
     * @param string $start_date Start date
     * @param string $end_date End date
     * @return string
     */
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

    /**
     * Calculate number of days remaining from now to a date.
     *
     * @param string $date The target date
     * @return float
     */
    public static function calculateRemainingDays($date): float
    {
        $now = Carbon::now();
        return max($now->diffInDays($date), 0);
    }

    /**
     * Format date range in a readable format.
     *
     * @param string $startDate Start date
     * @param string $endDate End date
     * @return string
     */
    public static function formatDateDuration($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // If same month and year
        if ($start->month == $end->month && $start->year == $end->year) {
            return $start->format('F d') . '-' . $end->format('d') . ', ' . $start->format(' Y');
        }

        // If different months but same year
        if ($start->year == $end->year) {
            return $start->format('d M') . ' - ' . $end->format('d M, Y');
        }

        // If different years
        return $start->format('d M Y') . ' - ' . $end->format('d M Y');
    }

    /**
     * Format time as a human-readable "time ago" string.
     *
     * @param string $timestamp The timestamp to format
     * @return string
     */
    public static function formatTimeAgo($timestamp)
    {
        // Convert timestamp to time ago
        $time_ago = strtotime($timestamp);
        $current_time = time();
        $timeDifference = $current_time - $time_ago;

        // Define time intervals and their singular/plural labels
        $intervals = [
            31553280 => ['year', 'years'],      // Approximate seconds in a year
            2629440  => ['month', 'months'],    // Approximate seconds in a month
            604800   => ['week', 'weeks'],      // Seconds in a week
            86400    => ['day', 'days'],        // Seconds in a day
            3600     => ['hour', 'hours'],      // Seconds in an hour
            60       => ['minute', 'minutes'],  // Seconds in a minute
            1        => ['second', 'seconds']   // Seconds
        ];

        // Iterate through the intervals and return the appropriate time format
        foreach ($intervals as $seconds => list($singular, $plural)) {
            $count = floor($timeDifference / $seconds);
            if ($count > 0) {
                return $count === 1 ? "1 $singular ago" : "$count $plural ago";
            }
        }
        return "Just now"; // If less than a second has passed
    }
}