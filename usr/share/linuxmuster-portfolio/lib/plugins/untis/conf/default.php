<?php
/**
* Options for untis-plugin
*/

$conf['debug'] = 0;
$conf['planinputdir'] = "untis";
$conf['max_days_in_future'] = 90;
$conf['show_max_days'] = 4;
$conf['image_path'] = "../../plugins/untis/img/";

// Die Untis Feldnamen haben Klammern und anderes, darum mappe ich sie auf einfachere 
// Namen, die von showfields referenziert werden
$conf['untisfieldmapping'] = 'Vtr-Nr.=>vtrnr,
Art=>art,
Fach=>fach,
Vertr.von=>vertrvon,
Stunde=>stunde,
(Le.)nach=>lenach,
(Fach)=>fachalt,
(Lehrer)=>lehreralt,
Vertreter=>kuerzel,
(Klasse(n))=>klassenalt,
Klasse(n)=>klasse,
(Raum)=>raumalt,
Raum=>raum,
Vertretungs-Text=>text';


// All available fields
// You can comment in/out needed/unneeded fields
// 
// This seems to make sense > fieldname      |  tableheading <

$conf['showfields'] = 'kuerzel=>Lehrer,
stunde=>Std.,
art=>Was?,
klasse=>Klasse,
fach=>Fach,
raum=>Raumneu,
//vtrnr=>VertrNr.,
//vertrvon=>VertrVon,
//lenach=>LeNach,
//klassenalt=>Klassealt,
raumalt=>Raumalt,
lehreralt=>Für,
fachalt=>,
text=>Bemerkungen ';


$conf['substitutions'] = 'Entfall => fa.gif|f.a. , 
Raum-Vtr. => ra.gif|RÄ, 
Betreuung => zusatz.gif|Betr.,
Pausenaufsicht => aufsicht.gif|Aufs.,
Vertretung => zusatz.gif|Vertr.';


