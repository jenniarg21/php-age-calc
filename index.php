<?php
	define("BIRTHDATE", "1.1.1902");
	define("FIRST_LEAP_YEAR", 1904);
	define("DAYS_IN_YEAR", 365);
	define("NUM_LEAP_YEAR_OCCURS", 4);
	define("FEB_NO_LEAP", 28);
	define("FEB_LEAP", 29);
	define("JAN_MAR_MAY_JUL_AUG_OCT_DEC", 31);
	define("APR_JUN_SEP_NOV", 30);
	define("FIRST_YEAR", 1970);
	define("LAST_YEAR" ,2038);

/* Set dates from default values or user input */
	//creates an array out of birthdate and sets cookie if necessary
	if (isset($_POST["submit"]) && isset($_POST["birthdate"]) && !empty($_POST["birthdate"])) {
		setcookie("BirthdateCookie", $_POST["birthdate"], 0, "/");
		$birthArray = explode(".", date_format(date_create_from_format("Y-m-d", $_POST["birthdate"]), "n.j.Y"));
	}
	else if (isset($_COOKIE["BirthdateCookie"]) && !empty($_COOKIE["BirthdateCookie"])) {
		$birthArray = explode(".", date_format(date_create_from_format("Y-m-d", $_COOKIE["BirthdateCookie"]), "n.j.Y"));
	}
	else {
		$birthArray = explode(".", BIRTHDATE);
	}

	//creates an array from second date
	if (empty($_GET["year"]) || !filter_input(INPUT_GET, "year", FILTER_VALIDATE_INT) || !filter_input(INPUT_GET, "month", FILTER_VALIDATE_INT)) {
	//assigns current date when nothing is passed from GET
		$date = date("n.j.Y"); //the current date will be used
		$dateArray = explode(".", $date); //makes an array out of date
	}
	else if (empty($_GET["day"]) || !filter_input(INPUT_GET, "day", FILTER_VALIDATE_INT)) {
			//creates an array with the selected date out of GET values month and year
			$year = $_GET["year"];
			$month = $_GET["month"];
			$dateArray = array($_GET["month"], 1, $_GET["year"]);
	}
	else {
		//creates an array with the selected date out of GET values day, month, and year
		$year = $_GET["year"];
		$month = $_GET["month"];
		$day = $_GET["day"];
		$dateArray = array($_GET["month"], $_GET["day"], $_GET["year"]);
	}

/*
** Function Name: calcDaysBetweenMonths
** Description: calculates the number of days between two months and adjusts for starting month not being on the first if desired
** Parameters: 
		(1) {int} start - the starting month
		(2) {int} offset - the day of the straing month to offset by
		(3) {int} end - the ending month
** Return Value: {int} res - the total days between the two months
*/
function calcDaysBetweenMonths($start, $offset, $end) {
	$res = 0;

	while ($start < $end) {
		//calculates the number of days in Feburary
		if ($start == 2) {
			if ($end % NUM_LEAP_YEAR_OCCURS != 0) {
				if ($offset > 0) {
					$res = FEB_NO_LEAP - $offset;
					$offset = 0;
				}
				else {
					$res += FEB_NO_LEAP;
				}
			}
			else if ($offset > 0) {
				$res = FEB_LEAP - $offset;
				$offset = 0;
			}
			else {
				$res += FEB_LEAP;
			}
		}

		//calculates the number of days in the first 7 months of a year, excluding Feburary
		if ($start <= 7 && $start != 2) {
			if ($start % 2 == 1) {
				if ($offset > 0) {
					$res = JAN_MAR_MAY_JUL_AUG_OCT_DEC - $offset;
					$offset = 0;
				}
				else {
					$res += JAN_MAR_MAY_JUL_AUG_OCT_DEC;
				}
			}
			else if ($offset > 0) {
				$res = APR_JUN_SEP_NOV - $offset;
				$offset = 0;
			}
			else {
				$res += APR_JUN_SEP_NOV;
			}
		}

		//calculates the number of days in the last 5 months of a year
		if($start >= 8) {
			if ($start % 2 == 1) {
				if ($offset > 0) {
					$res = APR_JUN_SEP_NOV - $offset;
					$offset = 0;
				}
				else {
					$res += APR_JUN_SEP_NOV;
				}
			}
			else if ($offset > 0) {
				$res = JAN_MAR_MAY_JUL_AUG_OCT_DEC - $offset;
				$offset = 0;
			}
			else {
				$res += JAN_MAR_MAY_JUL_AUG_OCT_DEC;
			}
		}
		$start++;
	} //end while loop
	return $res;
}

