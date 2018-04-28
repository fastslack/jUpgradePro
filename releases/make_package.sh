#!/bin/bash
###
# jUpgradePro
#
# @version $Id:
# @package jUpgradePro
# @copyright Copyright (C) 2004 - 2018 Matware. All rights reserved.
# @author Matias Aguirre
# @email maguirre@matware.com.ar
# @link http://www.matware.com.ar/
# @license GNU General Public License version 2 or later; see LICENSE
#

PROJECT="jupgradepro"
VERSION="3.8.0beta5"

RELEASE_DIR=`pwd`
PKG_DIR="pkg_$PROJECT"

COM_PACKAGE="com_jupgradepro"

# copy all needed files
rm *.zip
rm -rf ${PKG_DIR}
rm ${RELEASE_DIR}/packages/*

mkdir ${PKG_DIR}

# Copy administrator component
cp -r ../administrator/components/com_jupgradepro ${PKG_DIR}/com_jupgradepro

# Run composer
cd ${PKG_DIR}/com_jupgradepro
rm -rf vendor/
composer update
cd ${RELEASE_DIR}

cp -r ../plugins ${PKG_DIR}/plg_jupgradepro
cp -r ../media ${PKG_DIR}/com_jupgradepro/media

# Zip jUpgradePro component
cd ${PKG_DIR}/${COM_PACKAGE}
zip -rq ${COM_PACKAGE} .
mv ${COM_PACKAGE}.zip ${RELEASE_DIR}/packages/.
cd ${RELEASE_DIR}

# Create packages
zip -rq ${PKG_DIR}-${VERSION}.zip packages/ pkg_${PROJECT}.xml

exit;

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
