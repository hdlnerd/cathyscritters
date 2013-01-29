#!/bin/sh
#
# Dump the database before every commit.  Then, merging a branch should
# merge the dump files and allow us to integrate the two databases?
#

mysqldump -u critters_kevin -p711coffeesql --skip-extended-insert critters_petsupervisor > ~/sites/petsupervisor.com/critters_critterdb.sql

