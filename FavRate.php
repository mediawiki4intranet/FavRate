<?php

/**
 * MediaWiki FavRate extension
 * Copyright © 2010+ Vitaliy Filippov
 * License: GPLv3 or later
 * http://wiki.4intra.net/FavRate
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

if (!defined('MEDIAWIKI'))
    die();

/* INSTALLATION:

1. Include the extension to your LocalSettings.php:
require_once("$IP/extensions/FavRate/FavRate.php");

2. Add the following line to MediaWiki:Sidebar:
* favratebar
(including *)

3. (Optional) Override some configuration variables:

// Enable collecting of unique visitor logs for each page (false by default)
$egFavRateLogVisitors = true;

// Make all visitor logs public (false by default)
$egFavRatePublicLogs = true;

// Maximum value for logarithmic scale of hit count
$egFavRateMaxHits = 100000;

// Maximum value for logarithmic scale of favorite count
$egFavRateMaxFav = 100;

// Maximum value for logarithmic scale of link count
$egFavRateMaxLinks = 100;

// Hit count scale color
$egFavRateHitsColor = "#0c0";

// Favorites count scale color
$egFavRateFavColor = "#c00";

// Link count scale color
$egFavRateLinksColor = "#00c";

*/

$wgExtensionCredits['specialpage'][] = array(
    'name'           => 'FavRate',
    'version'        => '0.91 (2010-04-26)',
    'author'         => 'Vitaliy Filippov',
    'url'            => 'http://wiki.4intra.net/FavRate',
    'description'    => 'Yet another page rating system for MediaWiki.',
    'descriptionmsg' => 'favrate-desc',
);
$wgExtensionMessagesFiles['FavRate'] = dirname(__FILE__) . '/FavRate.i18n.php';
$wgAutoloadClasses['FavRate'] = dirname(__FILE__) . '/FavRate.class.php';
$wgAutoloadClasses['SpecialFavRate'] = dirname(__FILE__) . '/FavRate.special.php';
$wgSpecialPages['FavRate'] = 'SpecialFavRate';
$wgSpecialPageGroups['FavRate'] = 'highuse';
$wgAjaxExportList[] = 'efFavRateSet';

// Core hooks
$wgHooks['LoadExtensionSchemaUpdates'][] = 'FavRate::LoadExtensionSchemaUpdates';
$wgHooks['ArticleViewHeader'][] = 'FavRate::ArticleViewHeader';
$wgHooks['SkinBuildSidebar'][] = 'FavRate::SkinBuildSidebar';
$wgHooks['BeforePageDisplay'][] = 'FavRate::BeforePageDisplay';

// Hooks for Wikilog
$wgHooks['WikilogPreloadComments'][] = 'FavRate::WikilogPreloadComments';
$wgHooks['WikilogCommentToolLinks'][] = 'FavRate::WikilogCommentToolLinks';

// Hooks for TemplatedPageList
$wgHooks['TemplatedPageListAddSortOrders'][] = 'FavRate::TemplatedPageListAddSortOrders';

// ResourceLoader module
$wgResourceModules['ext.favrate'] = array(
    'localBasePath' => dirname(__FILE__),
    'remoteExtPath' => 'FavRate',
    'scripts' => array('favrate.js'),
    'styles' => array('favrate.css'),
    'messages' => array('favrate-addfav', 'favrate-remfav', 'favrate-remfav-cmt'),
    'position' => 'top',
);

// Default configuration values
$egFavRateLogVisitors = false;
$egFavRatePublicLogs = false;
$egFavRateMaxHits = 100000;
$egFavRateMaxFav = 100;
$egFavRateMaxLinks = 100;
$egFavRateHitsColor = "#0c0";
$egFavRateFavColor = "#d0d";
$egFavRateLinksColor = "#00c";

// AJAX export function
function efFavRateSet()
{
    return call_user_func_array('FavRate::ajaxSetFavorite', func_get_args());
}
