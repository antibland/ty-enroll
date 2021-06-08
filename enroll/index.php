<?php
define('DEBUG_MODE', false);
class get {
        public static function htmlentities($string, $quoteStyle = ENT_COMPAT, $charset = 'UTF-8', $doubleEncode = false) {
        return htmlentities($string, (!is_null($quoteStyle) ? $quoteStyle : ENT_COMPAT),
                            (!is_null($charset) ? $charset : 'UTF-8'), $doubleEncode);
    }
}
include 'html.php';
include 'form.php';
include 'tools.php';
$html = new htmlHelper;
$form = new formHelper;
$tools = new toolsHelper;

echo '<link rel="preconnect" href="https://fonts.gstatic.com" />';
echo '<link
  href="https://fonts.googleapis.com/css2?family=Alfa+Slab+One&display=swap"
  rel="stylesheet"
/>';

echo '<link rel="preconnect" href="https://fonts.gstatic.com" />';
echo '<link
  href="https://fonts.googleapis.com/css2?family=Abel&display=swap"
  rel="stylesheet"
/>';
echo '<link rel="stylesheet" href="../css/enroll-index.css" />';

echo '<div id="layout">'; # Start Layout

echo '<header>';
echo $html->image('/images/techieyouth-logo-black.png');
echo $html->h1('Techie Youth');
echo $html->h1('Learn to E-Earn');
echo $html->h2('How to Earn Money Online From Anywhere');
echo '</header>';

echo '<section>';

echo '<div class="flex-row enroll-1">';

echo '<div class="flex-col flex-col-8-12">';
echo $tools->youtube('__lG--A3dJI');
echo '</div>';

echo '<div class="flex-col flex-col-4-12">';

echo $html->h3('FREE Online Education & Certificate Program for Youth');
echo $html->p('In response to the COVID-19 pandemic, Techie Youth, a 501(c)3 not-for-profit registered charity of New York City, is empowering youth worldwide to earn immediate income from wherever they are, even during social distancing and/or a stay-home act.');

echo $form->open();
#echo $html->div($form->input(array('name' => 'email')));
echo '<input type="email" required aria-label="Enter email address" placeholder="Your Email Address here…" name="email" />';
echo $html->div($form->submit('Enroll to attend NOW!'));
echo $html->div($html->link('/earn', 'Already signed up? Go log in now!'));
echo $form->close();

echo '</div>';

echo '</div>';

echo '</section>';

echo '<section class="full-width dark">';

echo '<div class="flex-row">';

echo '<div class="flex-col flex-col-6-12">';
echo $html->h2('Who is this program for?');
echo $html->p('This is open to any youth who is interested in becoming financially independent and empowered with skills to earn income from anywhere.');
echo $html->p('No prior knowledge or experience is needed. If you can read this and understand the spoken-English in these videos then you have all the requirements. :)');
echo $tools->youtube('GANlQdmMDrw');
echo '</div>';

echo '<div class="flex-col flex-col-6-12">';
echo $tools->youtube('sYuJ5HwmOPo');
echo $html->h2('Why is it free?');
echo $html->p('Techie Youth is a donation-funded charity that has been providing totally-free tech-education to hundreds of NYC foster kids and at-risk youth since 2015; our classroom-based program was disrupted by the pandemic preventing large-gatherings, so we are making lemonade from the lemon-situation by transitioning our program into a free online platform available to all youth worldwide.');
echo '</div>';

echo '</div>';
echo '</section>';


echo '<section class="full-width">';

echo '<div class="flex-row with-separator">';

echo '<div class="flex-col flex-col-6-12">';
echo $html->h2('What is the online-course like?');
echo $html->p('Expect lots of fun and inspiration! Much of the curriculum is video-based with interactive facets throughout; human-help is always available if needed. Some of the content is gamified, so <strong>if you like playing video games then you will love those topics.</strong>');
echo $html->p('The program uses an accelerated-learning format covering content <strong>equivalent to 10 college credits</strong>, and is open to youth of all grades, from age 13 and up!');
echo $html->p('Each student learns at their own pace and schedule, taking as much or as little time as needed, but most will complete the program in about 6-weeks, spending 3-5 hours per day.');
echo '</div>';

echo '<div class="flex-col flex-col-6-12">';
echo $tools->youtube('dwW0qHEjQLU');
echo '</div>';

echo '</div>'; # end "flex-row"

echo '<div class="flex-row">';

echo '<div class="flex-col flex-col-6-12">';
echo $html->p('The curriculum is focused on teaching you how to earn income online, from wherever you are; it is divided into three sections:');
echo $html->linkList(array(
    '<strong>Making immediate money</strong> from your computer, right now',
    '<strong>Working remotely</strong> as a consultant, employee or part of a team',
    '<strong>Internet entrepreneurship</strong> and online businesses',
));
echo '</div>';

echo '<div class="flex-col flex-col-6-12">';
echo $html->p('We will also teach you ways to live well and improve the quality of your life within the financial means that you already have:');
echo $html->linkList(array(
    'Earning income from <strong>luxurious lifestyles</strong> and enjoying fun experiences',
    'Maximizing productivity to <strong>increase your happiness</strong> and life satisfaction',
    'Managing money you earn, so <strong>your money makes you more money</strong>',
));
echo '</div>';

echo '</div>'; # end "flex-row"

echo '</section>';

echo '<section class="full-width enroll-2">';

echo '<div class="flex-row">';

echo '<div class="flex-col flex-col-12-12">';

echo $html->h2('100% FREE for youth');
echo $html->p('Do not miss this opportunity to learn how to be financially independent, earning income with your computer from your home, the beach, or anywhere that you want to go.');

echo $form->open();
#echo $html->div($form->input(array('name' => 'email')));
echo '<input type="email" required aria-label="Enter email address" placeholder="Your Email Address here…" name="email" />';
echo $html->div($form->submit('Enroll to attend NOW!'));
echo $html->div($html->link('/earn', 'Already signed up? Go log in now!'));
echo $form->close();

echo '<div class="bottom">';
echo $html->h3('What happens after I finish this program?');
echo $html->p('Upon graduation you will earn a Techie Youth certificate of completion!');
echo $html->p('Our graduates are provided with ongoing access to our Techie Youth career assistance resources, ensuring that you will have support in your money-making endeavors.');

$linkTarget = array('target' => '_blank');
echo $html->div($html->link('https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fenroll.techieyouth.org&', '<span>Facebook</span>', $linkTarget)
              . $html->link('https://twitter.com/intent/tweet?url=https%3A%2F%2Fenroll.techieyouth.org&text=Learn%20How%20to%20Earn%20Money%20Online%20From%20Anywhere%20in%20the%20World%3A&via=techieyouth', '<span>Twitter</span>', $linkTarget), array('class' => 'social'));

echo $html->div($html->link('/tos', 'Techie Youth privacy policy &amp; terms of service', $linkTarget));

echo '</div>'; # end bottom

echo '</div>'; # end flex-col

echo '</div>'; # end flex-row

echo '</section>';

echo '<script>document.body.classList.add("enroll");</script>';

echo '</div>'; # End layout