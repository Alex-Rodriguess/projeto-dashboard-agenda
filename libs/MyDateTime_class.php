<?php

/**
 * MyDateTime - Php Multilanguage DateTime
 *
 * @package    MyDateTime
 * @author    Leonardo Caitano <leonardo@consept.com.br>
 * @website    http://leonardocaitano.com.br
 * @since    05/09/2013
 * @license    Free
 *
 */

class MyDateTime
{

    const TIMEZONE = 'UTC';

    /**
     * Returns new date string formatted according to the specified format
     *
     * @param string $dateString
     * @param string $formatFrom
     * @param string $formatTo
     * @return string
     */
    public static function format($dateString, $formatFrom, $formatTo)
    {
        $dateTimeObj = DateTime::createFromFormat($formatFrom, $dateString, new DateTimeZone(self::TIMEZONE));

        if (!$dateTimeObj) {
            return NULL;
        }


        $dateMonth = self::getMonth($dateTimeObj->format('m'));
        $dateMonthAbbrev = substr($dateMonth, 0, 3);
        $dateWeekDay = self::getDay($dateTimeObj->format('D'));
        $dateWeekDayAbbrev = substr($dateWeekDay, 0, 3);

        // inspired by wordpress
        $formatTo = ' ' . $formatTo;
        $formatTo = preg_replace("/([^\\\])F/", "\\1" . self::backSlashIt($dateMonth), $formatTo);
        $formatTo = preg_replace("/([^\\\])M/", "\\1" . self::backSlashIt($dateMonthAbbrev), $formatTo);
        $formatTo = preg_replace("/([^\\\])l/", "\\1" . self::backSlashIt($dateWeekDay), $formatTo);
        $formatTo = preg_replace("/([^\\\])D/", "\\1" . self::backSlashIt($dateWeekDayAbbrev), $formatTo);
        $formatTo = substr($formatTo, 1, strlen($formatTo) - 1);

        return $dateTimeObj->format($formatTo);
    }

    /**
     * Returns new date string in long format
     *
     * @param string $dateString
     * @param string [$formatFrom]
     * @return string
     */
    public static function longDateFormat($dateString, $formatFrom)
    {
        return static::format($dateString, $formatFrom, 'j \d\e F \d\e Y');
    }

    /**
     * Validate a date
     *
     * @param string $dateString
     * @param string $formatFrom
     * @return bool
     */
    public static function is_valid($dateString)
    {
        @list($y, $m, $d) = explode("-", $dateString);
        if (is_numeric($y) && is_numeric($m) && is_numeric($d)) {
            return checkdate($m, $d, $y);
        }

        return false;
    }

    // ------------------------ private methods -----------------------------

    /**
     * Returns correct names for months
     *
     * @param string $month
     * @return string
     */
    private static function getMonth($month)
    {
        $months = array(
            '01' => 'Janeiro',        // January
            '02' => 'Fevereiro',        // February
            '03' => 'Março',        // March
            '04' => 'Abril',        // April
            '05' => 'Maio',            // May
            '06' => 'Junho',        // June
            '07' => 'Julho',        // July
            '08' => 'Agosto',        // August
            '09' => 'Setembro',        // September
            '10' => 'Outubro',        // October
            '11' => 'Novembro',        // November
            '12' => 'Dezembro'        // December
        );

        return $months[$month];
    }


    /**
     * Returns correct names for week days
     *
     * @param string $day
     * @return string
     */
    private static function getDay($day)
    {
        $days = array(
            'Sun' => 'Domingo',        // Sunday
            'Mon' => 'Segunda-feira',    // Monday
            'Tue' => 'Terça-feira',    // Tuesday
            'Wed' => 'Quarta-feira',    // Wednesday
            'Thu' => 'Quinta-feira',    // Thursday
            'Fri' => 'Sexta-feira',    // Friday
            'Sat' => 'Sábado'        // Saturday
        );

        return $days[$day];
    }

    /**
     * Adds backslashes before letters and before a number at the start of a string.
     *
     * @param string $string Value to which backslashes will be added.
     * @return string String with backslashes inserted.
     *
     * @package WordPress
     * @since 0.71
     *
     */
    private static function backSlashIt($string)
    {
        if (isset($string[0]) && $string[0] >= '0' && $string[0] <= '9')
            $string = '\\\\' . $string;
        return addcslashes($string, 'A..Za..z');
    }

}
