<?php 
	require_once('iniSet/iniDM.php');
	include_once("error.php");
	/**
	 * sets values on the ini file
	 */
	class iniSet {

		const   NEW_LINE = "\n";
		const   DEBUG    = 'DEBUG';
		const   LOG_FILE = "C:/logs/iniSet_log.txt";

		private $ini_model;
		private $debug = false;

		private static $extensions = array("extension", "zend_extension", "zend_extension_ts");
		/**
		 * constructor, loads the iniSet_iniDM
		 * 
		 * @param   string $file_path (optional)
		 * @return  void
		 * @access  public
		 */
		public function __construct($file_path = null) {
			if($this->debug === true) {
				self::log("DEBUG MODE -- iniSet::__construct()");
			}

			$this->ini_model = new iniSet_iniDM();
			$this->ini_model->loadIni($file_path);

			$messages = $this->ini_model->getMessages();
			if(!empty($messages)) {
				foreach($messages as $message) {
					self::log($message);
					die();
				}
			}
		}

		/**
		 * sets the value of a node or an extension
		 * 
		 * @param  string $node
		 * @param  string $value
		 * @return void
		 * @access protected
		 */
		public function setValue($node, $value) {
			if($this->debug === true) {
				self::log("DEBUG MODE -- iniSet::setValue()");
			}

			if(in_array($node, self::$extensions)) {
				$this->setExtensionValue($node, $value);
			} else {
				$this->setNodeValue($node, $value);
			}
		}

		/**
		 * sets the value of a node in an ini file
		 * 
		 * @param  string $node
		 * @param  string $value
		 * @return void
		 * @access protected
		 */
		protected function setNodeValue($node, $value) {
			if($this->debug === true) {
				self::log("DEBUG MODE -- iniSet::setNodeValue()");
			}

			$ini_array   =& $this->ini_model->getIniArray();
			$line_number = $this->findNode($node);

			if($line_number !== false) {
				self::log('setting ' . $node . ' to ' . $value);
				$line_array              = explode("=", $ini_array[$line_number]);
				$old_val                 = trim($line_array[1]);
				$this->ini_model->setNodeValue($old_val, $value, $line_number);
			} else {
				self::log('Node ' . $node . ' does not exist');
			}
		}

		/**
		 * sets the value of an extension node in an ini file
		 * 
		 * @param  string $node
		 * @param  string $value
		 * @return void
		 * @access protected
		 */
		protected function setExtensionValue($extension, $value) {
			$ini_array   =& $this->ini_model->getIniArray();
			$line_number = $this->findExtension($value);

			if($line_number !== false) {
				self::log('setting ' . $node . ' to ' . $value);
				$line_array              = explode("=", $ini_array[$line_number]);
				$old_val                 = trim($line_array[1]);
				$this->ini_model->setNodeValue($old_val, $value, $line_number);
			} else {
				self::log('adding extension ' . $value);
				$this->ini_model->addExtension($extension, $value);
			}
		}

		/**
		 * enables a node in an ini file (works with iniDM->ini_array)
		 * 
		 * @param  string $node
		 * @param  string $line (optional)
		 * @return void
		 * @access public
		 */
		public function enableNode($node, $line = null) {
			if($this->debug === true) {
				self::log("DEBUG MODE -- iniSet::enableNode()");
			}

			if(!$line) {
				$line = $this->findNode($node);
			}

			$this->ini_model->enableNode($node, $line);
			self::log('enabled ' . $node);
		}

		/**
		 * disables a node in an ini file (works with iniDM->ini_array)
		 * 
		 * @param  string $node
		 * @param  string $line (optional)
		 * @return void
		 * @access public
		 */
		public function disableNode($node, $line = null) {
			if($this->debug === true) {
				self::log("DEBUG MODE -- iniSet::disableNode()");
			}

			if(!$line) {
				$line = $this->findNode($node);
			}

			$this->ini_model->disableNode($node, $line);
			self::log('disabled ' . $node);
		}

		/**
		 * enables a extension in an ini file (works with iniDM->ini_array)
		 * 
		 * @param  string $extension
		 * @param  string $line (optional)
		 * @return void
		 * @access public
		 */
		public function enableExtension($extension, $line = null) {
			if($this->debug === true) {
				self::log("DEBUG MODE -- iniSet::enableExtension()");
			}

			if(!$line) {
				$line = $this->findExtension($extension);
			}

			$this->ini_model->enableNode($line);
			self::log('enabled ' . $extension);
		}

		/**
		 * disables a extension in an ini file (works with iniDM->ini_array)
		 * 
		 * @param  string $extension
		 * @param  string $line (optional)
		 * @return void
		 * @access public
		 */
		public function disableExtension($extension, $line = null) {
			if($this->debug === true) {
				self::log("DEBUG MODE -- iniSet::disableExtension()");
			}

			if(!$line) {
				$line = $this->findExtension($extension);
			}

			$this->ini_model->disableNode($line);
			self::log('disabled ' . $extension);
		}

		/**
		 * searches an ini file for a specific node
		 * 
		 * @param  string $node
		 * @return void
		 * @access public
		 */
		public function findNode($node) {
			if($this->debug === true) {
				self::log("DEBUG MODE -- iniSet::findNode()");
			}

			$i = 0;
			$found = false;

			foreach($this->ini_model->getIniArray() as $line) {
				$line     = trim($line);
				$line_pos = strpos($line, $node);
				if( $line_pos !== false) {
					//get the expected position of the node name
					$expected_pos = '0';
					if(substr($line,0,1) == ';') {
						$expected_pos = '2';
					}

					if($line_pos <= $expected_pos) {
						self::log('found ' . $node . ' on line ' . ($i + 1));
						$found = true;
						break;
					} else {
						self::log('possible match for ' . $node . ' on line ' . ($i+1) . ' --> ' . $line);
					}
				}
				$i++;
			}

			if($found === false) {
				$i = false;
			}

			return $i;
		}

		/**
		 * searches an ini file for a specific extension
		 * 
		 * @param  string $extension
		 * @return void
		 * @access public
		 */
		public function findExtension($extension) {
			if($this->debug === true) {
				self::log("DEBUG MODE -- iniSet::findExtension()");
			}

			$i = 0;
			$found = false;

			foreach($this->ini_model->getIniArray() as $line) {
				$pieces = explode("=", $line);
				if(is_array($pieces) && count($pieces) > 1) {
					$pieces[1] = trim($pieces[1]);
					$extension = trim($extension);

					if( $pieces[1] == $extension) {
						self::log('found ' . $extension . ' on line ' . ($i + 1));
						$found = true;
						break;
					}
				}
				$i++;
			}

			if($found === false) {
				$i = false;
			}

			return $i;
		}

		/**
		 * saves the ini file from the format specified
		 * 
		 * @param  string $format (array, string, object)
		 * @return mixed (number of bytes written or false on failure)
		 * @access public
		 */
		public function save($format = 'array') {
			$result = false;
			if($this->backup()) {
				switch($format) {
					case 'array':
						$result = $this->saveFile($this->ini_model->getIniArray());
						break;
					
					case 'object':
						$result = $this->saveFileObject();
						break;
					
					case 'string':
						$result = $this->saveFile($this->ini_model->getIniString());
						break;

					default:
						self::log('Save format ' . $format . ' does not exist');
				}
			}

			return $result;
		}

		/**
		 * writes the contents to a file
		 * 
		 * @param mixed $data (string/array)
		 * @return mixed (number of bytes written or false on failure)
		 * @access private
		 */
		private function saveFile($data) {
			return file_put_contents($this->ini_model->getIniPath(), $data);
		}

		/**
		 * creates a backup of the ini file
		 * 
		 * @return boolean
		 * @access private
		 */
		private function backup() {
			return copy($this->ini_model->getIniPath(), $this->ini_model->getIniPath() . ".bak");
		}

		/**
		 * generates an array from the file object and writes the data to a file
		 * 
		 * @return mixed (number of bytes written or false on failure)
		 * @access private
		 */
		private function saveFileObject() {
			$lines = array();
			$ini_file = $this->ini_model->getIniFile();
			$ini_file->rewind();
			while($ini_file->valid()) {
				$lines[] = $ini_file->current();
				$ini_file->next();
			}
			
			return $this->saveFile($lines);
		}

		/**
		 * log message
		 *
		 * @param string $message - required
		 * @param bool   $echo    - optional (true)
		 * @param string $mode    - optional ('a') - mode for fopen
		 * @return void
		 */
		public static function log($message, $echo = true, $mode = 'a') {
			$handle = fopen(self::LOG_FILE, $mode);
			fwrite($handle, $message . self::NEW_LINE);
			fclose($handle);
			
			if($echo === true) {
				echo $message . self::NEW_LINE;
			}
		}

		/**
		 * converts non boolean values to boolean
		 * 
		 * @param mixed $var
		 * @return boolean
		 * @access public
		 */
		public function getBoolean($var) {
			if(is_bool($var)) {
				return $var;
			}

			$return = false;

			if($var != 'false') {
				$return = (bool)$var;
			}

			return $return;
		}


		/**
		 * restarts the apache server
		 * 
		 * @return void
		 * @static
		 * @access public
		 */
		public static function restartApache() {
			self::log('restarting apache' . self::NEW_LINE);
			exec('C:/Apache/Apache2.2/bin/httpd.exe -k restart', $output);
			
			if(isset($output) && is_array($output)) {
				foreach($output as $message) {
					self::log($message);
				}
			}
		}

		public function setDebug($debug) {
			$this->debug = $this->getBoolean($debug);
		}
	}