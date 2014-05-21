#!/bin/bash
#
# * jUpgradePro
# *
# * @author      Matias Aguirre
# * @email       maguirre@matware.com.ar
# * @url         http://www.matware.com.ar
# * @license     GNU/GPL
# 

PROJECT="jupgradepro"
VERSION="3.3.0"

DIR="com_$PROJECT"
PACKAGE="com_$PROJECT-$VERSION.zip"

# copy all needed files
rm *.zip
rm -rf $DIR
cp -r ../trunk $DIR

# delete version-control stuff and other files
find $DIR -name ".svn" -type d -exec rm -rf {} \;
find $DIR -name ".DS_Store" -exec rm -rf {} \;

# delete unused files
#rm $DIR/admin/${PROJECT}.xml
#rm $DIR/TODO

# Zipping plugin
cd $DIR/plugins/plg_jupgradepro-1.5
zip -rq plg_${PROJECT}-restful-${VERSION}-j1.5.zip .
mv plg_${PROJECT}-restful-${VERSION}-j1.5.zip ../../../.
cd ../../..

# Zipping plugin
cd $DIR/plugins/plg_jupgradepro-2.5
zip -rq plg_${PROJECT}-restful-${VERSION}-j2.5-j3.zip .
mv plg_${PROJECT}-restful-${VERSION}-j2.5-j3.zip ../../../.
cd ../../..

# create package
rm $PACKAGE
rm -rf $DIR/plugin/
zip -rq $PACKAGE $DIR

# create symlink
rm -rf com_${PROJECT}-latest.zip
ln -s $PACKAGE com_${PROJECT}-latest.zip

# cleanup
rm -rf $DIR
