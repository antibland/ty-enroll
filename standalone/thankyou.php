<?php include 'include.php';

echo '<link rel="stylesheet" href="/css/thankyou.css" />';

echo '<link rel="preconnect" href="https://fonts.gstatic.com">';
echo '<link href="https://fonts.googleapis.com/css2?family=Graduate&display=swap" rel="stylesheet">';

echo '<link rel="preconnect" href="https://fonts.gstatic.com">';
echo '<link href="https://fonts.googleapis.com/css2?family=Rock+Salt&display=swap" rel="stylesheet">';

echo '<div class="thankyou">';

echo '<div class="container">';

echo $html->h1('THANK YOU!!!');

echo '<div class="image-link-container">';
echo $html->image('/images/techie-youth-logo-large-transparent.png');

echo $html->h2($html->link('/enroll/register', 'Continue to <span>Course Registration</span>'), array('class' => 'continue-to-registration'));
echo '</div>'; # end .image-link-container

$linkTarget = array('target' => '_blank');
echo $html->div($html->link('https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fenroll.techieyouth.org&', '<span>Facebook</span>', $linkTarget)
              . $html->link('https://twitter.com/intent/tweet?url=https%3A%2F%2Fenroll.techieyouth.org&text=Learn%20How%20to%20Earn%20Money%20Online%20From%20Anywhere%20in%20the%20World%3A&via=techieyouth', '<span>Twitter</span>', $linkTarget), array('class' => 'social'));

echo '</div>'; # end .container
echo '</div>'; # end .thankyou