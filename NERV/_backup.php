<?php
include_once("../_magi_class.php");

class backup extends magi_class{
	
	//backup_tables('localhost','username','password','blog');
	var $_tables = "";
	var $_destination_dir = "";
	var $_user_id = 0;
	
	public function backup($userID=0, $destinationDir = "admin/backups/",$tables = '*'){
		$this->_tables = $tables;
		$this->_destination_dir = $destinationDir;
		$this->_user_id = $userID;
		if(!file_exists('backups')){
			mkdir('backups');
			chmod('backups', 0777);
		}
	}
	
	public function doIT(){
		$dir 		= $this->_destination_dir;
		$tables		= $this->_tables;
		$filename_db = $this->backup_tables($dir, $tables);
		$filename_ws = $this->backup_webspell('./', $dir, $filename_db);
		$files = array(
				$dir.$filename_db.".sql" => 'Datenbank Backup (<b>.sql</b>)',
				$dir.$filename_ws.".zip" => 'Dateien Backup (<b>.zip</b>)'
		);
		$this->insertBackups($files);
	}
	
	public function FILESBackup() {
		$dir 		= $this->_destination_dir;
		$tables		= $this->_tables;
		$filename = 'db-backup-'.time().'-'.(md5(time()));
		//$filename_db = $this->backup_tables($dir, $tables);
		$filename_ws = $this->backup_webspell('./', $dir, $filename);
		$files = array(
					$dir.$filename_ws.".zip" => 'Dateien Backup (<b>.zip</b>)'
				);
		$this->insertBackups($files);
	}
	
	public function SQLBackup() {
		$dir 		= $this->_destination_dir;
		$tables		= $this->_tables;
		$filename_db = $this->backup_tables($dir, $tables);
		$files = array(
					$dir.$filename_db.".sql" => 'Datenbank Backup (<b>.sql</b>)'
				);
		$this->insertBackups($files);
	}
	
	public function backup_webspell($url, $dir, $filename){
		$filename = "ws".substr($filename, 2, strlen($filename));
		$rootPath = ($url);
		// Initialize archive object
		$zip = new ZipArchive();
		$zip->open($dir.$filename.'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

		// Create recursive directory iterator
		/** @var SplFileInfo[] $files */
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($rootPath),
			RecursiveIteratorIterator::LEAVES_ONLY
		);

		foreach ($files as $name => $file)
		{
			// Skip directories (they would be added automatically)
			if (!$file->isDir())
			{
				// Get real and relative path for current file
				$filePath = $file->getRealPath();
				$relativePath = substr($filePath, strlen($rootPath) + 1);
				
				if(!strpos($filePath, "db-backup-") && !strpos($filePath, "ws-backup-")){
					$zip->addFile($filePath, $relativePath);
				} 
			}
		}

		// Zip archive will be created only after closing object
		$zip->close();
		return $filename;
	}
	
	// creates a full backup of a database
	private function backup_tables($dir,$tables = '*')
	{
		$return = "";
		if($tables == '*')
		{
			$tables = array();
			$result = $this->safe_query('SHOW TABLES');
			while($row = mysqli_fetch_row($result))
			{
				$tables[] = $row[0];
			}
		}
		else
		{
			$tables = is_array($tables) ? $tables : explode(',',$tables);
		}
		foreach($tables as $table)
		{
			$result = $this->safe_query('SELECT * FROM '.$table);
			$num_fields = mysqli_num_fields($result);
			
			$return.= 'DROP TABLE '.$table.';';
			$row2 = mysqli_fetch_row($this->safe_query('SHOW CREATE TABLE '.$table));
			$return.= "\n\n".$row2[1].";\n\n";
			
			for ($i = 0; $i < $num_fields; $i++) 
			{
				while($row = mysqli_fetch_row($result))
				{
					$return.= 'INSERT INTO '.$table.' VALUES(';
					for($j=0; $j < $num_fields; $j++) 
					{
						$row[$j] = addslashes($row[$j]);
						$row[$j] = ereg_replace("\n","\\n",$row[$j]);
						if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
						if ($j < ($num_fields-1)) { $return.= ','; }
					}
					$return.= ");\n";
				}
			}
			$return.="\n\n\n";
		}
		$filename = 'db-backup-'.time().'-'.(md5(time()));
		$handle = fopen($dir.$filename.'.sql','w+');
		fwrite($handle,$return);
		fclose($handle);
		if($this->create_zip(array($dir.$filename.".sql"),$dir.$filename.".zip", true)){
			return $filename;
		}else{
			return "Fehler, ZIP nicht erstellt ...";
		}
		array_map('unlink', glob($dir."*.sql"));
		//return $filename;
	}
	
