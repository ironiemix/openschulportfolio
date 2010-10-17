#!/bin/bash

WDIR=`pwd`
WDIR=`basename $WDIR`
if [ $WDIR != "debian" ]; then
echo "build-Script muss im debian Paketverzeichnis ausgeführt werden"
exit 1
fi

TDIR="../../build-area/"
BUILDDIR="tarbuild"


# ospversion.php erzeugen und Version hineinschreiben
OSPVERSION=../usr/share/linuxmuster-portfolio/lib/tpl/portfolio/ospversion.php
VERSION=`head -n 1 changelog  | awk '{print $2}' | sed 's/(//' | sed 's/)//'`
echo "<?php" > $OSPVERSION
echo -n 'print "' >> $OSPVERSION
echo -n $VERSION >> $OSPVERSION
echo  '";' >> $OSPVERSION

# Paketname ermitteln
PACKAGENAME=`head -n 1 changelog  | awk '{print $1}'`
TARFILE=${PACKAGENAME}_${VERSION}.tar.gz
SOURCEVERSION=`echo $VERSION | awk -F- '{print $1}'`
SOURCE=${PACKAGENAME}-${SOURCEVERSION}

# Statusmeldung
echo "Paketname:        $PACKAGENAME"
echo "Working-Dir:      $WDIR"
echo "Build-Target-Dir: $TDIR"
echo "Tar-Source-File:  $TARFILE"
echo "Tar-Source-Dir:   $SOURCE"
echo "Tar-Build-Dir:    $BUILDDIR"
echo "Version:          $VERSION"
echo "Source-Version:   $SOURCEVERSION"


sleep 5

# Anpassungen einpatchen
export QUILT_PATCHES=debian/patches
quilt --quiltrc /dev/null push -a

# debian Paket bauen
cd ..
svn-buildpackage --svn-ignore-new
cd debian

# Patches entfernen
quilt --quiltrc /dev/null pop -a

# TAR Pakete erzeugen 

# Nach TDIR wechseln
cd $TDIR

# checking
if [ -d $BUILDDIR ]; then 
rm -rf $BUILDDIR
fi

if [ ! -f $TARFILE ]; then 
echo "Unable to open source tarball $TARFILE"
exit 1
fi

# Builddir anlegen
mkdir $BUILDDIR > /dev/null 2>&1
# Quellen auspacken
tar -C $BUILDDIR -xzf $TARFILE
# Ins Builddir wechseln
cd $BUILDDIR
# conf und data Verzeichnis anlegen
mkdir -p portfolio/conf > /dev/null 2>&1
mkdir portfolio/data > /dev/null 2>&1


# copying distributed files from package in one documentroot
cp -r $SOURCE/usr/share/linuxmuster-portfolio/*  portfolio/
cp -r $SOURCE/etc/linuxmuster-portfolio/*  portfolio/conf/
mv portfolio/conf/user  portfolio/lib/tpl/portfolio/
cp -r $SOURCE/home/linuxmuster-portfolio/* portfolio/
cp -r $SOURCE/var/lib/linuxmuster-portfolio/data/* portfolio/data/
cp -r $SOURCE/var/lib/linuxmuster-portfolio/help/* portfolio/data/

# Angepasste Startseite und Co ins OSP Versichnis
# TODO


# removing obsolete files
rm portfolio/conf/apache2.conf

# removing preload script
rm portfolio/inc/preload.php

# adjusting configuration - common changes

##############################################
# Standalone Version - own usermanagement    #
##############################################

# removing ldap auth entrys
sed -i "/conf\['auth'\]\['ldap'\]/d" portfolio/conf/local.php
sed -i "/conf\['authtype'\]/d" portfolio/conf/local.php
sed -i "/conf\['savedir'\]/d" portfolio/conf/local.php
sed -i "/conf\['userewrite'\]/d" portfolio/conf/local.php
sed -i "/conf\['plugin'\]\['filelist'\]\['allowed_absolute_paths'\]/d" portfolio/conf/local.php
sed -i "s/conf\['disableactions'\].*/conf\['disableactions'\] = 'register,resendpwd';/" portfolio/conf/local.php 
# adding administrator line in users.auth.php
# default password is ospinstall2010
echo "admin:cbf915885771cd351a7cf6629356bc2a:Portfolio Administrator:mail@portfolio.nirgendwo:portfolioadm" >> portfolio/conf/users.auth.php

# building full zip package
zip -r openschulportfolio-${VERSION}-full.zip portfolio/
# building system update zip package
zip -r openschulportfolio-${VERSION}-update.zip portfolio/* -x portfolio/data/\* -x portfolio/conf/\* -x portfolio/lib/tpl/portfolio/user/\*

mv *.zip ..


