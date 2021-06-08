<?php include 'include.php';
echo '<section>';

echo $html->div('Wait! Your registration is not yet complete.');
echo $html->div('Step 2 of 3...');

echo '</section>';
echo '<section>';

echo $html->div('Support our youth');
echo $html->h1('Help keep this FREE for youth');
echo $html->p('This program is funded by donations from generous people like you.');
echo $html->p('Adults beyond age 24 are expected to provide a supportive donation in order to participate in this program, so we can keep it free for youth.');

echo '</section>';
echo '<section>';

echo $html->div('Meet the students of Techie Youth');
echo get::helper('tools')->youtube('6rxLoMhNVvw');

echo $html->div($html->link('https://www.paypal.com/donate?hosted_button_id=NVNY4SDW5GVSW', "YES, I'd like to donate $197 and enroll in the course"));

echo $html->div($html->link('https://www.paypal.com/donate?hosted_button_id=L2S47Y5T95LHW', "I would like to donate a different amount or set up monthly-donations"));
echo $html->div('Monthly donations are very beneficial to our charity as they provide us with a predictable source of funding.');

echo $html->div("I'd like to help by sharing this:");
$linkTarget = array('target' => '_blank');
echo $html->div($html->link('https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fenroll.techieyouth.org&', 'Facebook', $linkTarget)
              . $html->link('https://twitter.com/intent/tweet?url=https%3A%2F%2Fenroll.techieyouth.org&text=Learn%20How%20to%20Earn%20Money%20Online%20From%20Anywhere%20in%20the%20World%3A&via=techieyouth', 'Twitter', $linkTarget));

echo '</section>';