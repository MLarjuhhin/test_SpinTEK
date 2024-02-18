<?php

namespace Service;

use Carbon\Carbon;
use Config\Config;
use Exception;

class DateListGeneratorService
{
    private $year;
    private $dayType;
    private $nofication_day;
    private $holidaysFetcher;

    /**
     *
     * @param  int  $year  The year for which dates will be generated.
     * @param  HolidayManagerService  $holidayManager  Holiday service used to obtain information about holidays.
     * @param  string  $dayType  The day type that is used to generate the list of dates
     */
    public function __construct(int $year, HolidayManagerService $holidayManager, $dayType = null)
    {
        $this->year = $year;
        $this->holidaysFetcher = $holidayManager;
        $this->nofication_day = Config::getInstance()->get('notificationDays');
        $this->dayType = $dayType ?: Config::getInstance()->get('dayType');
    }

    /**
     * Generates a list of payment dates and notifications for each month of the year.
     *
     * @return array An array with information about payment dates and notifications for each month.
     *  Return array structure: [$month => ['pay_day' => 'dd.mm.yyyy', 'notification_day' => 'dd.mm.yyyy']]
     * @throws Exception If there is an error or data is empty
     */
    public function generateList()
    {
        $result = [];

        for ($month = 1; $month <= 12; $month++) {
            $paymentDate = $this->calculatePaymentDate($month);

            $notificationDate = $this->calculateNotificationDate($paymentDate);

            $result[$month] = [
                'pay_day'          => $paymentDate->format('d.m.Y'),
                'notification_day' => $notificationDate->format('d.m.Y')
            ];
        }
        if (empty($result)) {
            throw new \Exception("Result list is empty.");
        }
        return $result;

    }

    /**
     * Calculates the due date for a given month
     *
     * @param  int  $month The month for which you want to calculate the payment date.
     * @return Carbon Payment date Carbon
     */
    private function calculatePaymentDate(int $month): Carbon
    {
        if ($this->dayType === 'last_workday') {
            $date = Carbon::createFromDate($this->year, $month, 1)->endOfMonth();

        } else {
            if (!$this->isValidDate($month, $this->dayType)) {
                $date = Carbon::createFromDate($this->year, $month, 1)->endOfMonth();
            } else {
                $date = Carbon::create($this->year, $month, $this->dayType, 0, 0, 0, 'Europe/Tallinn');
            }
        }
        $date = $this->getAdjustedDate($date);

        return $date;
    }

    /**
     * The incoming date is changed, shifting it to the previous working day if it falls on a weekend or holiday.
     *
     * @param  Carbon  $date Original date
     * @return Carbon Modified date as Carbon
     * @throws Exception
     */
    private function getAdjustedDate(Carbon $date): Carbon
    {

        if ($date->isWeekend() || $this->isHoliday($date)) {
            return $date->previousWeekday();
        }
        return $date;
    }

    /**
     * Calculates the notification date that is X business days prior to the specified payment date.
     *
     * @param  Carbon  $paymentDate The payment date from which to count.
     * @return Carbon Notice date calculated as X business days before the payment date.
     * @throws Exception
     */
    private function calculateNotificationDate(Carbon $paymentDate): Carbon
    {
        $threeWorkdaysAgo = clone $paymentDate;
        $workdaysCount = 0;

        while ($workdaysCount < $this->nofication_day) {
            $threeWorkdaysAgo = $threeWorkdaysAgo->subDay();
            if (!$threeWorkdaysAgo->isWeekend() && !$this->isHoliday($threeWorkdaysAgo)) {
                $workdaysCount++;
            }
        }

        return $threeWorkdaysAgo;
    }

    /**
     * Determines whether the specified date is a holiday
     *
     * @param Carbon $date  The date that needs to be checked to see if it falls on a holiday.
     * @return bool Returns true if the date is a holiday, false otherwise.
     * @throws \Exception
     */
    private function isHoliday(Carbon $date): bool
    {

        $holidays = $this->holidaysFetcher->getHolidays($date->format('Y'));
        if (isset($holidays[$date->format('Y')])) {
            return in_array($date->format('Y-m-d'), $holidays[$date->format('Y')]);
        } else {
            return false;
        }

    }

    /**
     * Checks the validity of a date for a given year.
     * for example to remove 30 February
     *
     * @param  int  $month The month of the date whose validity needs to be checked.
     * @param  int  $day Day of the date whose validity needs to be checked.
     * @return bool Returns true if the date is valid for the given year, false otherwise.
     */
    private function isValidDate(int $month, int $day): bool
    {
        return checkdate($month, $day, $this->year);
    }

}