#!/bin/bash
#
# * jUpgradePro
# *
# * @author      Matias Aguirre
# * @email       maguirre@matware.com.ar
# * @url         http://www.matware.com.ar
# * @license     GNU/GPL
# 

PROJECT="jUpgradePro"
USER="fastslack"
FILE=~/path/to/file
LINK="http://github.com/${USER}/${PROJECT}/commit/%H"

git log --pretty=format:"%ai <i>by %an</i><br />%n \+ [[%h]](${LINK}) <b>%s</b> <br />%n" | grep -v Merge > ${FILE}
