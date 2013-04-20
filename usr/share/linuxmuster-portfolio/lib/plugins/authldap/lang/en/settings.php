<?php
$lang['server']      = 'Your LDAP server. Either hostname (<code>localhost</code>) or full qualified URL (<code>ldap://server.tld:389</code>)';
$lang['port']        = 'LDAP server port if no full URL was given above';
$lang['usertree']    = 'Where to find the user accounts. Eg. <code>ou=People, dc=server, dc=tld</code>';
$lang['grouptree']   = 'Where to find the user groups. Eg. <code>ou=Group, dc=server, dc=tld</code>';
$lang['userfilter']  = 'LDAP filter to search for user accounts. Eg. <code>(&amp;(uid=%{user})(objectClass=posixAccount))</code>';
$lang['groupfilter'] = 'LDAP filter to search for groups. Eg. <code>(&amp;(objectClass=posixGroup)(|(gidNumber=%{gid})(memberUID=%{user})))</code>';
$lang['version']     = 'The protocol version to use. You may need to set this to <code>3</code>';
$lang['starttls']    = 'Use TLS connections?';
$lang['referrals']   = 'Shall referrals be followed?';
$lang['binddn']      = 'DN of an optional bind user if anonymous bind is not sufficient. Eg. <code>cn=admin, dc=my, dc=home</code>';
$lang['bindpw']      = 'Password of above user';
$lang['userscope']   = 'Limit search scope for user search';
$lang['groupscope']  = 'Limit search scope for group search';
$lang['groupkey']    = 'Group member ship from any user attribute (instead of standard AD groups) e.g. group from department or telephone number';
$lang['debug']       = 'Display additional debug information on errors';
