<?php
include_once("../_magi_class.php");

class file_upload extends magi_class{
	var $_file_input = "";
	var $_destination_path = "";
	var $_max_size = "";
	var $_file_extension = "";
	var $_permitted_extensions = null;
	
	function file_upload($file_input, $destination_path, $max_size = 4, $permitted_extensions = null){
		$this->_file_input = $file_input;
		$this->_destination_path = $destination_path;
		$this->_max_size = $max_size;
		$this->_permitted_extensions = $permitted_extensions;
		$this->_file_extension = $this->get_file_extension($file_input['name']);
		if(!file_exists($destination_path)){
			mkdir($destination_path, 0777);
		}
		if(decoct(fileperms($destination_path)) != 0777){
			@chmod($destination_path, 0777);
		}
		// $size=filesize($destination_path.$file_input['tmp_name'].".".$this->_file_extension);
		// if($size > $max_size*1024) {
			// return false;
		// }
	}
	
	/* UPLOAD DOCUMENT */
	function archive_upload($new_file_name=false){
		$_file_input = $this->_file_input;
		$_destination_path = $this->_destination_path;
		$_file_extension = $this->_file_extension;
		if($this->_permitted_extensions == null){$this->default_archive_extensions();}
		$_permitted_extensions = $this->_permitted_extensions;
		if(!in_array($_file_extension, $_permitted_extensions)){
			return false;
		}
		if($new_file_name){
			if(move_uploaded_file($_file_input['tmp_name'], $_destination_path.$new_file_name.".".$_file_extension)){
				return true;
			}
		}else{
			if(move_uploaded_file($_file_input['tmp_name'], $_destination_path.$_file_input['name'])){
				return true;
			}
		}
		return false;
	}
	
	/* UPLOAD DOCUMENT */
	function document_upload($new_file_name=false){
		$_file_input = $this->_file_input;
		$_destination_path = $this->_destination_path;
		$_file_extension = $this->_file_extension;
		if($this->_permitted_extensions == null){$this->default_document_extensions();}
		$_permitted_extensions = $this->_permitted_extensions;
		if(!in_array($_file_extension, $_permitted_extensions)){
			return false;
		}
		if($new_file_name){
			if(move_uploaded_file($_file_input['tmp_name'], $_destination_path.$new_file_name.".".$_file_extension)){
				return true;
			}
		}else{
			if(move_uploaded_file($_file_input['tmp_name'], $_destination_path.$_file_input['name'])){
				return true;
			}
		}
		return false;
	}
	
	
	/* UPLOAD IMAGE */
	function image_upload($new_file_name=false){
		$_file_input = $this->_file_input;
		$_destination_path = $this->_destination_path;
		$_file_extension = $this->_file_extension;
		if($this->_permitted_extensions == null){$this->default_image_extensions();}
		$_permitted_extensions = $this->_permitted_extensions;
		if(!in_array($_file_extension, $_permitted_extensions)){
			return false;
		}
		if($new_file_name){
			if(file_exists( $_destination_path.$new_file_name.".".$_file_extension)){
				@unlink($_destination_path.$new_file_name.".".$_file_extension);
			}
			if(move_uploaded_file($_file_input['tmp_name'], $_destination_path.$new_file_name.".".$_file_extension)){
				return true;
			}
		}else{
			if(file_exists($_destination_path.$_file_input['name'])){
				@unlink($_destination_path.$_file_input['name']);
			}
			if(move_uploaded_file($_file_input['tmp_name'], $_destination_path.$_file_input['name'])){
				return true;
			}
		}
		return false;
	}
	
	/* UPLOAD IMAGE WIDTH RESIZED JPG */
	function image_upload_resize($resize_width = 800, $resize_height = 600, $new_file_name=false){
		$_file_input = $this->_file_input;
		$_destination_path = $this->_destination_path;
		$_file_extension = $this->_file_extension;
		if($this->_permitted_extensions == null){$this->default_image_extensions();}
		$_permitted_extensions = $this->_permitted_extensions;
		
		$uploadedfile = $_file_input['tmp_name'];
		$_file_extension = $this->get_file_extension($_file_input['name']);
		if(($_file_extension=="jpg" || $_file_extension=="jpeg") && in_array($_file_extension, $_permitted_extensions)){
			$src = imagecreatefromjpeg($uploadedfile);
		} else if($_file_extension=="png" && in_array($_file_extension, $_permitted_extensions)){
			$src = imagecreatefrompng($uploadedfile);
		} else if($_file_extension=="gif" && in_array($_file_extension, $_permitted_extensions)){
			$src = imagecreatefromgif($uploadedfile);
		}else if($_file_extension == "bmp" && in_array($_file_extension, $_permitted_extensions)){
			$src = imagecreatefromwbmp($uploadedfile);
		}else{
			return false;
		}
		 
		list($width,$height)	=	getimagesize($uploadedfile);
		if($width > $resize_width || $height > $resize_height){
			$newheight = $resize_height;
			$newwidth = $resize_width;
			
			if($width > $height && $newheight < $height){
				$newheight = $height / ($width / $newwidth);
			} else if ($width < $height && $newwidth < $width) {
				$newwidth = $width / ($height / $newheight);    
			} else if($height == $width){
				$newwidth = $newwidth;
				$newheight = $height / ($width / $newwidth);
			} else {
				$newwidth = $width;
				$newheight = $height;
			}
			
			$tmp=imagecreatetruecolor($newwidth,$newheight);
			imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);

			$filename = $_destination_path.$_file_input['name'].".jpg";

			imagejpeg($tmp,$filename,100);

			imagedestroy($src);
			imagedestroy($tmp);
			return true;
		}else{
			$this->image_upload($new_file_name);
		}
	}
	
	function get_file_extension($str) {
		// muss überarbeitet werden
		$i = strrpos($str,".");
		if (!$i) { return ""; } 
		$l = strlen($str) - $i;
		$ext = substr($str,$i+1,$l);
		return $ext;
	}
	
	function default_image_extensions(){
		$this->_permitted_extensions = array(
			"jpeg", "jpg", "gif", "png", "bmp"
		);
	}
	
	function default_document_extensions(){
		$this->_permitted_extensions = array(
			"pdf", "docx", "doc", "rtf"
		);
	}
	
	function default_archive_extensions(){
		$this->_permitted_extensions = array(
			"zip", "rar"
		);
	}
}
	
?>
