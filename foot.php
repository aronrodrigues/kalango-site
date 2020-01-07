<?php
// Link das variaveis com as constantes.
$template->parse("{HEAD}","head");
$template->parse("{MENU}","menu");
$template->parse("{CONTENT}","content");
$template->parse("{FOOT}","foot");
$template->parse("OUTPUT","main");

$template->FastPrint("OUTPUT");
?>