/*
** Function Name: calcAgeInDays
** Description: calculates the age into days
** Parameters: 
		(1) {array} birthArray - the birthdate
		(2) {array} birthArray - the date selected to make the age calculation
** Return Value: {int} ageInDays - the calculated age in days
*/
function calcAgeInDays ($birthArray, $dateArray, $date) {
	$ageInDays = 0;

	//calculates age in years
	if ($dateArray[2] == $birthArray[2]) {
		$ageInYears = 0;
	}
	else if ($birthArray[0] > $dateArray[0]) {
		$ageInYears = $dateArray[2] - $birthArray[2] - 1;
	}

	//calculates total number of leap years lived through
	if ($birthArray[2] < FIRST_LEAP_YEAR) {
		$start = 0;
		if ($dateArray[2] > FIRST_LEAP_YEAR)
		{
			$numOfLeapYears = 1;
		}
	}
	else {
		$start = $birthArray[2] - FIRST_LEAP_YEAR;
		$numOfLeapYears = 0;
	}
	$end = $dateArray[2] - FIRST_LEAP_YEAR;
	//loop through all leap years and check if divisable by birth year
	while ($start++ < $end) {
		if ($start % 4 == 0) {
			$numOfLeapYears++;
		}
	}

	//calculates how many days lived in the months before the selected date's month
	if ($birthArray[0] == $dateArray[0]) {
			$ageInDays = $dateArray[1] - $birthArray[1];
	}
	else if ($birthArray[0] > $dateArray[0]) {
		$ageInDays = calcDaysBetweenMonths($birthArray[0], $birthArray[1], 13);
		$ageInDays += calcDaysBetweenMonths(1, 0, $dateArray[0]);
		$ageInDays += $dateArray[1];
	}
	else {
		$ageInDays += calcDaysBetweenMonths($birthArray[0], $birthArray[1], $dateArray[0]) + $dateArray[1];
	}

	$ageInDays += ($ageInYears * DAYS_IN_YEAR) + $numOfLeapYears; //calculates final number for the total age of days

	return $ageInDays;
}

?>
<!--This page was created by Jennifer Argote for IT207 002.-->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
	<title>Lab 7 || Age Calculator</title>
	<link rel="stylesheet" type="text/css" href="lab7styles.css" />
	<script type="text/javascript">
		/*
		**	Function Name:	tooLow
		**	Description:	alert the user that years before 1970 are not available
		**	Parameters:		none
		**	Return Value:	none
		*/
		function tooLow() {
			window.alert("Calendar year before 1970 is not available.");
		}

		/*
		**	Function Name:	tooHigh
		**	Description:	alert the user that years after 2038 are not available
		**	Parameters:		none
		**	Return Value:	none
		*/
		function tooHigh() {
			window.alert("Calendar year after 2038 is not available.");
		}
	</script>
</head>

<body>
	<div id="container">
		<h1>Age Calculator</h1>
		<p>Calculate how old you are in days. For fun, your age will be displayed in two histogram charts at the end of the page.</p>
<?php
	/* calculate age in days from birthdate and second date */
	$ageInDays = calcAgeInDays($birthArray, $dateArray, $date);

	//dates chosen set as date types
	$date = mktime(0, 0, 0, $dateArray[0], $dateArray[1], $dateArray[2]);
	$bday = mktime(0, 0, 0, $birthArray[0], $birthArray[1], $birthArray[2]);
	$birthdate = date("F jS", $bday);

	if (date("Y-m-d", $bday) > date("Y-m-d", $date)) {
		echo "<p class=\"error\">Uh oh! The Date of Birth occurs after the Age at Date. Change either date to calculate an age.</p>";
	}
