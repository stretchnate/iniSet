<?php 
	require_once('Classes/iniSet.php');

	define('INI_FILE', 'C:/php5/php.ini');

	echo iniSet::NEW_LINE;

	$enable  = false;
	$disable = false;
	$restart = false;

	if(file_exists(INI_FILE)) {
		$ini_set_obj = new iniSet(INI_FILE);

		foreach($argv as $arg) {
			switch($arg) {
				case 'enable':
					$enable = true;
					break;

				case 'disable':
					$disable = true;
					break;

				case 'restart':
					$restart = true;
					break;

				case 'debug':
					$ini_set_obj->setDebug(true);
					break;

				default:
					if(strpos($arg, "=") === false) {
						continue;
					}

					$pieces = explode("=", $arg);
					$nodes[$pieces[0]] = $pieces[1];
					break;
			}
		}

		foreach($nodes as $node => $value) {
			$ini_set_obj->setValue($node, $value);

			if($enable === true) {
				$ini_set_obj->enableNode($node);
			} elseif($disable === true) {
				$ini_set_obj->disableNode($node);
			}
		}

		$ini_set_obj->save();

		if($restart === true) {
			$ini_set_obj->restartApache();
		}
	} else {
		iniSet::log('file ' . INI_FILE . ' does not exist');
	}
	
	iniSet::log(iniSet::NEW_LINE . 'finished searching ' . INI_FILE);

	die(":)");