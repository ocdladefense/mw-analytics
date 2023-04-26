<?php
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

$wgExtensionCredits['other'][] = array(
	'path'           => __FILE__,
	'name'           => 'Analytics Integration',
	'version'        => '0.1-alpha',
	'author'         => 'José Bernal',
	'descriptionmsg' => 'analytics-desc',
	'url'            => '',
);

// $wgExtensionMessagesFiles['analytics'] = dirname(__FILE__) . '/googleAnalytics.i18n.php';

$wgHooks['SkinAfterBottomScripts'][]  = 'efGoogleAnalyticsHookText';
// $wgHooks['ParserAfterTidy'][] = 'efGoogleAnalyticsASAC';





function efGoogleAnalyticsASAC( &$parser, &$text ) {
	global $wgOut, $wgGoogleAnalyticsAccount, $wgGoogleAnalyticsAddASAC;

	if( !empty($wgGoogleAnalyticsAccount) && $wgGoogleAnalyticsAddASAC ) {
		$wgOut->addScript('<script type="text/javascript">window.google_analytics_uacct = "' . $wgGoogleAnalyticsAccount . '";</script>');
	}

	return true;
}

function efGoogleAnalyticsHookText( $skin, &$text='' ) {
	$text .= efAddGoogleAnalytics();
	return true;
}

function efAddGoogleAnalytics() {
	
	// Use any analytics?
	global $wgUseAnalytics,
	
	// Specific to Google Analytics.
	$wgGoogleAnalyticsAccount,
	
	$wgGoogleAnalyticsIgnoreSysops,
	
	$wgGoogleAnalyticsIgnoreBots,
	
	$wgUser;



	if ( $wgUser->isAllowed( 'bot' ) && $wgGoogleAnalyticsIgnoreBots ) {
		return "\n<!-- Google Analytics tracking is disabled for bots -->";
	}
	
	$groups = $wgUser->getEffectiveGroups();       

	if ( in_array('ignoreanalytics', $groups ) && $wgGoogleAnalyticsIgnoreSysops ) {
		return "\n<!-- Google Analytics tracking is disabled for this user -->";
	}

	if ( $wgGoogleAnalyticsAccount === '' ) {
		return "\n<!-- Set \$wgGoogleAnalyticsAccount to your account # provided by Google Analytics. -->";
	}

	return <<<HTML
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$wgGoogleAnalyticsAccount}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '{$wgGoogleAnalyticsAccount}');
</script>
HTML;
}

///Alias for efAddGoogleAnalytics - backwards compatibility.
function addGoogleAnalytics() { return efAddGoogleAnalytics(); }
