<?php
/**
 * Plaintext authentication backend
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Andreas Gohr <andi@splitbrain.org>
 * @author     Chris Smith <chris@jalakai.co.uk>
 * @author     Kenneth Yrke Joergensen <mail@yrke.dk>
 */
print "Hallo";
$imap_login = imap_open("{albert.aeg-reutlingen.de:993/imap/ssl/novalidate-cert}", "schiebel", "prvga05", OP_HALFOPEN, 1);
print "Hallo";
?>
