<?php
// $Id: main.php 12313 2013-09-15 21:14:35Z skenow $
// ------------------------------------------------------------------------ //
// XOOPS - PHP Content Management System //
// Copyright (c) 2000 XOOPS.org //
// <http://www.xoops.org/> //
// ------------------------------------------------------------------------ //
// This program is free software; you can redistribute it and/or modify //
// it under the terms of the GNU General Public License as published by //
// the Free Software Foundation; either version 2 of the License, or //
// (at your option) any later version. //
// //
// You may not change or alter any portion of this comment or credits //
// of supporting developers from this source code or any supporting //
// source code which is considered copyrighted (c) material of the //
// original comment or credit authors. //
// //
// This program is distributed in the hope that it will be useful, //
// but WITHOUT ANY WARRANTY; without even the implied warranty of //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the //
// GNU General Public License for more details. //
// //
// You should have received a copy of the GNU General Public License //
// along with this program; if not, write to the Free Software //
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA //
// ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu) //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project //
// ------------------------------------------------------------------------- //
/**
 * Administration of finding users, main file
 *
 * @copyright http://www.impresscms.org/ The ImpressCMS Project
 * @license LICENSE.txt
 * @package Administration
 * @subpackage Users
 * @version SVN: $Id: main.php 12313 2013-09-15 21:14:35Z skenow $
 */
if (!is_object(icms::$user) || !is_object($icmsModule) || !icms::$user->isAdmin($icmsModule->getVar('mid'))) {
	exit("Access Denied");
}

/* set default values */
$op = 'form';

$filter_post = array();
$filter_get = array();

if (!empty($_POST)) {
	// in places where strict mode is not used for checkVarArray, make sure filter_ vars are not overwritten
	if (isset($_POST['filter_get'])) unset($_POST['filter_get']);
	$clean_POST = icms_core_DataFilter::checkVarArray($_POST, $filter_post, false);
	extract($clean_POST);
}
if (!empty($_GET)) {
	$clean_GET = icms_core_DataFilter::checkVarArray($_GET, $filter_get, false);
	extract($clean_GET);
}

icms_cp_header();

