<?php include 'include.php';
echo $html->h1('How old are you?');
echo $form->open(array('action' => '/enroll/register'));
echo $html->div($form->submit('I am between age 13 and 24'));
echo $form->close();

echo $form->open(array('action' => '/enroll/over25'));
echo $html->div($form->submit('I am age 25 or older'));
echo $form->close();