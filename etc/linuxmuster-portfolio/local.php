<?php
/*
 * Date: Sun, 17 Oct 2010 15:00:09 +0100
 */

$conf['title'] = 'Schulportfolio';
$conf['savedir'] = '/home/linuxmuster-portfolio/data/';
$conf['lang'] = 'de';
$conf['template'] = 'portfolio2';
$conf['license'] = '';
$conf['recent'] = 35;
$conf['breadcrumbs'] = 0;
$conf['youarehere'] = 1;
$conf['breadcrumbs'] = 7;
$conf['dformat'] = '%d.%m.%Y %H:%M';
$conf['useacl'] = 1;
$conf['openregister'] = '0';
$conf['superuser'] = '@portfolioadm';
$conf['rememberme'] = 0;
$conf['disableactions'] = 'register,resendpwd,profile';
$conf['sneaky_index'] = 1;
$conf['updatecheck'] = 0;
$conf['userewrite'] = '1';
$conf['autoplural'] = 1;
$conf['compress'] = 0;
$conf['plugin']['tag']['pagelist_flags'] = 'default';
$conf['plugin']['task']['datefield'] = 0;
$conf['plugin']['task']['tasks_formposition'] = 'top';
$conf['plugin']['filelist']['allowed_absolute_paths'] = '/home/linuxmuster-portfolio/data';
$conf['plugin']['archiveupload']['manageronly'] = 1;
$conf['plugin']['include']['showuser'] = 0;
$conf['plugin']['include']['showcomments'] = 0;
$conf['plugin']['include']['showlinkbacks'] = 0;
$conf['plugin']['include']['showtags'] = 0;
$conf['plugin']['include']['noheader'] = '1';
$conf['authtype']    = 'ldap';
$conf['auth']['ldap']['server']      = 'ldap://localhost:389';
$conf['auth']['ldap']['usertree']    = 'ou=accounts,dc=schule, dc=de';
$conf['auth']['ldap']['grouptree']   = 'ou=groups,dc=schule, dc=de';
$conf['auth']['ldap']['userfilter']  = '(&(uid=%{user})(objectClass=posixAccount))';
$conf['auth']['ldap']['groupfilter'] = '(&(objectClass=posixGroup)(|(gidNumber=%{gid})(memberUID=%{user})))';
$conf['auth']['ldap']['groupdelprefix'] = "p_";
$conf['auth']['ldap']['version'] = 3;
$conf['defaultgroup'] = "users";

// end auto-generated content
