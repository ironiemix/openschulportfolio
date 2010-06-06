#!/bin/bash

#Patchen
QUILT_PATCHES=./debian/patches
quilt --quiltrc /dev/null push -a

svn-buildpackage --svn-ignore-new

quilt --quiltrc /dev/null pop -a



