<?php
eval(' function xoops_module_update_' . $mydirname . '( $module ) { return protector_onupdate_base( $module , "' . $mydirname . '" ) ; } ');

if (!function_exists('protector_onupdate_base')) {

	function protector_onupdate_base($module, $mydirname) {
		// transations on module update
		global $msgs; // TODO :-D

		if (!is_array($msgs)) $msgs = array ();

		$db = icms_db_Factory::instance();
		$mid = $module->getVar('mid');

		// TABLES (write here ALTER TABLE etc. if necessary)

		// configs (Though I know it is not a recommended way...)
		$check_sql = "SHOW COLUMNS FROM " . $db->prefix("config") . " LIKE 'conf_title'";
		if (($result = $db->query($check_sql)) && ($myrow = $db->fetchArray($result)) && @$myrow['Type'] == 'varchar(30)') {
			$db->queryF("ALTER TABLE " . $db->prefix("config") . " MODIFY `conf_title` varchar(255) NOT NULL default '', MODIFY `conf_desc` varchar(255) NOT NULL default ''");
		}
		list(, $create_string) = $db->fetchRow($db->query("SHOW CREATE TABLE " . $db->prefix("config")));
		foreach (explode('KEY', $create_string) as $line) {
			if (preg_match('/(\`conf\_title_\d+\`) \(\`conf\_title\`\)/', $line, $regs)) {
				$db->query("ALTER TABLE " . $db->prefix("config") . " DROP KEY " . $regs[1]);
			}
		}
		$db->query("ALTER TABLE " . $db->prefix("config") . " ADD KEY `conf_title` (`conf_title`)");

		// 2.x -> 3.0
		list(, $create_string) = $db->fetchRow($db->query("SHOW CREATE TABLE " . $db->prefix($mydirname . "_log")));
		if (preg_match('/timestamp\(/i', $create_string)) {
			$db->query("ALTER TABLE " . $db->prefix($mydirname . "_log") . " MODIFY `timestamp` DATETIME");
		}

		// TEMPLATES (all templates have been already removed by modulesadmin)
		$tplfile_handler = &icms::handler('icms_view_template_file');
		$tpl_path = __DIR__ . '/templates';
		if ($handler = @opendir($tpl_path . '/')) {
			while (($file = readdir($handler)) !== false) {
				if (substr($file, 0, 1) == '.') continue;
				$file_path = $tpl_path . '/' . $file;
				if (is_file($file_path) && in_array(strrchr($file, '.'), array (
					'.html',
					'.css',
					'.js'
				))) {
					$mtime = (int) (@filemtime($file_path));
					$tplfile = &$tplfile_handler->create();
					$tplfile->setVar('tpl_source', file_get_contents($file_path), true);
					$tplfile->setVar('tpl_refid', $mid);
					$tplfile->setVar('tpl_tplset', 'default');
					$tplfile->setVar('tpl_file', $mydirname . '_' . $file);
					$tplfile->setVar('tpl_desc', '', true);
					$tplfile->setVar('tpl_module', $mydirname);
					$tplfile->setVar('tpl_lastmodified', $mtime);
					$tplfile->setVar('tpl_lastimported', 0);
					$tplfile->setVar('tpl_type', 'module');
					if (!$tplfile_handler->insert($tplfile)) {
						$msgs[] = '<span style="color:#ff0000;">ERROR: Could not insert template <b>' . htmlspecialchars($mydirname . '_' . $file) . '</b> to the database.</span>';
					} else {
						$tplid = $tplfile->getVar('tpl_id');
						$msgs[] = 'Template <b>' . htmlspecialchars($mydirname . '_' . $file) . '</b> added to the database. (ID: <b>' . $tplid . '</b>)';
						// generate compiled file
						if (!icms_view_Tpl::template_touch($tplid)) {
							$msgs[] = '<span style="color:#ff0000;">ERROR: Failed compiling template <b>' . htmlspecialchars($mydirname . '_' . $file) . '</b>.</span>';
						} else {
							$msgs[] = 'Template <b>' . htmlspecialchars($mydirname . '_' . $file) . '</b> compiled.</span>';
						}
					}
				}
			}
			closedir($handler);
		}

		if ((defined('ICMS_PRELOAD_PATH') && !file_exists(ICMS_PRELOAD_PATH . '/protector.php')) && (!defined('PROTECTOR_POSTCHECK_INCLUDED') || !defined('PROTECTOR_PRECHECK_INCLUDED')) && function_exists('icms_copyr')) {
			icms_core_Filesystem::copyRecursive(ICMS_TRUST_PATH . '/modules/protector/patches/ImpressCMS1.1/preload_protector.php', ICMS_PRELOAD_PATH . '/protector.php');
		}

		// Remove the prefix_manager page - no longer relevant, especially in this module
		icms_core_Filesystem::deleteFile(ICMS_TRUST_PATH . '/modules/protector/admin/prefix_manager.php');

		icms_view_Tpl::template_clear_module_cache($mid);

		return true;
	}

	function protector_message_append_onupdate(&$module_obj, &$log) {
		if (is_array(@$GLOBALS['msgs'])) {
			foreach ($GLOBALS['msgs'] as $message) {
				$log->add(strip_tags($message));
			}
		}
	}
}
