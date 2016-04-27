<?php 
	/**
	 * this is a data model for the loaded php.ini file
	 */

	class iniSet_iniDM {

		private $ini_array;
		private $ini_path;
		private $ini_string;
		private $ini_file;

		private $messages = array();

		public function __construct() {}

		/**
		 * loads the ini file in the class properties ($this->ini_array, $this->ini_path, $this->ini_file etc.)
		 * 
		 * @param   string $file_path (optional)
		 * @return  void
		 * @access  public
		 */
		public function loadIni($file_path = null) {
			if(!$file_path) {
				$file_path = php_ini_loaded_file();
			}

			$this->validateFile($file_path);

			$this->ini_path = $file_path;
			$this->iniToArray();
			$this->iniToString();
			$this->iniToObject();
		}

		/**
		 * loads the file as an array using file()
		 * 
		 * @return void
		 * @access protected
		 */
		protected function iniToArray() {
			$this->ini_array = file($this->ini_path);
		}

		/**
		 * loads the file as a string using file_get_contents()
		 * 
		 * @return void
		 * @access protected
		 */
		protected function iniToString() {
			$this->ini_string = file_get_contents($this->ini_path);
		}

		/**
		 * loads the file as an object using SplFileObject()
		 * 
		 * @return void
		 * @access protected
		 */
		protected function iniToObject() {
			$this->ini_file = new SplFileObject($this->ini_path);
		}

		/**
		 * sets the value of a node on ini_array
		 * 
		 * @param  mixed $old_val
		 * @param  mixed $new_val
		 * @param  mixed  $line_number
		 * @return void
		 * @access public
		 */
		public function setNodeValue($old_val, $new_val, $line_number) {
			$this->ini_array[$line_number] = str_replace($old_val, $new_val, $this->ini_array[$line_number]);
		}

		/**
		 * adds a new extension to the ini_array
		 * 
		 * @param  string $extension (extension, zend_extension, zend_extension_ts)
		 * @param  mixed  $value
		 * @return void
		 * @access public
		 */
		public function addExtension($extension, $value) {
			$this->ini_array[] = $extension . ' = ' . $value;
		}

		/**
		 * enables a node in an ini file (works with iniDM->ini_array)
		 * 
		 * @param  string $line
		 * @return void
		 * @access public
		 */
		public function enableNode($line) {
			$semicolon_pos = strpos(trim($this->ini_array[$line]), ';');
			if($semicolon_pos == '0' && $semicolon_pos !== false) {
				$this->ini_array[$line] = preg_replace('/;[ ]*/', '', $this->ini_array[$line]);
			}
		}

		/**
		 * disables a node in an ini file (works with iniDM->ini_array)
		 * 
		 * @param  string $line
		 * @return void
		 * @access public
		 */
		public function disableNode($line) {
			$semicolon_pos = strpos(trim($this->ini_array[$line]), ';');
			if($semicolon_pos === false) {
				$this->ini_array[$line] = '; ' . $this->ini_array[$line];
			}
		}

		/**
		 * validates the file path of the ini file
		 * 
		 * @param   string $file_path
		 * @return  boolean
		 * @access  protected
		 */
		protected function validateFile($file_path) {
			$result = true;
			if(!file_exists($file_path)) {
				$this->messages[] = "file " . $file_path . " does not exist!";
			}

			if(!is_file($file_path)) {
				$this->messages[] = "file " . $file_path . " is not a file!";
			}

			if(!empty($this->messages)) {
				$result = false;
			}

			return $result;
		}


		public function getIniArray() {
			return $this->ini_array;
		}

		public function setIniArray($ini_array) {
			$this->ini_array = is_array($ini_array) ? $ini_array : array($ini_array);
		}

		public function getIniPath() {
			return $this->ini_path;
		}

		public function getIniString() {
			return $this->ini_string;
		}

		public function setIniString($ini_string) {
			$this->ini_string = $ini_string;
		}

		public function getIniFile() {
			return $this->ini_file;
		}

		public function getMessages() {
			return $this->messages;
		}
	}