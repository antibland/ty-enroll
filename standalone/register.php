<?php include 'include.php';
echo '<link rel="stylesheet" href="/css/register.css" />';

echo '<div class="container register">';
echo '<section class="left-section">';

echo $html->image('/images/techieyouth-logo-white-big.png');

echo $html->h1('Techie Youth');
echo $html->h2('Learn to E-Earn');
echo $html->h3('How to Earn Money Online From Anywhere');
echo $html->div('100% FREE Online Education &amp; Certificate Program for Youth', array('class' => 'free-callout'));
echo '<hr class="separator">';
echo '<div id="timer"></div>';
echo $html->jsInline('
var timer = document.getElementById("timer");
timer.innerHTML = "<div class=\"time-remaining-text\">Time remaining to send <span>in your registration:</span></div><div id=\"countdown\"></div>";
var countdown = document.getElementById("countdown");
var min = 43; //starting time
var sec = 12;
var decrement = function() {
    sec--;
    if (sec < 0) {
        sec = 59;
        min--;
        if (min < 0) { //time has finished
            clearInterval(decrementer);
            timer.innerHTML = "";
        }
    }
    countdown.innerHTML = min + ":" + String(sec).padStart(2, "0");
}
decrement();
var decrementer = setInterval(decrement, 1000);
');

echo $html->div($html->link('/tos', 'Techie Youth privacy policy &amp; terms of service', array('target' => '_blank', 'class' => 'tos')));

echo '</section>';

echo '<section class="right-section">';

echo $form->open();
echo $html->div('Register to attend for FREE now:');

$args = array('name' => 'first', 'label' => 'First name');
echo $html->div($form->input($args));

$args = array('name' => 'last', 'label' => 'Last name');
echo $html->div($form->input($args));

$args = array('name' => 'email', 'label' => 'Email', 'value' => (isset($_SESSION['email']) ? $_SESSION['email'] : ''));
echo $html->div($form->email($args));

$args = array('name' => 'phone', 'label' => 'Telephone');
echo $html->div($form->input($args));

$form->checkboxes(array('name' => 'sms-opt-in', 'options' => array(1 => 'Yes, send me important alerts via SMS/text (optional)')));
echo $html->div(current($form->checkboxes));

$args = array('name' => 'address', 'label' => 'Address');
echo $html->div($form->input($args));

$args = array('name' => 'city', 'label' => 'city');
echo $html->div($form->input($args));

$args = array('name' => 'state', 'label' => 'State/province');
echo $html->div($form->input($args));

$args = array('name' => 'zip', 'label' => 'Zip code');
echo $html->div($form->input($args));

$countries = get::helper('data')->countries();
$countries = array_merge(array('US' => $countries['US']), $countries);
echo $html->div($form->select(array('label' => 'Country', 'name' => 'country', 'options' => $countries, 'prependBlank' => true)));

echo '<fieldset><legend>Birthday</legend>';
echo $html->div($form->select(array('label' => '', 'name' => 'dob-month', 'options' => get::component('data')->months()))
    . $form->select(array('label' => '', 'name' => 'dob-day', 'options' => range(1, 31), 'addBreak' => false))
    . $form->select(array('label' => '', 'name' => 'dob-year', 'options' => range((date('Y') - 12), (date('Y') - 99)), 'addBreak' => false))
);
echo '</fieldset>';

$genders = array('m' => 'Male', 'f' => 'Female', 'o' => 'Other/something-else/none');
echo $html->div($form->select(array('name' => 'gender', 'label' => 'Gender (optional)', 'options' => $genders, 'prependBlank' => true)));

echo $html->div($form->radios(array('name' => 'prior-ty-student', 'label' => 'Have you previously attended Techie Youth in-person, commuting to the classroom?', 'options' => array(true => 'Yes', false => 'No (or unsure)'))));

$args = array('name' => 'referrer', 'label' => 'Where did you hear about this program?');
echo $html->div($form->input($args));

$args = array('name' => 'syep', 'label' => 'If you are participating in Techie Youth through a youth employment program such as SYEP, what is the name of your program provider?');
echo $html->div($form->input($args));

//$vyOptions = get::$config->vyOptions;
$vyOptions = get::$vyOptions;
$select = array('label' => 'Check all that apply to you:', 'name' => 'vy', 'options' => $vyOptions);
echo $html->div($form->checkboxes($select));
echo $html->div($form->textarea(array('name' => 'vyexplain', 'label' => 'Provide relevant details about all checked above')));

$args = array('name' => 'foster-agency', 'label' => 'Foster care agency (if applicable)');
echo $html->div($form->input($args));

$args = array('name' => 'school', 'label' => 'Name of current or last school');
echo $html->div($form->input($args));

$args = array('name' => 'grade', 'label' => 'Current grade (if in school) or last grade completed');
echo $html->div($form->input($args));

echo $html->div('If you are under age-18, enter below the info for your parent or legal-guardian. <strong>Applicants who do not have a parent, guardian or foster parent:</strong> please provide the contact info of an adult who will always be able to contact you, such as a caseworker.');

$args = array('name' => 'guardian-name', 'label' => 'Parent/guardian name');
echo $html->div($form->input($args));

$args = array('name' => 'guardian-email', 'label' => 'Parent/guardian email address');
echo $html->div($form->email($args));

$args = array('name' => 'guardian-phone', 'label' => 'Parent/guardian phone number');
echo $html->div($form->input($args));

echo $form->submit('Submit My Registration Now');
echo $form->close();

echo '</section>';
echo '</div>';