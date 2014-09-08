#!/bin/bash
###
# jUpgradePro
#
# @version $Id:
# @package jUpgradePro
# @copyright Copyright (C) 2004 - 2013 Matware. All rights reserved.
# @author Matias Aguirre
# @email maguirre@matware.com.ar
# @link http://www.matware.com.ar/
# @license GNU General Public License version 2 or later; see LICENSE
#

PROJECT="jupgradepro"
VERSION="3.3.1"

RELEASE_DIR=`pwd`
DIR="com_$PROJECT"
PACKAGE="com_$PROJECT-$VERSION.zip"

# copy all needed files
rm *.zip
rm -rf $DIR
cp -r ../trunk $DIR

# delete version-control stuff and other files
find ${DIR} -name ".svn" -type d -exec rm -rf {} \;
find ${DIR} -name ".DS_Store" -exec rm -rf {} \;

# Zip plugin for J! 1.5
cd ${DIR}/plugins/system/plg_jupgradepro-1.5
zip -rq plg_${PROJECT}-restful-${VERSION}-j1.5.zip .
mv plg_${PROJECT}-restful-${VERSION}-j1.5.zip ${RELEASE_DIR}/.
cd ${RELEASE_DIR}

# Zip plugin for J! 2.5 or greater
cd $DIR/plugins/system/plg_jupgradepro-2.5
zip -rq plg_${PROJECT}-restful-${VERSION}-j2.5-j3.zip .
mv plg_${PROJECT}-restful-${VERSION}-j2.5-j3.zip ${RELEASE_DIR}/.
cd ${RELEASE_DIR}

# Create packages
#rm $PACKAGE
rm -rf $DIR/plugin/
zip -rq $PACKAGE $DIR

# create symlink
rm -rf com_${PROJECT}-latest.zip
ln -s $PACKAGE com_${PROJECT}-latest.zip

# cleanup
rm -rf $DIR
