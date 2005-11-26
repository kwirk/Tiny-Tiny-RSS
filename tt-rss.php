<?
	session_start();

	require_once "sanity_check.php";
	require_once "version.php"; 
	require_once "config.php";
	require_once "db-prefs.php";
	require_once "functions.php"; 

	$link = db_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);	

	login_sequence($link);

?>
<html>
<head>
	<title>Tiny Tiny RSS</title>

	<link rel="stylesheet" type="text/css" href="tt-rss.css">

	<?	$user_theme = $_SESSION["theme"];
		if ($user_theme) { ?>
		<link rel="stylesheet" type="text/css" href="themes/<?= $user_theme ?>/theme.css">
	<? } ?>

	<? $user_css_url = get_pref($link, 'USER_STYLESHEET_URL'); ?>
	<? if ($user_css_url) { ?>
		<link type="text/css" href="<?= $user_css_url ?>"/> 
	<? } ?>

	<? if (get_pref($link, 'USE_COMPACT_STYLESHEET')) { ?>

		<link rel="stylesheet" href="tt-rss_compact.css" type="text/css">

	<? } else { ?>

		<link title="Compact Stylesheet" rel="alternate stylesheet" 
			type="text/css" href="tt-rss_compact.css"/> 

	<? } ?>

	<script type="text/javascript" src="functions.js"></script>
	<script type="text/javascript" src="tt-rss.js"></script>
	<!--[if gte IE 5.5000]>
		<script type="text/javascript" src="pngfix.js"></script>
	<![endif]-->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<body onload="init()">

<table width="100%" height="100%" cellspacing="0" cellpadding="0" class="main">
<? if (get_pref($link, 'DISPLAY_HEADER')) { ?>
<tr>
	<td colspan="2">
		<table cellspacing="0" cellpadding="0" width="100%"><tr>
			<td rowspan="2" class="header" valign="middle">	
				<img src="images/ttrss_logo.png" alt="logo">	
			</td>
			<td align="right" valign="top">
				<div id="notify"><span id="notify_body"></div>
			</td>

			<div id="userDlg">&nbsp;</div>

		</tr><tr><td class="welcomePrompt">
			<? if (!SINGLE_USER_MODE) { ?>
				Hello, <b><?= $_SESSION["name"] ?></b>
				(<a href="logout.php">Logout</a>)
			<? } ?>
			</td>			
		</tr></table>
	</td>
</tr>
<? } else { ?>
	<div id="userDlg">&nbsp;</div>
<? } ?>
<tr>
	<td valign="top" rowspan="3" class="feeds"> 
		<table class="innerFeedTable" 
			cellspacing="0" cellpadding="0" height="100%" width="100%">
		<tr><td>
			<div id="dispSwitch"> 
			<a id="dispSwitchPrompt" href="javascript:toggleTags()">display tags</a>
		</div>
		</td></tr>	
		<tr><td height="100%" width="100%" valign="top">
		
		<!-- <div id="feeds">&nbsp;</div> -->


		<iframe frameborder="0" 
			src="backend.php?op=error&msg=Loading,%20please wait..."
			id="feeds-frame" name="feeds-frame" class="feedsFrame"> </iframe>

		</td></tr></table>

		<? if (get_pref($link, 'DISPLAY_FEEDLIST_ACTIONS')) { ?>

		<div align="center">All feeds: 
		
		<select id="allFeedsChooser">
			<option>Update</option>
			<option>Mark as read</option>
			<option>Show only unread</option>
		</select>

		<input type="submit" class="button" onclick="allFeedsMenuGo()" value="Go">

		</div>
		

		<? } ?>

	</td>
	<td valign="top" class="headlinesToolbarBox">
		<table width="100%" cellpadding="0" cellspacing="0">
		
		<tr><td class="headlinesToolbar" id="headlinesToolbar">
			<input id="searchbox"
			onblur="javascript:enableHotkeys()" onfocus="javascript:disableHotkeys()"
			onchange="javascript:search()">
		<select id="searchmodebox">
			<option>This feed</option>
			<option>All feeds</option>
		</select>
		
		<input type="submit" 
			class="button" onclick="javascript:search()" value="Search">

		&nbsp; 
		
		View: 
		
		<select id="viewbox" onchange="javascript:viewCurrentFeed(0, '')">
			<option>All Articles</option>
			<option>Starred</option>
			<option selected>Unread</option>
			<option>Unread or Starred</option>
			<option>Unread or Updated</option>
		</select>

		&nbsp;Limit:

		<select id="limitbox" onchange="javascript:viewCurrentFeed(0, '')">
		
		<?
			$limits = array(15 => 15, 30 => 30, 60 => 60);
			
			$def_art_limit = get_pref($link, 'DEFAULT_ARTICLE_LIMIT');

			print $def_art_limit;
	
			if ($def_art_limit >= 0) {
				$limits[$def_art_limit] = $def_art_limit; 
			}
			
			asort($limits);

			array_push($limits, 0);

			foreach ($limits as $key) {
				print "<option";
				if ($key == $def_art_limit) { print " selected"; }
				print ">";
				
				if ($limits[$key] == 0) { print "All"; } else { print $limits[$key]; }
				
				print "</option>";
			} ?>
		
		</select>

		&nbsp;Feed: <input class="button" type="submit"
			onclick="javascript:viewCurrentFeed(0, 'ForceUpdate')" value="Update">

		<input class="button" type="submit" id="btnMarkFeedAsRead"
			onclick="javascript:viewCurrentFeed(0, 'MarkAllRead')" value="Mark as read">

		</td>
		<td align="right">
			Actions: <select id="quickMenuChooser">
				<option id="qmcPrefs" selected>Preferences...</option>
				<option disabled>--------</option>
				<option style="color : #5050aa" disabled>Feed actions:</option>
				<option id="qmcAddFeed">&nbsp;&nbsp;Add new feed</option>
				<option id="qmcRemoveFeed">&nbsp;&nbsp;Remove this feed</option>
				<!-- <option>Edit this feed</option> -->
				<option disabled>--------</option>
				<option style="color : #5050aa" disabled>All feeds:</option>
				<option id="qmcUpdateFeeds">&nbsp;&nbsp;Update</option>
				<option id="qmcCatchupAll">&nbsp;&nbsp;Mark as read</option>
				<option id="qmcShowOnlyUnread">&nbsp;&nbsp;Show only unread</option>
			</select>
			<input type="submit" class="button" onclick="quickMenuGo()" value="Go">
		</td>
		</tr>
		</table>
	</td> 
</tr><tr>
	<td id="headlines" class="headlines" valign="top">
		<iframe frameborder="0" name="headlines-frame" 
			id="headlines-frame" class="headlinesFrame" 
				src="backend.php?op=error&msg=No%20feed%20selected."></iframe>
	</td>
</tr><tr>
	<td class="content" id="content" valign="top">
		<iframe frameborder="0" name="content-frame" 
			id="content-frame" class="contentFrame"> </iframe>
	</td>
</tr>
<? if (get_pref($link, 'DISPLAY_FOOTER')) { ?>
<tr>
	<td colspan="2" class="footer">
		<a href="http://tt-rss.spb.ru/">Tiny-Tiny RSS</a> v<?= VERSION ?> &copy; 2005 Andrew Dolgov
		<? if (WEB_DEMO_MODE) { ?>
		<br>Running in demo mode, some functionality is disabled.
		<? } ?>
	</td>
</td>
<? } ?>
</table>

<? db_close($link); ?>

</body>
</html>
