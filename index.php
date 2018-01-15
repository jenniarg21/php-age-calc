<?php
	define("BIRTHDATE", "1.1.1902");
	define("FIRST_LEAP_YEAR", 1752);
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
	$birthDate["month"] = $birthArray[0];
	$birthDate["day"] = $birthArray[1];
	$birthDate["year"] = $birthArray[2];

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
	$endDate["month"] = $dateArray[0];
	$endDate["day"] = $dateArray[1];
	$endDate["year"] = $dateArray[2];

/*
** Function Name: calcLeapYears
** Description: calculates the number of leap years that occur between two years
** Parameters: 
		(1) {int} startYear - the starting year
		(2) {int} endYear - the ending year
** Return Value: {int} res - the total amount of leap years that occurred
*/
function calcLeapYears($startYear, $endYear) {
	$res = 0;

	//account and adjust for birth year occurring prior to first leap year
	if ($startYear < FIRST_LEAP_YEAR && $endYear > FIRST_LEAP_YEAR)
		$res = 1;

	//ignore years occurring before first leap year
	if ($startYear < FIRST_LEAP_YEAR)
		$startYear = FIRST_LEAP_YEAR;

	//calculate number of leap years between the two dates as well as account for skipped leap years for every 100 years excluding years that are divisible by 400, i.e. 1600 and 2000 are leap years but 1700, 1800, and 1900 are not
	$skippedLeapYears = 0;
	while ($startYear < $endYear) {
		if ($startYear % 100 == 0 && $startYear % 400 != 0)
			$skippedLeapYears++;
		if ($startYear % 4 == 0)
			$res++;
		$startYear++;
	}

	return $res - $skippedLeapYears;
}

/*
** Function Name: pluralNum
** Description: finds out if the number should be described as plural or not
** Parameters: 
		(1) {int} num - the number
** Return Value: {bool} true if plural, false if not
*/
function pluralNum($num) {
	if ($num == 1)
		return 0;
	else
		return 1;
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
		<p>Calculate how old you are in days. This calculator accounts for leap years. For fun, your age will be displayed in two histogram charts.</p>
<?php
	//dates chosen set as date types
	$date = mktime(0, 0, 0, $endDate["month"], $endDate["day"], $endDate["year"]);
	$bday = mktime(0, 0, 0, $birthDate["month"], $birthDate["day"], $birthDate["year"]);
	$startDate = date("F jS", $bday);

	if (date("Y-m-d", $bday) > date("Y-m-d", $date)) {
		echo "<p class=\"error\">Uh-oh! The Date of Birth occurs after the Age at Date. Change either date to calculate a valid age.</p>";
	}
?>

	<input type="checkbox" id="bdayToggle" />
	<div class="dateInfo">
		<span class="bold">Date of Birth:</span> <?php echo $startDate . ", " . $birthDate["year"]; ?>
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
	$dateFirstOfMon = mktime(0, 0, 0, $endDate["month"], 1, $endDate["year"]);
	$curDay = date("l", $dateFirstOfMon);

	$daysOfWeek = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");

	echo "<h3>" . date("F Y", $date) . "</h3>";

	//link for previous year
	if ($endDate["year"] <= FIRST_YEAR) {
		echo "<p class=\"calMonYearLinks\"><span class=\"deco error\">***Attention: Calendar years before ", FIRST_YEAR, " is not available.***</span><br /><a href=\"#linkNav\" onclick=\"tooLow()\">", ($endDate["year"]-1), "</a> | "; //no link available before 1970; uses onclick attribute to call tooLow() function
	}
	else {
		echo "<p class=\"calMonYearLinks\"><a href=\"index.php?year=", ($endDate["year"]-1), "&month=", $endDate["month"], "\">&laquo; ", ($endDate["year"]-1), "</a> | "; //previous year
	}

	//links for months
	for ($i = 1; $i < 13; $i++) {
		$mon = date("M", mktime(0, 0, 0, $i, 1, $endDate["year"]));
		echo "<a href=\"index.php?year=". $endDate["year"]. "&month=". $i. "\">" . $mon . "</a> | ";
	}

	//link for following year
	if ($endDate["year"]+1 >= LAST_YEAR) {
		echo "<a href=\"#linkNav\" onclick=\"tooHigh()\">", ($endDate["year"]+1), "</a><br /><span class=\"bold\">***</span><span class=\"deco\">Attention:</span>  Calendar year ", LAST_YEAR, " and after are not available.<span class=\"bold\">***</span></p>"; //no link available after 2038; uses onclick attribute to call tooHigh() function
	}
	else {
		echo "<a href=\"index.php?year=", ($endDate["year"]+1), "&month=", $endDate["month"], "\">", ($endDate["year"]+1), " &raquo;</a></p>"; //following year
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
					echo "<td><a href=\"index.php?year=". $endDate["year"]. "&month=". $endDate["month"]. "&day=". $dayCounter. "\" class=\"day\"";
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
	while (checkdate($endDate["month"], $dayCounter, $endDate["year"])) {
		if (($dayCounter + $trailing) % 7 == 1) { //inserts row after 7 days
			echo "</tr><tr>";
		}

		echo "<td><a href=\"index.php?year=". $endDate["year"]. "&month=". $endDate["month"]. "&day=". $dayCounter. "\" class=\"day\"";
		if ($dayCounter == $choosenDay) {
			echo " id=\"choosenDay\"";
		}
		echo ">". $dayCounter. "</td>"; //individual cell for day
		$dayCounter++;
	} //end while loop

	echo "</tr></table>";
	echo "</div>";


	/* calculate age in days from birthdate and end date */
	$leapYears = calcLeapYears($birthDate["year"], $endDate["year"]);
	$interval = date_diff(new DateTime($birthDate["year"] . "-" . $birthDate["month"] . "-" . $birthDate["day"]), new DateTime($endDate["year"] . "-" . $endDate["month"] . "-" . $endDate["day"]));
	$ageInDays = $interval->format("%a");
	$age["years"] = $interval->format("%y");
	$age["months"] = $interval->format("%m");
	$age["days"] = $interval->format("%d");
	echo "<p><span class=\"bold\">Age:</span> " . number_format($ageInDays);
	echo (pluralNum($ageInDays) ? " days" : " day");
	echo "</p><p>Or ";
	echo $age["years"];
	echo (pluralNum($age["years"]) ? " years" : " year");
	echo ", " . $age["months"];
	echo (pluralNum($age["months"]) ? " months" : " month");
	echo ", and " . $age["days"];
	echo (pluralNum($age["days"]) ? " days" : " day");
	echo "<br />And lived through ";
	echo $leapYears;
	echo (pluralNum($leapYears) ? " leap years" : " leap year");
	echo "</p>";

	/* control structures used to display hisotgram tables */
	$ageInDaysArray; //array to hold individual numbers in age
	//creates an array out of the age in days
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