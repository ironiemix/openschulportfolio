#!/bin/bash

DELETEFILES="dw_removeoldfiles.txt"
INCTARGET=../usr/share/linuxmuster-portfolio/
LIBTARGET=../usr/share/linuxmuster-portfolio/
BINTARGET=../home/linuxmuster-portfolio/
CONFTARGET=../etc/linuxmuster-portfolio/
ROOTTARGET=../usr/share/linuxmuster-portfolio/

if [ ! $1 ]; then 
 echo "Quellverzeichnis muss als Argument angegeben werden!"
 echo "  i.e. ./dw-upgrade.sh /root/dokuwiki-2012-01-25/ "
 exit 1
fi

DWSOURCE=$1

if [ ! -d $DWSOURCE ]; then 
echo "Quellverzeichnis nicht vorhanden"
exit 1
fi

if [ ! -d $DWSOURCE/inc ]; then 
echo "inc nicht gefunden, es ist was faul!"
exit 1
fi

# INC
DELETES=`grep -Ev "^($|#)" $DELETEFILES | grep -E "^inc"`
for delete in $DELETES; do
 ftd=${INCTARGET}${delete}
 if [ -e $ftd ]; then 
  echo "removing $ftd"
  svn rm  $ftd
 fi
done

cp -r $DWSOURCE/inc $INCTARGET

#########################################
# lib
DELETES=`grep -Ev "^($|#)" $DELETEFILES | grep -E "^lib"`
for delete in $DELETES; do
 ftd=${LIBTARGET}${delete}
 if [ -e $ftd ]; then 
  echo "removing $ftd"
  svn rm $ftd
 fi
done
cp -r $DWSOURCE/lib $LIBTARGET

# bin
cp -r $DWSOURCE/bin $BINTARGET

# conf
cp $DWSOURCE/conf/* $CONFTARGET
rm -f $CONFTARGET/*.dist
rm -f $CONFTARGET/*.example


# Root 
for myfile in $DWSOURCE/*; do 
 if [ -f $myfile ]; then 
  echo "copying $myfile"
  cp -r $myfile $ROOTTARGET
 fi
done







