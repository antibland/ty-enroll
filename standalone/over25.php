<?php include 'include.php';
echo '<link rel="preconnect" href="https://fonts.gstatic.com">';
echo '<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;700&display=swap" rel="stylesheet">';
echo '<link rel="stylesheet" href="/css/over25.css" />';

echo '<div class="over25-wrapper">';
echo '<header>';

echo '<div class="header-content">';

echo $html->h4('Wait! Your registration is not yet complete.');
echo '<div class="progress-bar">';
echo $html->div('Step 2 of 3...', array('class' => 'progress-bar-inner'));
echo '</div>';

echo '</div>';

echo '</header>';

echo '<main>';

echo '<div class="top-section">';
echo '<section class="left-section">';

echo $html->h2('Support our youth');
echo $html->h1('Help keep this FREE for youth');
echo $html->p('This program is funded by donations from generous people like you.');

echo '</section>';

echo '<section class="right-section">';

echo $html->h3('Meet the students of Techie Youth');
echo '<div class="vid-wrap">';
echo get::helper('tools')->youtube('6rxLoMhNVvw');
echo '</div>';

echo '</section>';

echo '</div>'; #end .top-section

echo '<div class="bottom-section">';

echo '<div class="block">';
echo $html->p('Adults beyond age 24 are expected to provide a supportive donation in order to participate in this program, so we can keep it free for youth.');
echo '</div>';

echo '<div class="block">';
echo $html->div($html->link('https://www.paypal.com/donate?hosted_button_id=NVNY4SDW5GVSW', "YES, I'd like to donate $197 and enroll in the course"), array('class' => 'like-to-donate'));
echo '</div>';

echo '<div class="block">';

echo $html->div($html->link('https://www.paypal.com/donate?hosted_button_id=L2S47Y5T95LHW', "I would like to donate a different amount or set up monthly-donations"), array('class' => 'other-donation'));
echo $html->p('Monthly donations are very beneficial to our charity as they provide us with a predictable source of funding.');


echo $html->div("I'd like to help by sharing this", array('class' => 'social-sharing'));
$linkTarget = array('target' => '_blank');
echo $html->div($html->link('https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fenroll.techieyouth.org&', '<span>Facebook</span>', $linkTarget)
              . $html->link('https://twitter.com/intent/tweet?url=https%3A%2F%2Fenroll.techieyouth.org&text=Learn%20How%20to%20Earn%20Money%20Online%20From%20Anywhere%20in%20the%20World%3A&via=techieyouth', '<span>Twitter</span>', $linkTarget), array('class' => 'social-buttons'));
echo '</div>';

echo '</div>'; # end .bottom-section


echo '</main>';
echo '</div>';