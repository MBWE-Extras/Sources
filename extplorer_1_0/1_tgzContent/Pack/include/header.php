<?php
// ensure this file is being included by a parent file
if( !defined( '_JEXEC' ) && !defined( '_VALID_MOS' ) ) die( 'Restricted access' );
/**
 * @version $Id: header.php 231 2013-09-04 18:12:47Z soeren $
 * @package eXtplorer
 * @copyright soeren 2007-2012
 * @author The eXtplorer project (http://extplorer.net)
 * @author The	The QuiX project (http://quixplorer.sourceforge.net)
 * 
 * @license
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 * 
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 * 
 * Alternatively, the contents of this file may be used under the terms
 * of the GNU General Public License Version 2 or later (the "GPL"), in
 * which case the provisions of the GPL are applicable instead of
 * those above. If you wish to allow use of your version of this file only
 * under the terms of the GPL and not to allow others to use
 * your version of this file under the MPL, indicate your decision by
 * deleting  the provisions above and replace  them with the notice and
 * other provisions required by the GPL.  If you do not delete
 * the provisions above, a recipient may use your version of this file
 * under either the MPL or the GPL."
 * 
 * This is the file, which prints the header row with the Logo
 */
function show_header($dirlinks='') {
	echo "<link rel='stylesheet' href='"._EXT_URL."/style/style.css' type='text/css' />\n";
	echo "<div id='ext_header'>\n";
	echo "<table border='0' width='100%' cellspacing='0' cellpadding='0'><tr>\n";

	echo "<td width='35%'>
		<a href='".ext_make_link('logout', null )."' title='".ext_Lang::msg('logoutlink', true)."'>
		<img src='/extras/images/icon_extras.gif' alt='Extras Logo' border='0' height='52' /></a>
		</td>\n";

	echo "<td style='padding-left: 15px; color:black; display:none' id='bookmark_container' width='0%'></td>\n";

	echo "<td width='50%'>
		<a href='".$GLOBALS['ext_home']."' target='_blank' title='eXtplorer Project'>
		<img src='"._EXT_URL."/images/eXtplorer_logo.png' alt='eXtplorer Logo' border='0' /></a>
		</td>\n";

	echo "<td width='10%'>
		<a href='http://wdc.com' target='_blank' title='Western Digital'>
		<img src='/admin/image/small_logo.jpg' alt='WD Logo' border='0' />
		</td>\n";

	echo "</tr></table>\n";
	echo "</div>\n";
}
//------------------------------------------------------------------------------
?>
