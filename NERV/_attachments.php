<?php
include_once("../_magi_class.php");
include_once("_file_upload.php");

class attachments extends magi_class{
	var $_topicID 	= 0;
	var $_userID	= 0;
	var $_files		= "";
	
	function __construct($topicID, $userID=0){
		$this->_topicID = $topicID;
		$this->_userID	= $userID;
	}
	
	public function setFiles($files){
		$this->_files = $files;
	}
	
	public function getAll(){
		$topicID = $this->_topicID;
		$select_query = "SELECT id, filename, description FROM ".PREFIX."attachments WHERE topic_id = $topicID";
		$result = $this->safe_query($select_query);
		if(mysqli_num_rows($result)>0){
			return $result;
		}
		return false;
	}
	
	public function upload(){
		$files = $this->_files;
		$userID = $this->_userID;
		$topicID = $this->_topicID;
		
		$error=array();
		$extension=array("jpeg","jpg","png","gif");
		foreach($files["tmp_name"] as $key=>$tmp_name) 
		{
			$file_name=$files["name"][$key];
			$file_tmp=$files["tmp_name"][$key];
			$ext=pathinfo($file_name,PATHINFO_EXTENSION);
			if(in_array($ext,$extension))
			{
				if(!file_exists("uploads/attachments/".$topicID."_".$userID."_".$file_name))
				{
					move_uploaded_file($file_tmp=$files["tmp_name"][$key],"uploads/attachments/".$topicID."_".$userID."_".$file_name);
				}
				else
				{
					$filename=basename($file_name,$ext);
					$newFileName=$filename.time().".".$ext;
					move_uploaded_file($file_tmp=$files["tmp_name"][$key],"uploads/attachments/".$topicID."_".$userID."_".$newFileName);
				}
				$insert_sql =  "INSERT INTO ".PREFIX."attachments 
								(topic_id, user_id, filename) 
								VALUES ($topicID, $userID, '".$topicID."_".$userID."_".$file_name."')";
				$this->safe_query($insert_sql);
				
			}
			else
			{
				array_push($error,"$file_name, ");
			}
		}
		$_SESSION['attachments']['error'] = $error;
	}
	
	public function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
	}
	
}