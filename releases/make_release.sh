#!/bin/bash
#
# * jUpgrade
# *
# * @author      Matias Aguirre
# * @email       maguirre@matware.com.ar
# * @url         http://www.matware.com.ar
# * @license     GNU/GPL
# 

PROJECT="jupgradepro-free"
VERSION="3.0.0-RC2"

DIR="com_$PROJECT"
PACKAGE="com_$PROJECT-$VERSION.zip"

# copy all needed files
rm -rf $DIR
cp -r ../trunk $DIR

# delete version-control stuff and other files
find $DIR -name ".svn" -type d -exec rm -rf {} \;
find $DIR -name ".DS_Store" -exec rm -rf {} \;

# delete unused files
#rm $DIR/admin/${PROJECT}.xml
#rm $DIR/TODO

# Zipping plugin
cd $DIR/plugin/
zip -rq plg_${PROJECT}-rest-${VERSION}.zip .
mv plg_${PROJECT}-rest-${VERSION}.zip ../../.
cd ../..

# create package
rm $PACKAGE
rm -rf $DIR/plugin/
zip -rq $PACKAGE $DIR

# create symlink
rm -rf com_${PROJECT}-latest.zip
ln -s $PACKAGE com_${PROJECT}-latest.zip

# cleanup
rm -rf $DIR
