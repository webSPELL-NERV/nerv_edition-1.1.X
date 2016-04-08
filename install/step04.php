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

if($_POST['hp_url']) {
?>

  <tr>
   <td id="step" align="center" colspan="2">
   <ol class="breadcrumb" style="margin-bottom: 5px;">
		<li class="active"><?php echo $_language->module['step0']; ?></li>
		<li class="active"><?php echo $_language->module['step1']; ?></li>
		<li class="active"><?php echo $_language->module['step2']; ?></li>
		<li class="active"><?php echo $_language->module['step3']; ?></li>
		<li class="active"><b><?php echo $_language->module['step4']; ?></b></li>
		<li class="active"><?php echo $_language->module['step5']; ?></li>
		<li class="active"><?php echo $_language->module['step6']; ?></li>
	</ol>
   </td>
  </tr>
  <tr id="headline">
   <td colspan="2" id="title"><?php echo $_language->module['select_install']; ?></td>
  </tr>
  <tr>
   <td id="content" colspan="2">
   <b><br /><?php echo $_language->module['what_to_do']; ?></b><br /><br />
   <div class="panel-footer">
	   <br /><input disabled type="radio" name="installtype" value="update" /> <?php echo $_language->module['update_31']; ?>
	   <br /><input disabled type="radio" name="installtype" value="update_beta" /> <?php echo $_language->module['update_beta4']; ?>
	   <br /><input disabled type="radio" name="installtype" value="update_beta5" /> <?php echo $_language->module['update_beta5']; ?>
	   <br /><input disabled type="radio" name="installtype" value="update_beta6" /> <?php echo $_language->module['update_beta6']; ?>
	   <br /><input disabled type="radio" name="installtype" value="update_final" /> <?php echo $_language->module['update_40']; ?>
	   <br /><input disabled type="radio" name="installtype" value="update_40100" /> <?php echo $_language->module['update_40100']; ?>
	   <br /><input disabled type="radio" name="installtype" value="update_40102" /> <?php echo $_language->module['update_40102']; ?>
	</div>
   <br /><input type="radio" name="installtype" value="full" checked="checked" /> <b><?php echo $_language->module['new_install']; ?></b>
   <br /><br />
   <div class="alert alert-danger">This is the first NERV-Edition installable Version, so to prevent many bugs, we considered only the fullinstall, in the future it will be updatable.</div>
   <input type="hidden" name="hp_url" value="<?php echo $_POST['hp_url']; ?>" />
   <br><br>
	<div class="progress">
		<div class="progress-bar progress-bar-striped active" role="progressbar"
		aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width:80%">
		<b>80%</b>
	  </div>
	</div>
   <div align="right"><br /><a href="javascript:document.ws_install.submit()"><img src="images/next.jpg" alt="" /></a></div>
   </td>
  </tr>

<?php } ?>