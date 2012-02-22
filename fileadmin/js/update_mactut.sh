#!/bin/sh

DIR=/srv/www/htdocs/vifamath/math/fileadmin/js
SCRIPT=${DIR}/link-scrapper.php

php $SCRIPT > mactut_new.txt 2>/dev/null

i=$(ls -l ${DIR}/mactut.txt | awk '{ print $5 }')
j=$(ls -l ${DIR}/mactut_new.txt | awk '{ print $5 }')
if [ ${i} -lt ${j} ]
then
 cp ${DIR}/mactut_new.txt ${DIR}/mactut.txt
 rm -rf ${DIR}/mactut_new.txt
fi
echo "updated: `date`" >> update_mactut.log

