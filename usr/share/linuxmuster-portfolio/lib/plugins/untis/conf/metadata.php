<?php
/**
 * Metadata for configuration manager plugin
 * Additions for the untis plugin
 *
 * @author Frank Schiebel <frank@linuxmuster.net>
 */


$meta['_basic']  = array('fieldset');
$meta['debug'] = array('onoff');
$meta['planinputdir'] = array('string');
$meta['substitutions']  = array('');
$meta['max_days_in_future'] = array('numeric', '_pattern' => '/[0-9]*/');
$meta['show_max_days'] = array('numeric', '_pattern' => '/[0-9]*/');

$meta['_extended']  = array('fieldset');
$meta['image_path'] = array('string');
$meta['untisfieldmapping'] = array('');
$meta['showfields'] = array('');
