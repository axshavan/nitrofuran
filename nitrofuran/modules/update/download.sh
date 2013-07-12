#!/bin/sh

cd $1
wget http://nodeload.github.com/axshavan/nitrofuran/zip/master
unzip master
rm master
cd $1/nitrofuran-master
rm -rf tmp
rm -rf tests