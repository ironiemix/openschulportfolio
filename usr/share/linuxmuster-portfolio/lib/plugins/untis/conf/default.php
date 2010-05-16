<?php
/**
*Optionsfortheuntisplugin
*/

$conf['debug'] = 0;
$conf['planinputdir'] = "untis";

// Die Untis Feldnamen haben Klammern und anderes, darum mappe ich sie auf einfachere 
// Namen, die von showfields referenziert werden
$conf['untisfieldmapping'] = array( 'Vtr-Nr.'           => 'vtrnr' , 
                                    'Art'               => 'art' ,
                                    'Fach'              => 'fach' ,
                                    'Vertr.von'         => 'vertrvon' ,
                                    'Stunde'            => 'stunde' ,
                                    '(Le.)nach'         => 'lenach',
                                    '(fach)'            => 'fachalt',
                                    '(Lehrer)'          => 'lehreralt',
                                    'Vertreter'         => 'kuerzel',
                                    '(Klasse(n))'       => 'klassenalt',
                                    'Klasse(n)'         => 'klasse',
                                    '(Raum)'            => 'raumalt',
                                    'Raum'              => 'raum',
                                    'Vertretungs-Text'  => 'text');


// All available fields
// $conf['showfields'] = array('lehrer', 'kuerzel', 'art','stunde','klasse','fach','raum','vvon','lenach','lehreralt','fachalt','raumalt');
// This seems to make sense
$conf['showfields'] = array('kuerzel'   => 'Lehrer' , 
                            'stunde'    => 'Std.' ,
                            'art'       => 'Was?' ,
                            'klasse'    => 'Klasse' ,
                            'fach'      => 'Fach' ,
                            'raum'      => 'Raum neu',
                            'raumalt'      => 'Raum alt',
                            'lehreralt' => 'Für' ,
                            'fachalt'   => ''  );

$conf['substitutions'] = array('Entfall'    => '<img src="../../plugins/untis/img/fa.gif" /> f.a.' , 
                               'Raum-Vtr.' => '<img src="../../plugins/untis/img/ra.gif" /> RÄ', 
                               'Betreuung' => '<img src="../../plugins/untis/img/zusatz.gif" /> Betr.',
                               'Vertretung' => '<img src="../../plugins/untis/img/zusatz.gif" /> Vertr.' );

