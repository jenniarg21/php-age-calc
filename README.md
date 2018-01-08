# php-age-calc
PHP page which calculates the difference in days between two dates.
Live page: http://jenniarg.com/it207/lab7

This is a modified assignment from GMU. It does the following:
  - Calculate the age in days between two dates (taking into account leap years)
  - Dynamically display information in histogram charts
  - Create a calendar in which the end user can change a date
  - Use a cookie to store the date of birth

Calculating the age in days was the difficulest task in the assignment. There's a lot to account for to get the correct result. I split up the task into 3 parts:
  1) Calculating the age in years
  2) Calculating the amount of occurring leap years between the two dates
  3) Calculating the additional amount of days that weren't accounted for in calculating the age in years

I took care of edge cases in each of these parts, such as:
  - Ensuring not to add an addtional year if the two dates occurred in the same year
  - Taking into account the first occurence of a leap year in 1904
  - Taking into account the total amount of days for every month (30 vs 31 vs 28 vs 29)
  - Taking into account when the birth month occurs in relation to the second date's month

We had to create two histogram charts which displayed the age horizontally (with heading at the left) and vertically (with heading at the bottom). We had to split the age into individual numbers. Each number would have it's own row or column depending on the projection. These charts are dynamic and the total amount of rows and columns change based on the data. Creating the vertical histogram chart was a bit tricky, but all I had to do was find out which was the largest number and use that for the max (plus one for the frequency heading) number of rows.

We also had to create a calendar which the end user could use to change the second date. I used the GET method to pass the date along. We needed to display options for the previous year, following year, months, and days within the choosen month. The calendar is dynamic and changes based on the choosen month which include:
  - Total amount of days in choosen month
  - Trailing days in the first week which are not part of the choosen month

The months and days of the week on the calendar are not hardcoded. I used for loops to write them to the page. I also added conditional styling to highlight the choosen day on the calendar--this makes it easier to tell what day you chose.

I provided the end user the ability to change the date of birth using a form. This form uses the POST method to pass the data entered. I store this data in a cookie which expires when the browser is closed.

Just to make it clear, the design choice to vary how the end user chooses a Date of Birth and Age at Date was to display my abilities--I would not keep it this way because it can confuse and frustrate the end user.
