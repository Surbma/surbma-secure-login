<?php
// A File-nak közvetlen hozzáférés tiltása
if (!function_exists('add_action')) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}
?>