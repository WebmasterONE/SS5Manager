<?php
######################################################################################
#  Copyright (C) 2012 Elite.So. All rights reserved.
#
#  This program is free software; you can redistribute it and/or modify
#  it under the terms of the GNU General Public License as published by
#  the Free Software Foundation; either version 2 of the License, or
#  (at your option) any later version.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
######################################################################################

header("Content-Type: text/html\n\n");

// *** Common variables ***
$cpAppName = 'Socks 5 Server Manager';
$cpAppVersion = '1.0 Beta Version';

// *** update stuff *** //
function checkUpdate()
{
	$newv = file_get_contents('http://www.elite.so/ss5mgr/vrsctl.db');
	$thisv = shell_exec("cat /usr/local/cpanel/whostmgr/docroot/cgi/ss5mgr/verctl.db");
	if ($newv != $thisv)
	{
		return true;
	}
	else {
		return false;
	}
}
// *** end update stuff *** //
$user = getenv('REMOTE_USER');
if($user != "root") { echo "You do not have the proper permissions to access SS5 Manager..."; exit; }
function ejecutar($act) {
if($act == "restart") {
$var = shell_exec("/etc/init.d/ss5 restart");
if(empty($var)) { echo "<p>Socks 5 Proxy Restarted Successfully.</p>"; } else { echo "<p>{$var}</p>"; }
}
else if($act == "stop") {
$var = shell_exec("/etc/init.d/ss5 stop");
if(empty($var)) { echo "<p>Socks 5 Proxy Stopped Successfully.</p>"; } else { echo "<p>{$var}</p>"; }
}
else if($act == "start") {
$var = shell_exec("/etc/init.d/ss5 start");
if(empty($var)) { echo "<p>Socks 5 Proxy Started Successfully.</p>"; } else { echo "<p>{$var}</p>"; }
}
}
$run = "Down";
$checkstat = shell_exec("ps -A");
if(strstr($checkstat,"ss5")) { $run = "UP"; }
?>
<html>
<head>
<title><?php echo $cpAppName; ?></title>
<meta name="description" content="WHM Plug-in of Socks 5 Proxy Server for cPanel servers" />
<link rel='stylesheet' type='text/css' href='/themes/x/style_optimized.css' />
<script type="text/javascript">
function okay() {
if(confirm('Are you sure of save configuration?')) {
document.getElementById('okay').submit();
}
return false;
}
function clog() {
document.getElementById('log').submit();
}
</script>
<style>
div#wrap {
margin: 0 auto;
width: 700px;
}
</style>
</head>
<body class="yui-skin-sam">
<div id="pageheader">
        <div id="breadcrumbs">
                <p>&nbsp;<a href="/scripts/command?PFILE=main">Main</a> &gt;&gt; <a href="ss5.php" class="active"><?php echo 
$cpAppName; ?></a></p>
        </div>