	// creates a zip from file array
	private function create_zip($files = array(),$destination = '',$overwrite = false) {
		if(file_exists($destination) && !$overwrite) { return false; }
		$valid_files = array();
		if(is_array($files)) {
			foreach($files as $file) {
				if(file_exists($file)) {
					$valid_files[] = $file;
				}
			}
		}
		if(count($valid_files)) {
			$zip = new ZipArchive();
			if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
			}
			foreach($valid_files as $file) {
				$zip->addFile($file,$file);
			}
			$zip->close();
			return file_exists($destination);
		}
		else
		{
			return false;
		}
	}
	
	
	//$files = array('file' => 'description', ...)
	private function insertBackups($files){
		$userID = $this->_user_id;
		if(count($files)>0){
			foreach($files as $file => $description){
				if(!file_exists($file)){
					echo $file." gibts nicht!";
					return false;
				}
			}
			foreach($files as $file => $description){
				$insert_query = "INSERT INTO ".PREFIX."backups (
					filename,
					description,
					createdby
				) VALUES (
					'$file',
					'$description',
					$userID
				)";
				$result = $this->safe_query($insert_query);
				unset($result);
			}
		}
		
	}
	
	public function getBackup($bid){
		$select_query = "SELECT id,filename, description, createdby, createdate FROM ".PREFIX."backups WHERE id LIKE '$bid' LIMIT 1";
		$result = $this->safe_query($select_query);
		$row = mysqli_fetch_array($result);
		$backup = array(
			'id' 			 => $row['id'],
			'filename' 		 => $row['filename'],
			'description' 	 => $row['description'],
			'createdby' 	 => $row['createdby'],
			'createdate'	 => $row['createdate']
		);
		return $backup;
	}
	
	public function getAllBackups(){
		$select_query = "SELECT id,filename, description, createdby, createdate FROM ".PREFIX."backups ORDER BY createdate DESC";
		$result = $this->safe_query($select_query);
		$backups = array();
		while($row = mysqli_fetch_array($result)) {
			$backups[] = array(
				'id' 			 => $row['id'],
				'filename' 		 => $row['filename'],
				'description' 	 => $row['description'],
				'createdby' 	 => $row['createdby'],
				'createdate'	 => $row['createdate']
			);
		}
		return $backups;
	}
	
	public function deleteBackup($bid){
		$backup = $this->getBackup($bid);
		
		$delete_query = "DELETE FROM ".PREFIX."backups WHERE id LIKE '$bid'";
		$result = $this->safe_query($delete_query);

		$dir = $this->_destination_dir;
		
		$filename = $backup['filename'];
		if(file_exists($filename)){
			unlink($filename); 
		}
		
		if(substr($filename, -4) == ".sql"){
			unlink(substr($filename,0,strlen($filename)-4).".zip"); 
		};
		return false;
	}
	
	public function deleteAll(){
		$delete_query = "DELETE FROM ".PREFIX."backups";
		$dir = $this->_destination_dir;
		echo $dir;
		$files = glob($dir.'*'); 
		print_r($files);
		foreach($files as $file){
		  if(is_file($file))
			unlink($file); 
		}
		$result = $this->safe_query($delete_query);
		return $result;
	}
	
}