?>

	<input type="checkbox" id="bdayToggle" />
	<div class="dateInfo">
		<span class="bold">Date of Birth:</span> <?php echo $birthdate . ", " . $birthArray[2]; ?>
		&nbsp;(<label for="bdayToggle">Change</label>)
	</div>
<?php
	$selectedDate = date("F jS, Y", $date);

	//form for changing default birthdate
?>
	<form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="birthForm">
		<label for="birthdate" class="bold">Date of Birth: </label>
		<input type="date" id="birthdate" name="birthdate" min="1400-01-01" value="<?php echo date("Y-m-d", $bday); ?>" />
		<input type="submit" name="submit" value="Submit" />
	</form>

	<!-- Displays second date links (calendar) -->
	<a name=\"linkNav\"></a>

	<!-- Displays selected date -->
	<input type="checkbox" id="dateToggle" <?php if (isset($_GET["year"]) || isset($_GET["month"]) || isset($_GET["day"])) { echo "checked"; } ?> />
	<div class="dateInfo">
		<span class="bold">Age at the Date of:</span> <?php echo $selectedDate; ?>
		&nbsp;(<label for="dateToggle"></label>)
	</div>

	<div id="changeDateLinks">
		<p>You can choose a different Age at Date by using the calendar options below, or you can reset to <a href="index.php">today's date</a> (<span class="deco"><?php echo date("l, F jS, Y"); ?></span>)</p>