<div id="doctitle"><h1><?php echo $cpAppName; ?> (v<?php echo $cpAppVersion; ?>)</h1></div>
</div>
<div id="wrap">
<table cellpadding="0" cellspacing="0">
<tr align="center">
<td width="180"><img src="ss5mgr/home.png" alt="Home" /></td>
<?php
if ($run=="UP")
{
	echo '<td width="180"><img src="ss5mgr/restart.png" alt="Restart Socks 5 Server" /></td>';
}
?>
<td width="180"><img src="ss5mgr/configss5.png" alt="Edit Socks 5 Server Config" /></td>
<td width="180"><img src="ss5mgr/ss5users.png" alt="Edit Socks 5 Usernames & Passwords" /></td>
<?php
if ($run=="Down")
{
	echo '<td width="180"><img src="ss5mgr/start.png" alt="Start Socks 5 Server" /></td>';
}
if ($run=="UP")
{
	echo '<td width="180"><img src="ss5mgr/stop.png" alt="Stop Socks 5 Server" /></td>';
}
?>
</tr>
<tr align="center">
<td><a href="ss5.php">Home</a></td>
<?php
if ($run=="UP")
{
	echo '<td><a href="ss5.php?op=restart">Restart Socks 5 Server</a></td>';
}
?>
<td><a href="ss5.php?op=edit">Configuration Editor</a></td>
<td><a href="ss5.php?op=users">User Editor</a></td>
<?php
if ($run=="Down")
{
	echo '<td><a href="ss5.php?op=start">Start Socks 5 Server</a></td>';
}
if ($run=="UP")
{
	echo '<td><a href="ss5.php?op=stop">Stop Socks 5 Server</a></td>';
}
?>
</tr>
</table><br />
<?php
$op = &$_GET['op'];
switch($op) {
case "restart": echo "<p style=\"color: #009\"><b>Restarting Socks 5 Server...</b></p>"; ejecutar("restart"); echo "<p style=\"color: #009\"><b>Done...</b></p>"; break;
case "edit":
if(isset($_POST['conf'])) {
$conf = $_POST['conf'];
file_put_contents("/etc/opt/ss5/ss5.conf", $conf);
echo "<p><b>Configuration has been updated.</b></p>";
if(isset($_POST['c'])) { ejecutar("restart"); }
}
?>
<form action="ss5.php?op=edit" method="post" id="okay"><textarea name="conf" cols="80" rows="20"><?=shell_exec("cat /etc/opt/ss5/ss5.conf")?></textarea><br />Restart Socks 5 Server? <input type="checkbox" name="c" /><br /><br /><input type="submit" value="Update!" onClick="okay();return false;" /></form>
<?
break;
case "update":
echo "<p style=\"color: #009\"><b>Updating SS5 Manager...</b></p>"; 
$updcmd = shell_exec("sh /usr/local/cpanel/whostmgr/docroot/cgi/ss5updater");
if(empty($updcmd)) { echo "<p>SS5 Manager Updated Successfully!.</p>"; } else { echo "<p>{$updcmd}</p>"; }
echo "<p style=\"color: #009\"><b>Done...</b></p>"; 
break;
case "users":
if(isset($_POST['conf'])) {
$conf = $_POST['conf'];
file_put_contents("/etc/opt/ss5/ss5.passwd", $conf);
echo "<p><b>Users have been updated.</b></p>";
if(isset($_POST['c'])) { ejecutar("restart"); }
}
?>
<form action="ss5.php?op=users" method="post" id="okay"><textarea name="conf" cols="80" rows="20"><?=shell_exec("cat /etc/opt/ss5/ss5.passwd")?></textarea><br />Restart Socks 5 Server? <input type="checkbox" name="c" /><br /><br /><input type="submit" value="Update!" onClick="okay();return false;" /></form>
<?
break;
case "stop": echo "<p style=\"color: #009\"><b>Stopping Socks 5 Server...</b></p>"; ejecutar("stop"); echo "<p style=\"color: #009\"><b>Done...</b></p>"; break;
case "start": echo "<p style=\"color: #009\"><b>Starting Socks 5 Server...</b></p>"; ejecutar("start"); echo "<p style=\"color: #009\"><b>Done...</b></p>"; break;
default:
echo "Socks 5 Service Status: <font style=\"color: #0c0\"><b>{$run}</b></font>";
if (checkUpdate())
{
	echo '<br><p><b><u><a href="ss5.php?op=update">Update Available!</a></u></b></p><br>';
}
?>
<p style="color: #03F"><b>About Socks 5 Server Manager</b></p>
<p>Socket Secure (SOCKS) is an Internet protocol that routes network packets between a client and server through a proxy server. SOCKS5 additionally provides authentication so only authorized users may access a server.<br></p>
<p>SS5 Manager was created for cpanel server admins that want to create their own SS5 Proxy and control it from within whm as well.<br></p>
<p>For Support Visit <a href="http://www.elite.so" target='_blank'>Elite.So</a></p>
<? } ?>
<p>SS5 Manager: v<?php echo $cpAppVersion; ?></p><p>©2013, <a href="http://www.elite.so" target='_blank'>Elite.So</a></p>
</div>
</body>
</html>
