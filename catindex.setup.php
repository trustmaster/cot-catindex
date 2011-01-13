<?php
/* ====================
Copyright (c) 2008-2009, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.

[BEGIN_SED_EXTPLUGIN]
Code=catindex
Name=Category Index
Description=Displays categories on index
Version=0.2
Date=2009-jan-31
Author=Trustmaster
Copyright=(c) Vladimir Sibirov
Notes=
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
[END_SED_EXTPLUGIN]

[BEGIN_SED_EXTPLUGIN_CONFIG]
root=01:string:::Root category code (or empty for overall root)
cols=02:select:1,2,3,4:3:Number of columns
[END_SED_EXTPLUGIN_CONFIG]
==================== */
if ( !defined('SED_CODE') ) { die("Wrong URL."); }

?>