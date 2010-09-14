#!/bin/bash

# ospversion.php erzeugen
OSPVERSION=../usr/share/linuxmuster-portfolio/lib/tpl/portfolio/ospversion.php
VERSION=`head -n 1 changelog  | awk '{print $2}' | sed 's/(//' | sed 's/)//'`
echo "<?php" > $OSPVERSION
echo -n 'print "' >> $OSPVERSION
echo -n $VERSION >> $OSPVERSION
echo  '";' >> $OSPVERSION

#Patchen
export QUILT_PATCHES=debian/patches
quilt --quiltrc /dev/null push -a

cd ..
svn-buildpackage --svn-ignore-new
cd debian

quilt --quiltrc /dev/null pop -a