<?php
	//table with links for days in calendar format
	$dateFirstOfMon = mktime(0, 0, 0, $dateArray[0], 1, $dateArray[2]);
	$curDay = date("l", $dateFirstOfMon);

	$daysOfWeek = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");

	echo "<h3>" . date("F Y", $date) . "</h3>";

	//link for previous year
	if ($dateArray[2] <= FIRST_YEAR) {
		echo "<p class=\"calMonYearLinks\"><span class=\"deco error\">***Attention: Calendar years before ", FIRST_YEAR, " is not available.***</span><br /><a href=\"#linkNav\" onclick=\"tooLow()\">", ($dateArray[2]-1), "</a> | "; //no link available before 1970; uses onclick attribute to call tooLow() function
	}
	else {
		echo "<p class=\"calMonYearLinks\"><a href=\"index.php?year=", ($dateArray[2]-1), "&month=", $dateArray[0], "\">&laquo; ", ($dateArray[2]-1), "</a> | "; //previous year
	}

	//links for months
	for ($i = 1; $i < 13; $i++) {
		$mon = date("M", mktime(0, 0, 0, $i, 1, $dateArray[2]));
		echo "<a href=\"index.php?year=". $dateArray[2]. "&month=". $i. "\">" . $mon . "</a> | ";
	}

	//link for following year
	if ($dateArray[2]+1 >= LAST_YEAR) {
		echo "<a href=\"#linkNav\" onclick=\"tooHigh()\">", ($dateArray[2]+1), "</a><br /><span class=\"bold\">***</span><span class=\"deco\">Attention:</span>  Calendar year ", LAST_YEAR, " and after are not available.<span class=\"bold\">***</span></p>"; //no link available after 2038; uses onclick attribute to call tooHigh() function
	}
	else {
		echo "<a href=\"index.php?year=", ($dateArray[2]+1), "&month=", $dateArray[0], "\">", ($dateArray[2]+1), " &raquo;</a></p>"; //following year
	}


	echo "<table id=\"calendar\"><tr>";
	for ($i = 0; $i < count($daysOfWeek); $i++) {
		echo "<th>";
		echo $daysOfWeek[$i];
		echo "</th>";
	}
	echo "</tr><tr>";

	//displays first week in month if first of month doesn't fall on a Sunday
	$choosenDay = date("d", $date);
	for ($i = 0; $i < 7; $i++) {
		if ($daysOfWeek[$i] == $curDay) {
			//output trailing cells in calendar
			if ($i > 0) {
				$preDays = $i;
				while ($preDays-- > 0) {
					echo "<td></td>";
				}
				//output the calendar days for the first week of the month
				$preDays = $i;
				$dayCounter = 0;
				while ($preDays + $dayCounter++ < 7) {
					echo "<td><a href=\"index.php?year=". $dateArray[2]. "&month=". $dateArray[0]. "&day=". $dayCounter. "\" class=\"day\"";
					if ($dayCounter == $choosenDay) {
						echo " id=\"choosenDay\"";
					}
					echo ">". $dayCounter. "</td>"; //table cell with day
				}
			}
			else {
				$dayCounter = 1;
			}
			$trailing = $i;
			break;
		}
	} //end for loop

	//displays the rest of the days in choosen month
	while (checkdate($dateArray[0], $dayCounter, $dateArray[2])) {
		if (($dayCounter + $trailing) % 7 == 1) { //inserts row after 7 days
			echo "</tr><tr>";
		}

		echo "<td><a href=\"index.php?year=". $dateArray[2]. "&month=". $dateArray[0]. "&day=". $dayCounter. "\" class=\"day\"";
		if ($dayCounter == $choosenDay) {
			echo " id=\"choosenDay\"";
		}
		echo ">". $dayCounter. "</td>"; //individual cell for day
		$dayCounter++;
	} //end while loop

	echo "</tr></table>";
	echo "</div>";



	/* control structures used to display hisotgram tables */
	$ageInDaysArray; //array to hold individual numbers in age
	echo "<p><span class=\"bold\">Age</span>: " . $ageInDays . " days</p>";

	//this for loop creates an array out of the age in days
	for ($i = 0; $i < strlen($ageInDays); $i++) {
		$ageInDaysArray[$i] = substr($ageInDays, $i, 1);
	} //end for loop


	//begin displaying horizonal table
	echo "<h3>Horizonal Chart Table for Age</h3>";
	echo "<table id=\"horizonal\">";

	//displays content of table
	for ($i = 0; $i < count($ageInDaysArray); $i++) {
		echo "<tr><td class=\"num\">", $ageInDaysArray[$i], "</td>"; //outputs first cell of row

		//outputs table cell with a star, if applicable
		if ($ageInDaysArray[$i] > 0) {
			for($j = 1; $j <= $ageInDaysArray[$i]; $j++) {
				echo "<td class=\"star\">*</td>";
			}
		}

		echo "</tr>";
	} // end for loop

	echo "</table><br /><br />";
	//end displaying horizonal table



	//begin displaying vertical table
	echo "<h3>Vertical Chart Table for Age</h3>";
	echo "<table id=\"vertical\">";

	//finds out which individual number of age is the largest and therefore would be the longest on the histogram
	$longestVal = 0;
	for ($i = 0; $i < count($ageInDaysArray); $i++) {
		if ($longestVal < $ageInDaysArray[$i]) {
			$longestVal = $ageInDaysArray[$i];
		}
	} // end for loop

	//outputs content for vertical histogram table
	for ($i = 0; $i < $longestVal; $i++) {
		echo "<tr>";

		//outputs all cells and determines which ones will have a star
		for ($j = 0; $j < count($ageInDaysArray); $j++) {
			if ($ageInDaysArray[$j] >= ($longestVal - $i)) {
				echo "<td class=\"star\">*</td>";
			}
			else {
				echo "<td></td>";
			}
		} // end inner for loop

		echo "</tr>";

	} // end outer for loop

	//displays last row with the age in days, which is split into individual cells per number in the age
	echo "<tr>";
	for ($i = 0; $i < count($ageInDaysArray); $i++) {
		echo "<td class=\"num\">", $ageInDaysArray[$i], "</td>";
	}

	echo "</tr></table><br /><br />";
	//end displaying vertical table
?>
	</div>
</body>
</html>