if ($op == "form") {
	$member_handler = icms::handler('icms_member');
	$acttotal = icms_conv_nr2local($member_handler->getUserCount(new icms_db_criteria_Item('level', 0, '>')));
	$inacttotal = icms_conv_nr2local($member_handler->getUserCount(new icms_db_criteria_Item('level', 0)));
	$group_select = new icms_form_elements_select_Group(_AM_GROUPS, "selgroups", NULL, FALSE, 5, TRUE);
	$uname_text = new icms_form_elements_Text("", "user_uname", 30, 60);
	$uname_match = new icms_form_elements_select_Matchoption("", "user_uname_match");
	$uname_tray = new icms_form_elements_Tray(_AM_UNAME, "&nbsp;");
	$uname_tray->addElement($uname_match);
	$uname_tray->addElement($uname_text);
	$name_text = new icms_form_elements_Text("", "user_name", 30, 60);
	$name_match = new icms_form_elements_select_Matchoption("", "user_name_match");
	$name_tray = new icms_form_elements_Tray(_AM_REALNAME, "&nbsp;");
	$name_tray->addElement($name_match);
	$name_tray->addElement($name_text);
	$email_text = new icms_form_elements_Text("", "user_email", 30, 60);
	$email_match = new icms_form_elements_select_Matchoption("", "user_email_match");
	$email_tray = new icms_form_elements_Tray(_AM_EMAIL, "&nbsp;");
	$email_tray->addElement($email_match);
	$email_tray->addElement($email_text);
	$login_name_text = new icms_form_elements_Text("", "user_login_name", 30, 60);
	$login_name_match = new icms_form_elements_select_Matchoption("", "user_login_name_match");
	$login_name_tray = new icms_form_elements_Tray(_AM_LOGINNAME, "&nbsp;");
	$login_name_tray->addElement($login_name_match);
	$login_name_tray->addElement($login_name_text);
	$url_text = new icms_form_elements_Text(_AM_URLC, "user_url", 30, 100);
	// $theme_select = new icms_form_elements_select_Theme(_AM_THEME, "user_theme");
	// $timezone_select = new icms_form_elements_select_Timezone(_AM_TIMEZONE, "user_timezone_offset");
	$icq_text = new icms_form_elements_Text("", "user_icq", 30, 100);
	$icq_match = new icms_form_elements_select_Matchoption("", "user_icq_match");
	$icq_tray = new icms_form_elements_Tray(_AM_ICQ, "&nbsp;");
	$icq_tray->addElement($icq_match);
	$icq_tray->addElement($icq_text);
	$aim_text = new icms_form_elements_Text("", "user_aim", 30, 100);
	$aim_match = new icms_form_elements_select_Matchoption("", "user_aim_match");
	$aim_tray = new icms_form_elements_Tray(_AM_AIM, "&nbsp;");
	$aim_tray->addElement($aim_match);
	$aim_tray->addElement($aim_text);
	$yim_text = new icms_form_elements_Text("", "user_yim", 30, 100);
	$yim_match = new icms_form_elements_select_Matchoption("", "user_yim_match");
	$yim_tray = new icms_form_elements_Tray(_AM_YIM, "&nbsp;");
	$yim_tray->addElement($yim_match);
	$yim_tray->addElement($yim_text);
	$msnm_text = new icms_form_elements_Text("", "user_msnm", 30, 100);
	$msnm_match = new icms_form_elements_select_Matchoption("", "user_msnm_match");
	$msnm_tray = new icms_form_elements_Tray(_AM_MSNM, "&nbsp;");
	$msnm_tray->addElement($msnm_match);
	$msnm_tray->addElement($msnm_text);
	$location_text = new icms_form_elements_Text(_AM_LOCATION, "user_from", 30, 100);
	$occupation_text = new icms_form_elements_Text(_AM_OCCUPATION, "user_occ", 30, 100);
	$interest_text = new icms_form_elements_Text(_AM_INTEREST, "user_intrest", 30, 100);

	// $bio_text = new icms_form_elements_Text(_AM_EXTRAINFO, "user_bio", 30, 100);
	$lastlog_more = new icms_form_elements_Text(_AM_LASTLOGMORE, "user_lastlog_more", 10, 5);
	$lastlog_less = new icms_form_elements_Text(_AM_LASTLOGLESS, "user_lastlog_less", 10, 5);
	$reg_more = new icms_form_elements_Text(_AM_REGMORE, "user_reg_more", 10, 5);
	$reg_less = new icms_form_elements_Text(_AM_REGLESS, "user_reg_less", 10, 5);
	$posts_more = new icms_form_elements_Text(_AM_POSTSMORE, "user_posts_more", 10, 5);
	$posts_less = new icms_form_elements_Text(_AM_POSTSLESS, "user_posts_less", 10, 5);
	$mailok_radio = new icms_form_elements_Radio(_AM_SHOWMAILOK, "user_mailok", "both");
	$mailok_radio->addOptionArray(array("mailok" => _AM_MAILOK, "mailng" => _AM_MAILNG, "both" => _AM_BOTH));
	$type_radio = new icms_form_elements_Radio(_AM_SHOWTYPE, "user_type", "actv");
	$type_radio->addOptionArray(array("actv" => _AM_ACTIVE, "inactv" => _AM_INACTIVE, "both" => _AM_BOTH));
	$sort_select = new icms_form_elements_Select(_AM_SORT, "user_sort");
	$sort_select->addOptionArray(array("uname" => _AM_UNAME, "login_name" => _AM_LOGINNAME, "email" => _AM_EMAIL, "last_login" => _AM_LASTLOGIN, "user_regdate" => _AM_REGDATE, "posts" => _AM_POSTS));
	$order_select = new icms_form_elements_Select(_AM_ORDER, "user_order");
	$order_select->addOptionArray(array("ASC" => _AM_ASC, "DESC" => _AM_DESC));
	$limit_text = new icms_form_elements_Text(_AM_LIMIT, "limit", 6, 2);
	$fct_hidden = new icms_form_elements_Hidden("fct", "findusers");
	$op_hidden = new icms_form_elements_Hidden("op", "submit");
	$submit_button = new icms_form_elements_Button("", "user_submit", _SUBMIT, "submit");

	$form = new icms_form_Theme(_AM_FINDUS, "uesr_findform", "admin.php", 'post', TRUE);
	$form->addElement($uname_tray);
	$form->addElement($name_tray);
	$form->addElement($login_name_tray);
	$form->addElement($email_tray);
	$form->addElement($group_select);
	// $form->addElement($theme_select);
	// $form->addElement($timezone_select);
	$form->addElement($icq_tray);
	$form->addElement($aim_tray);
	$form->addElement($yim_tray);
	$form->addElement($msnm_tray);
	$form->addElement($url_text);
	$form->addElement($location_text);
	$form->addElement($occupation_text);
	$form->addElement($interest_text);
	// $form->addElement($bio_text);
	$form->addElement($lastlog_more);
	$form->addElement($lastlog_less);
	$form->addElement($reg_more);
	$form->addElement($reg_less);
	$form->addElement($posts_more);
	$form->addElement($posts_less);
	$form->addElement($mailok_radio);
	$form->addElement($type_radio);
	$form->addElement($sort_select);
	$form->addElement($order_select);
	$form->addElement($fct_hidden);
	$form->addElement($limit_text);
	$form->addElement($op_hidden);
	// if this is to find users for a specific group
	if (!empty($group) && (int) $group > 0) {
		$group_hidden = new icms_form_elements_Hidden("group", (int) $group);
		$form->addElement($group_hidden);
	}
	$form->addElement($submit_button);
	echo '<div class="CPbigTitle" style="background-image: url(' . ICMS_MODULES_URL . '/system/admin/findusers/images/findusers_big.png)">' . _AM_FINDUS . '</div><br />';
	echo "(" . sprintf(_AM_ACTUS, "<span style='color:#ff0000;'>$acttotal</span>") . " " . sprintf(_AM_INACTUS, "<span style='color:#ff0000;'>$inacttotal</span>") . ")<br /><br />";
	$form->display();
} elseif ($op == "submit" & icms::$security->check()) {
	$criteria = new icms_db_criteria_Compo();
	if (!empty($user_uname)) {
		$match = (!empty($user_uname_match)) ? (int) $user_uname_match : XOOPS_MATCH_START;
		switch ($match) {
			case XOOPS_MATCH_START:
				$criteria->add(new icms_db_criteria_Item('uname', icms_core_DataFilter::addSlashes(trim($user_uname)) . '%', 'LIKE'));
				break;

			case XOOPS_MATCH_END:
				$criteria->add(new icms_db_criteria_Item('uname', '%' . icms_core_DataFilter::addSlashes(trim($user_uname)), 'LIKE'));
				break;

			case XOOPS_MATCH_EQUAL:
				$criteria->add(new icms_db_criteria_Item('uname', icms_core_DataFilter::addSlashes(trim($user_uname))));
				break;

			case XOOPS_MATCH_CONTAIN:
				$criteria->add(new icms_db_criteria_Item('uname', '%' . icms_core_DataFilter::addSlashes(trim($user_uname)) . '%', 'LIKE'));
				break;

			default:
				break;
		}
	}
	if (!empty($user_name)) {
		$match = (!empty($user_name_match)) ? (int) $user_name_match : XOOPS_MATCH_START;
		switch ($match) {
			case XOOPS_MATCH_START:
				$criteria->add(new icms_db_criteria_Item('name', icms_core_DataFilter::addSlashes(trim($user_name)) . '%', 'LIKE'));
				break;

			case XOOPS_MATCH_END:
				$criteria->add(new icms_db_criteria_Item('name', '%' . icms_core_DataFilter::addSlashes(trim($user_name)), 'LIKE'));
				break;

			case XOOPS_MATCH_EQUAL:
				$criteria->add(new icms_db_criteria_Item('name', icms_core_DataFilter::addSlashes(trim($user_name))));
				break;

			case XOOPS_MATCH_CONTAIN:
				$criteria->add(new icms_db_criteria_Item('name', '%' . icms_core_DataFilter::addSlashes(trim($user_name)) . '%', 'LIKE'));
				break;

			default:
				break;
		}
	}
	if (!empty($user_login_name)) {
		$match = (!empty($user_login_name_match)) ? (int) $user_login_name_match : XOOPS_MATCH_START;
		switch ($match) {
			case XOOPS_MATCH_START:
				$criteria->add(new icms_db_criteria_Item('login_name', icms_core_DataFilter::addSlashes(trim($user_login_name)) . '%', 'LIKE'));
				break;

			case XOOPS_MATCH_END:
				$criteria->add(new icms_db_criteria_Item('login_name', '%' . icms_core_DataFilter::addSlashes(trim($user_login_name)), 'LIKE'));
				break;

			case XOOPS_MATCH_EQUAL:
				$criteria->add(new icms_db_criteria_Item('login_name', icms_core_DataFilter::addSlashes(trim($user_login_name))));
				break;

			case XOOPS_MATCH_CONTAIN:
				$criteria->add(new icms_db_criteria_Item('login_name', '%' . icms_core_DataFilter::addSlashes(trim($user_login_name)) . '%', 'LIKE'));
				break;

			default:
				break;
		}
	}
	if (!empty($user_email)) {
		$match = (!empty($user_email_match)) ? (int) $user_email_match : XOOPS_MATCH_START;
		switch ($match) {
			case XOOPS_MATCH_START:
				$criteria->add(new icms_db_criteria_Item('email', icms_core_DataFilter::addSlashes(trim($user_email)) . '%', 'LIKE'));
				break;

			case XOOPS_MATCH_END:
				$criteria->add(new icms_db_criteria_Item('email', '%' . icms_core_DataFilter::addSlashes(trim($user_email)), 'LIKE'));
				break;

			case XOOPS_MATCH_EQUAL:
				$criteria->add(new icms_db_criteria_Item('email', icms_core_DataFilter::addSlashes(trim($user_email))));
				break;

			case XOOPS_MATCH_CONTAIN:
				$criteria->add(new icms_db_criteria_Item('email', '%' . icms_core_DataFilter::addSlashes(trim($user_email)) . '%', 'LIKE'));
				break;

			default:
				break;
		}
	}
	if (!empty($user_url)) {
		$url = formatURL(trim($user_url));
		$criteria->add(new icms_db_criteria_Item('url', $url . '%', 'LIKE'));
	}
	if (!empty($user_icq)) {
		$match = (!empty($user_icq_match)) ? (int) $user_icq_match : XOOPS_MATCH_START;
		switch ($match) {
			case XOOPS_MATCH_START:
				$criteria->add(new icms_db_criteria_Item('user_icq', icms_core_DataFilter::addSlashes(trim($user_icq)) . '%', 'LIKE'));
				break;

			case XOOPS_MATCH_END:
				$criteria->add(new icms_db_criteria_Item('user_icq', '%' . icms_core_DataFilter::addSlashes(trim($user_icq)), 'LIKE'));
				break;

			case XOOPS_MATCH_EQUAL:
				$criteria->add(new icms_db_criteria_Item('user_icq', '%' . icms_core_DataFilter::addSlashes(trim($user_icq))));
				break;

			case XOOPS_MATCH_CONTAIN:
				$criteria->add(new icms_db_criteria_Item('user_icq', '%' . icms_core_DataFilter::addSlashes(trim($user_icq)) . '%', 'LIKE'));
				break;

			default:
				break;
		}
	}
	if (!empty($user_aim)) {
		$match = (!empty($user_aim_match)) ? (int) $user_aim_match : XOOPS_MATCH_START;
		switch ($match) {
			case XOOPS_MATCH_START:
				$criteria->add(new icms_db_criteria_Item('user_aim', icms_core_DataFilter::addSlashes(trim($user_aim)) . '%', 'LIKE'));
				break;

			case XOOPS_MATCH_END:
				$criteria->add(new icms_db_criteria_Item('user_aim', '%' . icms_core_DataFilter::addSlashes(trim($user_aim)), 'LIKE'));
				break;

			case XOOPS_MATCH_EQUAL:
				$criteria->add(new icms_db_criteria_Item('user_aim', icms_core_DataFilter::addSlashes(trim($user_aim))));
				break;

			case XOOPS_MATCH_CONTAIN:
				$criteria->add(new icms_db_criteria_Item('user_aim', '%' . icms_core_DataFilter::addSlashes(trim($user_aim)) . '%', 'LIKE'));
				break;

			default:
				break;
		}
	}
	if (!empty($user_yim)) {
		$match = (!empty($user_yim_match)) ? (int) $user_yim_match : XOOPS_MATCH_START;
		switch ($match) {
			case XOOPS_MATCH_START:
				$criteria->add(new icms_db_criteria_Item('user_yim', icms_core_DataFilter::addSlashes(trim($user_yim)) . '%', 'LIKE'));
				break;

			case XOOPS_MATCH_END:
				$criteria->add(new icms_db_criteria_Item('user_yim', '%' . icms_core_DataFilter::addSlashes(trim($user_yim)), 'LIKE'));
				break;

			case XOOPS_MATCH_EQUAL:
				$criteria->add(new icms_db_criteria_Item('user_yim', icms_core_DataFilter::addSlashes(trim($user_yim))));
				break;

			case XOOPS_MATCH_CONTAIN:
				$criteria->add(new icms_db_criteria_Item('user_yim', '%' . icms_core_DataFilter::addSlashes(trim($user_yim)) . '%', 'LIKE'));
				break;

			default:
				break;
		}
	}
	if (!empty($user_msnm)) {
		$match = (!empty($user_msnm_match)) ? (int) $user_msnm_match : XOOPS_MATCH_START;
		switch ($match) {
			case XOOPS_MATCH_START:
				$criteria->add(new icms_db_criteria_Item('user_msnm', icms_core_DataFilter::addSlashes(trim($user_msnm)) . '%', 'LIKE'));
				break;

			case XOOPS_MATCH_END:
				$criteria->add(new icms_db_criteria_Item('user_msnm', '%' . icms_core_DataFilter::addSlashes(trim($user_msnm)), 'LIKE'));
				break;

			case XOOPS_MATCH_EQUAL:
				$criteria->add(new icms_db_criteria_Item('user_msnm', '%' . icms_core_DataFilter::addSlashes(trim($user_msnm))));
				break;

			case XOOPS_MATCH_CONTAIN:
				$criteria->add(new icms_db_criteria_Item('user_msnm', '%' . icms_core_DataFilter::addSlashes(trim($user_msnm)) . '%', 'LIKE'));
				break;

			default:
				break;
		}
	}
	if (!empty($user_from)) {
		$criteria->add(new icms_db_criteria_Item('user_from', '%' . icms_core_DataFilter::addSlashes(trim($user_from)) . '%', 'LIKE'));
	}
	if (!empty($user_intrest)) {
		$criteria->add(new icms_db_criteria_Item('user_intrest', '%' . icms_core_DataFilter::addSlashes(trim($user_intrest)) . '%', 'LIKE'));
	}
	if (!empty($user_occ)) {
		$criteria->add(new icms_db_criteria_Item('user_occ', '%' . icms_core_DataFilter::addSlashes(trim($user_occ)) . '%', 'LIKE'));
	}

	if (!empty($user_lastlog_more) && is_numeric($user_lastlog_more)) {
		$f_user_lastlog_more = (int) trim($user_lastlog_more);
		$time = time() - (60 * 60 * 24 * $f_user_lastlog_more);
		if ($time > 0) {
			$criteria->add(new icms_db_criteria_Item('last_login', $time, '<'));
		}
	}
	if (!empty($user_lastlog_less) && is_numeric($user_lastlog_less)) {
		$f_user_lastlog_less = (int) trim($user_lastlog_less);
		$time = time() - (60 * 60 * 24 * $f_user_lastlog_less);
		if ($time > 0) {
			$criteria->add(new icms_db_criteria_Item('last_login', $time, '>'));
		}
	}
	if (!empty($user_reg_more) && is_numeric($user_reg_more)) {
		$f_user_reg_more = (int) trim($user_reg_more);
		$time = time() - (60 * 60 * 24 * $f_user_reg_more);
		if ($time > 0) {
			$criteria->add(new icms_db_criteria_Item('user_regdate', $time, '<'));
		}
	}
	if (!empty($user_reg_less) && is_numeric($user_reg_less)) {
		$f_user_reg_less = (int) $user_reg_less;
		$time = time() - (60 * 60 * 24 * $f_user_reg_less);
		if ($time > 0) {
			$criteria->add(new icms_db_criteria_Item('user_regdate', $time, '>'));
		}
	}
	if (!empty($user_posts_more) && is_numeric($user_posts_more)) {
		$criteria->add(new icms_db_criteria_Item('posts', (int) $user_posts_more, '>'));
	}
	if (!empty($user_posts_less) && is_numeric($user_posts_less)) {
		$criteria->add(new icms_db_criteria_Item('posts', (int) $user_posts_less, '<'));
	}
	if (isset($user_mailok)) {
		if ($user_mailok == "mailng") {
			$criteria->add(new icms_db_criteria_Item('user_mailok', 0));
		} elseif ($user_mailok == "mailok") {
			$criteria->add(new icms_db_criteria_Item('user_mailok', 1));
		} else {
			$criteria->add(new icms_db_criteria_Item('user_mailok', 0, '>='));
		}
	}
	if (isset($user_type)) {
		if ($user_type == "inactv") {
			$criteria->add(new icms_db_criteria_Item('level', 0, '='));
		} elseif ($user_type == "actv") {
			$criteria->add(new icms_db_criteria_Item('level', 0, '>'));
		} else {
			$criteria->add(new icms_db_criteria_Item('level', 0, '>='));
		}
	}
	$groups = empty($selgroups) ? array() : array_map('intval', $selgroups);
	$validsort = array("uname", "login_name", "email", "last_login", "user_regdate", "posts");
	$sort = (!in_array($user_sort, $validsort)) ? "uname" : $user_sort;
	$order = "ASC";
	if (isset($user_order) && $user_order == "DESC") {
		$order = "DESC";
	}
	$limit = (!empty($limit)) ? (int) $limit : 50;
	if ($limit == 0 || $limit > 50) {
		$limit = 50;
	}
	$start = (!empty($start)) ? (int) $start : 0;
	$member_handler = icms::handler('icms_member');
	$total = $member_handler->getUserCountByGroupLink($groups, $criteria);
	echo '<div class="CPbigTitle" style="background-image: url(' . ICMS_MODULES_URL . '/system/admin/findusers/images/findusers_big.png)">' . _AM_FINDUS . '</div><br />';
	if ($total == 0) {
		echo "<h4>" . _AM_NOFOUND, "</h4>";
	} elseif ($start < $total) {
		echo sprintf(_AM_USERSFOUND, icms_conv_nr2local($total)) . "<br />";
		echo "<form action='admin.php' method='post' name='memberslist' id='memberslist'><input type='hidden' name='op' value='delete_many' />
		<table width='100%' border='0' cellspacing='1' cellpadding='4' class='outer'><tr><th align='center'><input type='checkbox' name='memberslist_checkall' id='memberslist_checkall' onclick='xoopsCheckAll(\"memberslist\", \"memberslist_checkall\");' /></th><th align='center'>" . _AM_AVATAR . "</th><th align='center'>" . _AM_UNAME . "</th><th align='center'>" . _AM_LOGINNAME . "</th><th align='center'>" . _AM_REALNAME . "</th><th align='center'>" . _AM_EMAIL . "</th><th align='center'>" . _AM_PM . "</th><th align='center'>" . _AM_URL . "</th><th align='center'>" . _AM_REGDATE . "</th><th align='center'>" . _AM_LASTLOGIN . "</th><th align='center'>" . _AM_POSTS . "</th><th align='center'>" . _AM_ACTIONS . "</th></tr>";
		$criteria->setSort($sort);
		$criteria->setOrder($order);
		$criteria->setLimit($limit);
		$criteria->setStart($start);
		$foundusers = &$member_handler->getUsersByGroupLink($groups, $criteria, TRUE);
		$ucount = 0;
		foreach (array_keys($foundusers) as $j) {
			if ($ucount % 2 == 0) {
				$class = 'even';
			} else {
				$class = 'odd';
			}
			$ucount++ ;
			$fuser_avatar = $foundusers[$j]->getVar("user_avatar") ? "<img src='" . ICMS_UPLOAD_URL . "/" . $foundusers[$j]->getVar("user_avatar") . "' alt='' />" : "&nbsp;";
			$fuser_name = $foundusers[$j]->getVar("name") ? $foundusers[$j]->getVar("name") : "&nbsp;";
			echo "<tr class='$class'><td align='center'><input type='checkbox' name='memberslist_id[]' id='memberslist_id[]' value='" . $foundusers[$j]->getVar("uid") . "' /><input type='hidden' name='memberslist_uname[" . $foundusers[$j]->getVar("uid") . "]' id='memberslist_uname[]' value='" . $foundusers[$j]->getVar("uname") . "' /></td>";
			echo "<td>$fuser_avatar</td><td><a href='" . ICMS_URL . "/userinfo.php?uid=" . $foundusers[$j]->getVar("uid") . "'>" . $foundusers[$j]->getVar("uname") . "</a></td><td>" . $foundusers[$j]->getVar("login_name") . "</td><td>" . $fuser_name . "</td><td align='center'><a href='mailto:" . $foundusers[$j]->getVar("email") . "'><img src='" . ICMS_URL . "/images/icons/" . $GLOBALS["icmsConfig"]["language"] . "/email.gif' border='0' alt='";
			printf(_SENDEMAILTO, $foundusers[$j]->getVar("uname", "E"));
			echo "' /></a></td><td align='center'><a href='javascript:openWithSelfMain(\"" . ICMS_URL . "/pmlite.php?send2=1&amp;to_userid=" . $foundusers[$j]->getVar("uid") . "\",\"pmlite\",800,680);'><img src='" . ICMS_URL . "/images/icons/" . $GLOBALS["icmsConfig"]["language"] . "/pm.gif' border='0' alt='";
			printf(_SENDPMTO, $foundusers[$j]->getVar("uname", "E"));
			echo "' /></a></td><td align='center'>";
			if ($foundusers[$j]->getVar("url", "E") != "") {
				echo "<a href='" . $foundusers[$j]->getVar("url", "E") . "' target='_blank'><img src='" . ICMS_URL . "/images/icons/" . $GLOBALS["icmsConfig"]["language"] . "/www.gif' border='0' alt='" . _VISITWEBSITE . "' /></a>";
			} else {
				echo "&nbsp;";
			}
			echo "</td><td align='center'>" . formatTimeStamp($foundusers[$j]->getVar("user_regdate"), "s") . "</td><td align='center'>";
			if ($foundusers[$j]->getVar("last_login") != 0) {
				echo formatTimeStamp($foundusers[$j]->getVar("last_login"), "m");
			} else {
				echo "&nbsp;";
			}
			echo "</td><td align='center'>" . icms_conv_nr2local($foundusers[$j]->getVar("posts")) . "</td>";
			echo "<td align='center'><a href='" . ICMS_MODULES_URL . "/system/admin.php?fct=users&amp;uid=" . $foundusers[$j]->getVar("uid") . "&amp;op=modifyUser'><img src='" . ICMS_IMAGES_SET_URL . "/actions/edit.png' alt=" . _EDIT . " title=" . _EDIT . " /></a></td></tr>\n";
		}
		echo "<tr class='foot'><td><select name='fct'><option value='users'>" . _DELETE . "</option><option value='mailusers'>" . _AM_SENDMAIL . "</option>";
		$group = !empty($group) ? (int) $group : 0;
		if ($group > 0) {
			$member_handler = icms::handler('icms_member');
			$add2group = &$member_handler->getGroup($group);
			echo "<option value='groups' selected='selected'>" . sprintf(_AM_ADD2GROUP, $add2group->getVar('name')) . "</option>";
		}
		echo "</select>&nbsp;";
		if ($group > 0) {
			echo "<input type='hidden' name='groupid' value='" . $group . "' />";
		}
		echo "</td><td colspan='12'>" . icms::$security->getTokenHTML() . "<input type='submit' value='" . _SUBMIT . "' /></td></tr></table></form>\n";
		$totalpages = ceil($total / $limit);
		if ($totalpages > 1) {
			$hiddenform = "<form name='findnext' action='admin.php' method='post'>";
			$skip_vars = array('selgroups');
			foreach ($_POST as $k => $v) {
				if ($k == 'selgroups') {
					foreach ($selgroups as $_group) {
						$hiddenform .= "<input type='hidden' name='selgroups[]' value='" . $_group . "' />\n";
					}
				} elseif ($k == 'XOOPS_TOKEN_REQUEST') {
					// regenerate token value
					$hiddenform .= icms::$security->getTokenHTML() . "\n";
				} else {
					$hiddenform .= "<input type='hidden' name='" . icms_core_DataFilter::htmlSpecialChars($k) . "' value='" . icms_core_DataFilter::htmlSpecialChars(icms_core_DataFilter::stripSlashesGPC($v)) . "' />\n";
				}
			}
			if (!isset($limit)) {
				$hiddenform .= "<input type='hidden' name='limit' value='" . $limit . "' />\n";
			}
			if (!isset($start)) {
				$hiddenform .= "<input type='hidden' name='start' value='" . $start . "' />\n";
			}
			$prev = $start - $limit;
			if ($start - $limit >= 0) {
				$hiddenform .= "<a href='#0' onclick='javascript:document.findnext.start.value=" . $prev . ";document.findnext.submit();'>" . _AM_PREVIOUS . "</a>&nbsp;\n";
			}
			$counter = 1;
			$currentpage = ($start + $limit) / $limit;
			while ($counter <= $totalpages) {
				if ($counter == $currentpage) {
					$hiddenform .= "<strong>" . $counter . "</strong> ";
				} elseif (($counter > $currentpage - 4 && $counter < $currentpage + 4) || $counter == 1 || $counter == $totalpages) {
					if ($counter == $totalpages && $currentpage < $totalpages - 4) {
						$hiddenform .= "... ";
					}
					$hiddenform .= "<a href='#" . $counter . "' onclick='javascript:document.findnext.start.value=" . ($counter - 1) * $limit . ";document.findnext.submit();'>" . $counter . "</a> ";
					if ($counter == 1 && $currentpage > 5) {
						$hiddenform .= "... ";
					}
				}
				$counter++ ;
			}
			$next = $start + $limit;
			if ($total > $next) {
				$hiddenform .= "&nbsp;<a href='#" . $total . "' onclick='javascript:document.findnext.start.value=" . $next . ";document.findnext.submit();'>" . _AM_NEXT . "</a>\n";
			}
			$hiddenform .= "</form>";
			echo "<div style='text-align:center'>" . $hiddenform . "<br />";
			printf(_AM_USERSFOUND, $total);
			echo "</div>";
		}
	}
} else {
	redirect_header('admin.php?fct=findusers', 3, implode('<br />', icms::$security->getErrors()));
}
icms_cp_footer();

