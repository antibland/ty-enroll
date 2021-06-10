<?php include 'include.php';

echo '<link rel="preconnect" href="https://fonts.gstatic.com">';
echo '<link href="https://fonts.googleapis.com/css2?family=Fjalla+One&display=swap" rel="stylesheet">';

echo '<link rel="preconnect" href="https://fonts.gstatic.com">';
echo '<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300&display=swap" rel="stylesheet">';

echo '<link rel="stylesheet" href="/css/completed.css" />';

echo '<div class="completed-wrapper">';
echo '<div class="container">';

echo $html->image('/images/techieyouth-logo-black.png');

echo $html->h1('CONGRATULATIONS!');
echo $html->h2('You are registered for ' . $html->link('/earn', 'Techie Youth Learn to E-Earn!'));

echo $html->div('You have completed the first step towards creating an amazing financial future for yourself!', array('class' => 'you-have-completed'));
echo $html->div('NEXT: Join the inner-circle of influencers by sharing your achievement to Facebook and Twitter:', array('class' => 'inner-circle'));
$linkTarget = array('target' => '_blank');
echo $html->div($html->link('https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fenroll.techieyouth.org&', '<span>Facebook</span>', $linkTarget)
              . $html->link('https://twitter.com/intent/tweet?url=https%3A%2F%2Fenroll.techieyouth.org&text=Learn%20How%20to%20Earn%20Money%20Online%20From%20Anywhere%20in%20the%20World%3A&via=techieyouth', '<span>Twitter</span>', $linkTarget), array('class' => 'social'));

echo $html->div('<p>Next, check your email - we just sent you your login credentials.</p><p>Once you have that, ' . $html->link('/earn', 'click here to log in and begin your income-earning journey!</p>'), array('class' => 'next-steps'));

echo '</div>'; # end .container
echo '</div>'; # end .completed-wrapper
