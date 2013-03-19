#!/bin/sh
cp .gitignore .gitignore_mine
wget --no-check-certificate https://github.com/Fabrik/fabrik/tarball/master -O - | tar -zx --strip-components 1
mv .gitignore .gitignore_fabrik
mv .gitignore_mine  .gitignore
