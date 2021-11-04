<?php

/**
 * Check if the given DateTime object is a business day.
 *
 * @param DateTime $date
 * @return bool
 */
function isBusinessDay(DateTime $date)
{
    //Weekends
    if ($date->format('N') > 5) {
        return false;
    }

    //Hard coded public Holidays
    $holidays = [
        "Human Rights Day"      => new DateTime(date('Y') . '-03-21'),
        "Good Friday"           => new DateTime(date('Y') . '-03-30'),
        "Family Day"            => new DateTime(date('Y') . '-04-02'),
        "Freedom Day"           => new DateTime(date('Y') . '-04-27'),
        "Labour Day"            => new DateTime(date('Y') . '-05-01'),
        "Youth Day"             => new DateTime(date('Y') . '-06-16'),
        "National Women's Day"  => new DateTime(date('Y') . '-08-09'),
        "Heritage Day"          => new DateTime(date('Y') . '-09-24'),
        "Day of Reconciliation" => new DateTime(date('Y') . '-12-16'),
    ];

    foreach ($holidays as $holiday) {
        if ($holiday->format('Y-m-d') === $date->format('Y-m-d')) {
            return false;
        }
    }

    //December company holidays
    if (new DateTime(date('Y') . '-12-15') <= $date && $date <= new DateTime((date('Y') + 1) . '-01-08')) {
        return false;
    }

    // Other checks can go here

    return true;
}

/**
 * Get the available business time between two dates (in seconds).
 *
 * @param $start
 * @param $end
 * @return mixed
 */
function businessTime($start, $end)
{
    $start = $start instanceof \DateTime ? $start : new DateTime($start);
    $end = $end instanceof \DateTime ? $end : new DateTime($end);
    $dates = [];

    $date = clone $start;

    while ($date <= $end) {

        $datesEnd = (clone $date)->setTime(23, 59, 59);

        if (isBusinessDay($date)) {
            $dates[] = (object)[
                'start' => clone $date,
                'end'   => clone ($end < $datesEnd ? $end : $datesEnd),
            ];
        }

        $date->modify('+1 day')->setTime(0, 0, 0);
    }

    return array_reduce($dates, function ($carry, $item) {

        $businessStart = (clone $item->start)->setTime(8, 0, 0);
        $businessEnd = (clone $item->start)->setTime(16, 30, 0);

        $start = $item->start < $businessStart ? $businessStart : $item->start;
        $end = $item->end > $businessEnd ? $businessEnd : $item->end;

        //Diff in seconds
        return $carry += max(0, $end->getTimestamp() - $start->getTimestamp());
    }, 0);
}

/*$seconds = businessTime('2018-01-01 10:00:00', '2018-01-03 15:00:00');

echo $seconds;*/

$observationData = array();
$observationData['ClockRefAtFrom'] = 6;
$observationData['ClockRefTo'] = 3;

$clockRefFrom = !empty($observationData['ClockRefAtFrom']) ? $observationData['ClockRefAtFrom'] : 0;
$clockRefTo = !empty($observationData['ClockRefTo']) ? $observationData['ClockRefTo'] : 0;

if($clockRefFrom <= $clockRefTo){
	$clockFace = $clockRefTo - $clockRefFrom;
}
else{
	$clockRefFrom = !empty($clockRefFrom) ? $clockRefFrom : 12;
	$clockRefFrom = 12 - $clockRefFrom;
	$clockFace = $clockRefFrom + $clockRefTo;
}

echo $clockFace;
?>