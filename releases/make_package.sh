#!/bin/bash
###
# jUpgradePro
#
# @version $Id:
# @package jUpgradePro
# @copyright Copyright (C) 2004 - 2016 Matware. All rights reserved.
# @author Matias Aguirre
# @email maguirre@matware.com.ar
# @link http://www.matware.com.ar/
# @license GNU General Public License version 2 or later; see LICENSE
#

PROJECT="jupgradepro"
VERSION="3.6.1"

RELEASE_DIR=`pwd`
PKG_DIR="pkg_$PROJECT"

COM_PACKAGE="com_jupgradepro"
LIB_PACKAGE="lib_matware"
PLG_PACKAGE="plg_sys_matware"

# copy all needed files
rm *.zip
#rm -rf $DIR

mkdir ${PKG_DIR}

cp -r ../administrator/components/com_jupgradepro ${PKG_DIR}/com_jupgradepro
cp -r ../matware-libraries/libraries/matware ${PKG_DIR}/lib_matware
cp -r ../matware-libraries/plugins/system/matware ${PKG_DIR}/plg_matware
cp -r ../plugins ${PKG_DIR}/plg_jupgradepro
cp -r ../media ${PKG_DIR}/com_jupgradepro/media

# Zip jUpgradePro component
cd ${PKG_DIR}/${COM_PACKAGE}
zip -rq ${COM_PACKAGE} .
mv ${COM_PACKAGE}.zip ${RELEASE_DIR}/packages/.
cd ${RELEASE_DIR}

# Zip Matware library
cd ${PKG_DIR}/lib_matware
zip -rq ${LIB_PACKAGE} .
mv ${LIB_PACKAGE}.zip ${RELEASE_DIR}/packages/.
cd ${RELEASE_DIR}

# Zip Matware plugin
cd ${PKG_DIR}/plg_matware
zip -rq ${PLG_PACKAGE} .
mv ${PLG_PACKAGE}.zip ${RELEASE_DIR}/packages/.
cd ${RELEASE_DIR}

# Create packages
zip -rq ${PKG_DIR}-${VERSION}.zip packages/ pkg_${PROJECT}.xml

# Zip plugin for J! 1.5
cd ../plugins/system/plg_jupgradepro-1.5
zip -rq plg_${PROJECT}-restful-${VERSION}-j1.5.zip .
mv plg_${PROJECT}-restful-${VERSION}-j1.5.zip ${RELEASE_DIR}/.
cd ${RELEASE_DIR}

# Zip plugin for J! 2.5 or greater
cd ../plugins/system/plg_jupgradepro-2.5
zip -rq plg_${PROJECT}-restful-${VERSION}-j2.5-j3.zip .
mv plg_${PROJECT}-restful-${VERSION}-j2.5-j3.zip ${RELEASE_DIR}/.
cd ${RELEASE_DIR}

# create symlink
#rm -rf com_${PROJECT}-latest.zip
#ln -s $PACKAGE com_${PROJECT}-latest.zip

# Cleanup
rm -rf ${PKG_DIR}
#rm packages/*.zip
