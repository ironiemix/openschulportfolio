#!/bin/bash

#Patchen
export QUILT_PATCHES=debian/patches
quilt --quiltrc /dev/null push -a

cd ..
svn-buildpackage --svn-ignore-new
cd debian

quilt --quiltrc /dev/null pop -a



