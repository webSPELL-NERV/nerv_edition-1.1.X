<?php
/*
##########################################################################
#                                                                        #
#           Version 4       /                        /   /               #
#          -----------__---/__---__------__----__---/---/-               #
#           | /| /  /___) /   ) (_ `   /   ) /___) /   /                 #
#          _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___               #
#                       Free Content / Management System                 #
#                                   /                                    #
#                                                                        #
#                                                                        #
#   Copyright 2005-2011 by webspell.org                                  #
#                                                                        #
#   visit webSPELL.org, webspell.info to get webSPELL for free           #
#   - Script runs under the GNU GENERAL PUBLIC LICENSE                   #
#   - It's NOT allowed to remove this copyright-tag                      #
#   -- http://www.fsf.org/licensing/licenses/gpl.html                    #
#                                                                        #
#   Code based on WebSPELL Clanpackage (Michael Gruber - webspell.at),   #
#   Far Development by Development Team - webspell.org                   #
#                                                                        #
#   visit webspell.org                                                   #
#                                                                        #
##########################################################################
*/

if($_POST['installtype']=="full" AND $_POST['hp_url']) {

	function RandPass($length, $type=0) {		
		$pass = null;
		for ($i = 0; $i < $length; $i++) {
			if($type==0) $rand = rand(1,3);
			else $rand = $type;
			switch($rand) {
				case 1: $pass .= chr(rand(48,57)); break;
				case 2: $pass .= chr(rand(65,90)); break;
				case 3: $pass .= chr(rand(97,122)); break;
			}
		}
		return $pass;
	}

?>

  <tr>
   <td id="step" align="center" colspan="2">
    <ol class="breadcrumb" style="margin-bottom: 5px;">
		<li class="active"><?php echo $_language->module['step0']; ?></li>
		<li class="active"><?php echo $_language->module['step1']; ?></li>
		<li class="active"><?php echo $_language->module['step2']; ?></li>
		<li class="active"><?php echo $_language->module['step3']; ?></li>
		<li class="active"><?php echo $_language->module['step4']; ?></li>
		<li class="active"><b><?php echo $_language->module['step5']; ?></b></li>
		<li class="active"><?php echo $_language->module['step6']; ?></li>
	</ol>
   </td>
  </tr>
  <tr id="headline">
   <td colspan="2" id="title"><h4><?php echo $_language->module['data_config']; ?></h4><br></br></td>
  </tr>
  <tr>
   <td id="content" colspan="2">
   <table class="table table-hover" border="0" cellpadding="0" cellspacing="2" style="width: 600px;margin: 0 auto;">
     <tr>
      <td colspan="2"><b><?php echo $_language->module['database_config']; ?></b><br></br></td>
     </tr>
     <tr>
      <td><?php echo $_language->module['host_name']; ?>:</td>
      <td><input class="form-control" type="text" name="host" size="30" value="localhost" />
      <a class="tooltip" href="#"><img src="images/tooltip.png" alt="" />
      <span><?php echo $_language->module['tooltip_1']; ?></span></a></td>
     </tr>
     <tr>
      <td><?php echo $_language->module['mysql_username']; ?>:</td>
      <td><input class="form-control" type="text" name="user" size="30" />
      <a class="tooltip" href="#"><img src="images/tooltip.png" alt="" />
      <span><?php echo $_language->module['tooltip_2']; ?></span></a></td>
     </tr>
     <tr>
      <td><?php echo $_language->module['mysql_password']; ?>:</td>
      <td><input class="form-control"  type="password" name="pwd" size="30" />
      <a class="tooltip" href="#"><img src="images/tooltip.png" alt="" />
      <span><?php echo $_language->module['tooltip_3']; ?></span></a></td>
     </tr>
     <tr>
      <td><?php echo $_language->module['mysql_database']; ?>:</td>
      <td><input class="form-control" type="text" name="db" size="30" />
      <a class="tooltip" href="#"><img src="images/tooltip.png" alt="" />
      <span><?php echo $_language->module['tooltip_4']; ?></span></a></td>
     </tr>
     <tr>
      <td><?php echo $_language->module['mysql_prefix']; ?>:</td>
      <td><input class="form-control" name="prefix" type="text" value="<?php echo 'ws_'.RandPass(3).'_'; ?>" size="10" />
      <a class="tooltip" href="#"><img src="images/tooltip.png" alt="" />
      <span><?php echo $_language->module['tooltip_5']; ?></span></a></td>
     </tr>
     <tr>
      <td colspan="2"><br /><b><?php echo $_language->module['webspell_config']; ?></b><br></br></td>
     </tr>
     <tr>
      <td><?php echo $_language->module['admin_username']; ?>:</td>
      <td><input class="form-control" type="text" name="adminname" size="30" />
      <a class="tooltip" href="#"><img src="images/tooltip.png" alt="" />
      <span><?php echo $_language->module['tooltip_6']; ?></span></a></td>
     </tr>
     <tr>
      <td><?php echo $_language->module['admin_password']; ?>:</td>
      <td><input class="form-control" type="password" name="adminpwd" size="30" />
      <a class="tooltip" href="#"><img src="images/tooltip.png" alt="" />
      <span><?php echo $_language->module['tooltip_7']; ?></span></a></td>
     </tr>
     <tr>
      <td><?php echo $_language->module['admin_email']; ?>:</td>
      <td><input class="form-control" type="text" name="adminmail" size="30" />
      <a class="tooltip" href="#"><img src="images/tooltip.png" alt="" />
      <span><?php echo $_language->module['tooltip_8']; ?></span></a></td>
     </tr>
   </table>
   <input type="hidden" name="url" value="<?php echo $_POST['hp_url']; ?>" />
   
   <?php
   } else echo '<tr>
   <td id="step" align="center" colspan="2">
   <ol class="breadcrumb" style="margin-bottom: 5px;">
		<li class="active">'.$_language->module['step0'].'</li>
		<li class="active">'.$_language->module['step1'].'</li>
		<li class="active">'.$_language->module['step2'].'</li>
		<li class="active">'.$_language->module['step3'].'</li>
		<li class="active">'.$_language->module['step4'].'</li>
		<li class="active"><b>'.$_language->module['step5'].'</b></li>
		<li class="active">'.$_language->module['step6'].'</li>
	</ol>
   </td>
  </tr>
  <tr id="headline">
   <td colspan="2" id="title">'.$_language->module['finish_install'].'</td>
  </tr>
  <tr>
   <td id="content" colspan="2">
	'.$_language->module['finish_next'];
   ?>
   
   <input type="hidden" name="installtype" value="<?php echo $_POST['installtype']; ?>" />
	
	<br><br>
	<div class="progress">
		<div class="progress-bar progress-bar-striped active" role="progressbar"
		aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" style="width:90%">
		<b>90%</b>
	  </div>
	</div>
   <div align="right"><br /><a href="javascript:document.ws_install.submit()"><img src="images/next.jpg" alt="" /></a></div>
   </td>
  </tr>