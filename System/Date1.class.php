<?php

@session_start();

class Date {

	var $year;
	var $month;
	var $day;
	var $hour;
	var $minute;
	var $second;
	var $CurrentDate;

	function __construct() {
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");

		$this->year = date("Y");
		$this->month = date("m");
		$this->day = date("d");

		$this->hour = date("H");
		$this->minute = date("i");
		$this->second = date("s");

		$this->CurrentDate = "$this->year-$this->month-$this->day $this->hour:$this->minute:$this->second";

		date_default_timezone_set($MainTimezone);
	}

	static function FormatToCompare($date) {
		if (empty($date)) {
			$format = "-";
		} else {
			$date = substr($date, 0, 20) . substr($date, 24, 2);
			$format = strtotime($date);
			$format = date("Y-m-d H:i:s", $format);
		}
		return $format;
	}

	static function DateFormat($date, $type = 0) {
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");

		if (empty($date)) {
			$format = "-";
		} else {
			$date = substr($date, 0, 20) . substr($date, 24, 2);
			$format = strtotime($date);
			if ($type == 0) {
				$format = date($MainDateFormat, $format);
			} else {
				$format = date($MainDateFormatShort, $format);
			}
		}
		return $format;
	}

	static function TimeFormat($date, $complement = "") {
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");

		if (empty($date)) {
			$format = "-";
		} else {
			$date = substr($date, 0, 20) . substr($date, 24, 2);
			$format = strtotime($date);
			$format = date($MainTimeFormat, $format);
		}
		return $format . $complement;
	}

	function TimeRemaining($date) {
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/GenericMessages.php");

		$Array = explode(" ", date("Y-m-d H:i:s", strtotime(substr($date, 0, 20) . substr($date, 24, 2))));
		$DateArray = explode("-", $Array[0]);
		$TimeArray = explode(":", $Array[1]);

		$DateTo = mktime($TimeArray[0], $TimeArray[1], $TimeArray[2], $DateArray[1], $DateArray[2], $DateArray[0]);
		$DateFrom = time();

		$RemainingTime = $DateTo - $DateFrom;
		if ($RemainingTime < 60) {
			return "-";
		}

		$Days = intval($RemainingTime / 86400);

		$Hours = ($RemainingTime - ($Days * 86400));
		$Hours = intval($Hours / 3600);

		$Minutes = ($RemainingTime - ($Days * 86400) - ($Hours * 3600));
		$Minutes = intval($Minutes / 60);

		if ($Days > 0) {
			return $Days . " " . $GenericMessage17 . ", " . $Hours . " " . $GenericMessage18 . ", " . $Minutes . " " . $GenericMessage19;
		}

		if ($Hours > 0) {
			return $Hours . " " . $GenericMessage18 . ", " . $Minutes . " " . $GenericMessage19;
		}

		return $Minutes . " " . $GenericMessage19;
	}

	function ElapsedTime($date) {
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Config/Main.php");
		require($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "Language/$MainLanguage/GenericMessages.php");

		$Array = explode(" ", date("Y-m-d H:i:s", strtotime(substr($date, 0, 20) . substr($date, 24, 2))));
		$DateArray = explode("-", $Array[0]);
		$TimeArray = explode(":", $Array[1]);

		$DateFrom = mktime($TimeArray[0], $TimeArray[1], $TimeArray[2], $DateArray[1], $DateArray[2], $DateArray[0]);
		$DateTo = time();

		$ElapsedTime = $DateTo - $DateFrom;
		if ($ElapsedTime < 0) {
			return "< 1 min";
		}

		$Days = intval($ElapsedTime / 86400);

		$Hours = ($ElapsedTime - ($Days * 86400));
		$Hours = intval($Hours / 3600);

		$Minutes = ($ElapsedTime - ($Days * 86400) - ($Hours * 3600));
		$Minutes = intval($Minutes / 60);

		if ($Days > 0) {
			return $Days . " " . $GenericMessage17 . ", " . $Hours . " " . $GenericMessage18 . ", " . $Minutes . " " . $GenericMessage19;
		}

		if ($Hours > 0) {
			return $Hours . " " . $GenericMessage18 . ", " . $Minutes . " " . $GenericMessage19;
		}

		return $Minutes . " " . $GenericMessage19;
	}

}

?>