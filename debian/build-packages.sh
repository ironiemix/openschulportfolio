#!/bin/bash

opt=$1

if [ -z $opt ]; then 
 opt="deb"
fi

WDIR=`pwd`
SCHUQWIKIDIR="${WDIR}/schuqwiki/"
WDIR=`basename $WDIR`
if [ $WDIR != "debian" ]; then
echo "build-Script muss im debian Paketverzeichnis ausgeführt werden"
exit 1
fi


BUILDDIR="linuxmuster-portfolio"

# clean up
rm -rf $BUILDDIR


# ospversion.php erzeugen und Version hineinschreiben
OSPVERSION=../usr/share/linuxmuster-portfolio/lib/tpl/portfolio2/ospversion.php
VERSION=`head -n 1 changelog  | awk '{print $2}' | sed 's/(//' | sed 's/)//'`
echo "<?php" > $OSPVERSION
echo -n 'print "' >> $OSPVERSION
echo -n $VERSION >> $OSPVERSION
echo  '";' >> $OSPVERSION

# Paketname ermitteln
PACKAGENAME=`head -n 1 changelog  | awk '{print $1}'`
SOURCEVERSION=`echo $VERSION | awk -F- '{print $1}'`
SOURCE="."

# Statusmeldung
echo "Paketname:        $PACKAGENAME"
echo "Working-Dir:      $WDIR"
echo "Tar-Source-Dir:   $SOURCE"
echo "Tar-Build-Dir:    $BUILDDIR"
echo "Version:          $VERSION"
echo "Source-Version:   $SOURCEVERSION"
sleep 5
# Anpassungen einpatchen
export QUILT_PATCHES=debian/patches

quilt --quiltrc /dev/null pop -a
PATCHFAIL="NO"
quilt --quiltrc /dev/null push -a || PATCHFAIL="FAILED"

if [ $PATCHFAIL = "FAILED" ]; then
    echo "******************* Patching failed! **************************************"
    quilt --quiltrc /dev/null pop -a
    exit 1
fi
# debian Paket bauen
cd ..
dpkg-buildpackage -i\.git -I.git
cd debian

# Patches entfernen
quilt --quiltrc /dev/null pop -a


if [ $opt = "zip" ]; then 


# Ins Builddir wechseln
cd $BUILDDIR
# conf und data Verzeichnis anlegen
mkdir -p portfolio/conf > /dev/null 2>&1
mkdir portfolio/data > /dev/null 2>&1
mkdir portfolio/data/media_meta > /dev/null 2>&1
mkdir portfolio/data/media_attic > /dev/null 2>&1

# copying distributed files from package in one documentroot
cp -r $SOURCE/usr/share/linuxmuster-portfolio/*  portfolio/
cp -r $SOURCE/etc/linuxmuster-portfolio/*  portfolio/conf/
#mv portfolio/conf/user  portfolio/lib/tpl/portfolio/
cp -r $SOURCE/home/linuxmuster-portfolio/* portfolio/
cp -r $SOURCE/var/lib/linuxmuster-portfolio/data/* portfolio/data/
cp -r $SOURCE/var/lib/linuxmuster-portfolio/help/* portfolio/data/

# removing obsolete files
rm portfolio/conf/apache2.conf
rm portfolio/install.php

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

# building full zip package for OSP
echo -n "Zipping full package for OSP..."
zip -qr openschulportfolio-${VERSION}-full.zip portfolio/
echo "   done."
# building system update zip package OSP
echo -n "Zipping update package for OSP..."
zip -qr openschulportfolio-${VERSION}-update.zip portfolio/* -x portfolio/data/\* -x portfolio/conf/\* -x portfolio/lib/tpl/portfolio/user/\*
zip -qu openschulportfolio-${VERSION}-update.zip portfolio/data/media_attic
zip -qu openschulportfolio-${VERSION}-update.zip portfolio/data/media_meta
echo "   done."

# modifiyng for schuqwiki
#cp -r portfolio/lib/tpl/portfolio/ portfolio/lib/tpl/schuqwiki
#cp -r ${SCHUQWIKIDIR}/* portfolio/lib/tpl/schuqwiki/
#cp -r ${SCHUQWIKIDIR}/../schuqwiki.credits portfolio/data/pages/wiki/credits.txt
#rm portfolio/lib/tpl/schuqwiki/user/.htaccess 

#sed -i "s/conf\['template'\].*/conf\['template'\] = 'schuqwiki';/" portfolio/conf/local.php 
## building full zip package for SQW
#echo -n "Zipping full package for SQW..."
#zip -qr schu-q-wiki-${VERSION}-full.zip portfolio/
#echo "   done."
# building system update zip package SQW
#echo -n "Zipping update package for SQW..."
#zip -qr schu-q-wiki-${VERSION}-update.zip portfolio/* -x portfolio/data/\* -x portfolio/conf/\* -x portfolio/lib/tpl/portfolio/user/\*
#zip -qur schu-q-wiki-${VERSION}-update.zip  portfolio/data/pages/bookcreator/
#echo "   done."
mv *.zip ../../
fi


