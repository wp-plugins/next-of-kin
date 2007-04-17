<?php
/*
Plugin Name: Next Of Kin
Plugin URI: http://tzafrir.net/nextofkin/
Description: This plugin will send a text of your choice to an email address(es) in the unfortunate case in which you lose ability to access your blog, due to disability or, I hope not, untimely death. Useful for sending your person of choice your blog password, domain password, web host's control panel password, your e-mail password, etc.
Version: 1.1
Author: Tzafrir Rehan
Author URI: http://tzafrir.net
*/

/*  Copyright 2007  Tzafrir Rehan  (email : tzafrir@tzafrir.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

load_plugin_textdomain('nextofkin','wp-content/plugins/nextofkin');
update_option('tz_nextofkin_ver', '1.1');
function tz_nextofkin_activate() { //Create option in database
global $current_user;
add_option('tz_nextofkin_ll_'.$current_user->user_login, time(), $current_user->user_login.'\'s last login time', 'yes');
add_option('tz_nextofkin_lastiteration', (time() - 7200));
}

function tz_nextofkin_init() { 													//Runs every load of wordpress
global $current_user;																	//but only if
	if (get_option('tz_nextofkin_active_'.$current_user->user_login) == true 
	&& (time() - get_option('tz_nextofkin_ll_'.$current_user->user_login)) > 43200) {//the plugin is activated in own menu per current user (not just plugins menu)
			//and writes the current time as last login time if 12 hours passed since last write
			update_option('tz_nextofkin_ll_'.$current_user->user_login, time());
				}
				
	}
	// Here is the actual work of the plugin:
	$weeks = 604800; //seconds in a week
	if ((time() - get_option('tz_nextofkin_lastiteration')) > 7200) { // The following is a bit heavy, so we'll only do it once every two hours
		$tz_nok_users = get_option('tz_nextofkin_users');// get users list
		if (is_array($tz_nok_users)) {
			foreach ($tz_nok_users as $user) {
				if ($user != '') {
				$cur_options = get_option('tz_nextofkin_options_'.$user);
				$cur_optionsm = get_option('tz_nextofkin_optionsm_'.$user);
				
				if ((time() - get_option('tz_nextofkin_ll_'.$user)) > ($cur_options['interval1'] * $weeks) 
				&& get_option('tz_nextofkin_step1_'.$user) != true) { // After interval 1
						if ($cur_optionsm['email1'] != '') { tz_send_mail($cur_options['name'], $cur_options['email'], $cur_options['email'], $cur_optionsm['subject1'], $cur_optionsm['email1']);}
						update_option('tz_nextofkin_step1_'.$user, true);
				}
				
				if ((time() - get_option('tz_nextofkin_ll_'.$user)) > (($cur_options['interval1'] + $cur_options['interval2']) * $weeks)
				&& get_option('tz_nextofkin_step2_'.$user) != true) { // After interval 2
						if ($cur_optionsm['email2'] != '') { tz_send_mail($cur_options['name'], $cur_options['email'], $cur_options['email'], $cur_optionsm['subject2'], $cur_optionsm['email2']);}
						if ($cur_optionsm['email3'] != '') { tz_send_mail($cur_options['name'], $cur_options['email'], $cur_options['email_other'], $cur_optionsm['subject3'], $cur_optionsm['email3']);}
						update_option('tz_nextofkin_step2_'.$user, true);
				}
				
				if ((time() - get_option('tz_nextofkin_ll_'.$user)) > (($cur_options['interval1'] + $cur_options['interval2'] + $cur_options['interval3']) * $weeks)
				&& get_option('tz_nextofkin_step3_'.$user) != true) { // After interval 3 (Death)
						if ($cur_optionsm['email4'] != '') { tz_send_mail($cur_options['name'], $cur_options['email'], $cur_options['email_other'], $cur_optionsm['subject4'], $cur_optionsm['email4']);}
						update_option('tz_nextofkin_step3_'.$user, true);
				}
			}
		
		}
			update_option('tz_nextofkin_lastiteration', time());
		}
	}

function tz_nextofkin_menu() { // function that calls the option menu function
    if (function_exists('add_options_page')) {
        add_options_page(__('Next Of Kin Options', 'nextofkin'), __('Next Of Kin', 'nextofkin'), 8, basename(__FILE__), 'tz_nextofkin_menupage');
    }
 }
 
 function tz_send_mail($from, $fromemail, $to, $subject, $message) {
		$subject = '[' . $from . '] ' . $subject;
		$charset = get_settings('blog_charset');
		$headers  = "From: \"{$from}\" <{$fromemail}>\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-Type: text/plain; charset=\"{$charset}\"\n";
		return wp_mail($to, $subject, $message, $headers);
	}
 
function tz_nextofkin_menupage() { //the options menu
global $current_user;
$tz_nok_users = get_option('tz_nextofkin_users');// get users list
echo '<div class="wrap"><h2>'.__('Next Of Kin Options', 'nextofkin').'</h2>';
 if (isset($_POST['tz_nok_update']) || isset($_POST['tz_nok_update_mails']) || isset($_POST['tz_nok_reset'])) {
    ?><div class="updated"><p><strong><?php 
_e('Options updated succesfully.', 'nextofkin'); 
			if ($_POST['tz_nok_act'] == 'on') {
				echo '<br />
				'.__('Plugin activated - Now go and edit your messages and options below!', 'nextofkin'); }
	if (isset($_POST['tz_nok_reset'])) {
		echo '<br />
		'.__('Timestamps and sent mails status reset.', 'nextofkin');
		}
				
    ?></strong></p></div><?php 
	}

		if (isset($_POST['tz_nok_reset'])) {
			update_option('tz_nextofkin_ll_'.$current_user->user_login, time());
			update_option('tz_nextofkin_step1_'.$current_user->user_login, false);
			update_option('tz_nextofkin_step2_'.$current_user->user_login, false);
			update_option('tz_nextofkin_step3_'.$current_user->user_login, false);
			}
			
	
		if (isset($_POST['tz_nok_update'])) {
			if ($_POST['tz_nok_act'] == 'on') { // If plugin was just enabled (through options menu, not activated in plugins page)
				update_option('tz_nextofkin_active_'.$current_user->user_login, true); //then set the "activated" option to true. this enables the rest of the menu, and the plugin functionality.
				$tz_nok_users = get_option('tz_nextofkin_users');
				if ( !is_array($tz_nok_users) ) {
					$tz_nok_users = array($current_user->user_login);
					}
					else {
					
						if (!in_array($current_user->user_login, $tz_nok_users)) {
						$tz_nok_users[] = $current_user->user_login;
							}
						}
			}
				else { 
				update_option('tz_nextofkin_active_'.$current_user->user_login, false);
				$counter = 1;
				while ( $counter <= count($tz_nok_users) ) {
						if ($tz_nok_users[($counter - 1)] == $current_user->user_login) $tz_nok_users[($counter - 1)] = ''; //remove from userlist on deactivation
						$counter = $counter + 1;
						}
				}
				update_option('tz_nextofkin_users', $tz_nok_users);
				}
		
		echo '<form method="post">
		<p><label><input type="checkbox" name="tz_nok_act"';
		if (get_option('tz_nextofkin_active_'.$current_user->user_login) == true) { // all options are user specific. will this work in MU?
			echo 'checked="checked"';
			}
		echo ' />'.__('Enable Next Of Kin plugin functionality', 'nextofkin').'</label></p>
		
		';
		
if (get_option('tz_nextofkin_active_'.$current_user->user_login) == true) { // Most of what comes next only loads if plugin is enabled for the logged in user.
				$options = get_option('tz_nextofkin_options_'.$current_user->user_login); // load the options from DB
		if ( !is_array($options) )
			$options = array('email'=>$current_user->user_email, 'email_other'=>'', 'name'=>$current_user->display_name, 'interval1'=>'2', 'interval2'=>'1', 'interval3'=>'1'); // Default values. Getting self email from profile, but can be changed, also to multiple addresses.
		if (isset($_POST['tz_more'])) { // If hit "update options" button when seeing all options

		if ($_POST['tz_nok_email'] == '') {$options['email'] = $current_user->user_email;} //No blank self email allowed - will result in false deaths.
		else { 
				$options['email'] = strip_tags(stripslashes($_POST['tz_nok_email']));
			}
			if ($_POST['tz_nok_name'] == '') {$options['name'] = $current_user->display_name;} //No blank name allowed - we want the recepient to know who's sending this
		else { 
				$options['name'] = strip_tags(stripslashes($_POST['tz_nok_name']));
			}
			$options['email_other'] = strip_tags(stripslashes($_POST['tz_nok_email_other']));
			
			$options['interval1'] = strip_tags(stripslashes($_POST['tz_nok_int1']));
			$options['interval2'] = strip_tags(stripslashes($_POST['tz_nok_int2']));
			$options['interval3'] = strip_tags(stripslashes($_POST['tz_nok_int3']));
			update_option('tz_nextofkin_options_'.$current_user->user_login, $options); //write it all to DB
		}
		
		
			$optionsm = get_option('tz_nextofkin_optionsm_'.$current_user->user_login); // load the messages from DB
		if ( !is_array($optionsm) ) //default values
			$optionsm = array('email1'=>__('This email is automatically sent to you because you did not login at your blog for two weeks. If you will not login within the next week, the system will assume the worst and will take further actions to send your preset details to the person(s) you chose to have those sent to.', 'nextofkin')
			, 'email2'=>__('The text here will be sent to you in case you have not logged in to the system after receiving the first email. On the same time the following email will be sent to whoever you chose to receive your personal details.', 'nextofkin')
			, 'email3'=>__('The text here will be sent to your chosen person(s) once you do not log in to the system after receiving the first notice. Write it carefully - you sadly might not be alive at this point.', 'nextofkin')
			, 'email4'=>__('This is the final text that will be sent to your chosen person(s) after you do not log in to the system for a month (by default). Here you should write passwords and logins to your blog, domain management panel, web management panel, secret bank account numbers, and whatever information you feel should be left after you have gone. Again, write this carefully! By this point, it is assumed you are deceased for several weeks.', 'nextofkin')
			, 'subject1'=>__('This is the email subject', 'nextofkin')
			, 'subject2'=>__('This is the email subject', 'nextofkin')
			, 'subject3'=>__('This is the email subject', 'nextofkin')
			, 'subject4'=>__('This is the email subject', 'nextofkin')
			);
			
			if (isset($_POST['tz_nok_update_mails'])) { // If user hits Update Messages button
			$optionsm['email1'] = strip_tags(stripslashes($_POST['tz_nok_msg1']));
			$optionsm['email2'] = strip_tags(stripslashes($_POST['tz_nok_msg2']));
			$optionsm['email3'] = strip_tags(stripslashes($_POST['tz_nok_msg3']));
			$optionsm['email4'] = strip_tags(stripslashes($_POST['tz_nok_msg4']));
			$optionsm['subject1'] = strip_tags(stripslashes($_POST['tz_nok_subj1']));
			$optionsm['subject2'] = strip_tags(stripslashes($_POST['tz_nok_subj2']));
			$optionsm['subject3'] = strip_tags(stripslashes($_POST['tz_nok_subj3']));
			$optionsm['subject4'] = strip_tags(stripslashes($_POST['tz_nok_subj4']));

			update_option('tz_nextofkin_optionsm_'.$current_user->user_login, $optionsm); // write them all to DB
			}
		
		//The html forms
		echo '<fieldset class="options">
		<input type="hidden" name="tz_more" value="1" />
                                <table class="optiontable">
                                        <tbody>
                                        <tr valign="top">
                                                <th scope="row">'.__('Your Email(s): ', 'nextofkin').' '.__('(seperate multiple addresses with commas)', 'nextofkin').'</th>
                                                <td>
                                                        <input style="direction:ltr;" type="text" name="tz_nok_email" value="' . $options['email'] . '" size="50"/>
                                                </td>
                                        </tr>
										<tr valign="top">
                                                <th scope="row">'.__('Email(s) to receive your electronic will: ', 'nextofkin').' '.__('(seperate multiple addresses with commas)', 'nextofkin').'</th>
                                                <td>
                                                        <input style="direction:ltr;" type="text" name="tz_nok_email_other" value="' . $options['email_other'] . '" size="50"/>
                                                </td>
                                        </tr>
										<tr valign="top">
                                                <th scope="row">'.__('Your name: (will appear as sender)', 'nextofkin').'</th>
                                                <td>
                                                        <input type="text" name="tz_nok_name" value="' . $options['name'] . '" size="50"/>
                                                </td>
                                        </tr>
										
                                        <tr valign="top">
                                                <th scope="row">'.__('Interval 1: (time between last login into the system and first warning mail)', 'nextofkin').'</th>
                                                <td>
														<select name="tz_nok_int1" size="1">';
														$tz_int = 1;
														while ($tz_int < 11) {
														echo '<option '.($options['interval1']==$tz_int ? "selected='selected'" : '').' value="'.$tz_int.'">'.$tz_int.'</option>';
														$tz_int++;
														}
														echo '</select> '.__('Weeks', 'nextofkin').'
														</td>
                                        </tr>
                                        <tr>
                                              <th scope="row">'.__('Interval 2: (time between first warning mail to seconds warning mail)', 'nextofkin').'</th>
                                                <td>
                                                        <select name="tz_nok_int2" size="1">';
														$tz_int = 1;
														while ($tz_int < 11) {
														echo '<option '.($options['interval2']==$tz_int ? "selected='selected'" : '').' value="'.$tz_int.'">'.$tz_int.'</option>';
														$tz_int++;
														}
														echo '</select> '.__('Weeks', 'nextofkin').'
                                                </td>
                                        </tr>
										<tr>
                                                <th scope="row">'.__('Interval 3: (time between the second warning mail to the moment the system assumes you are no longer alive or functioning', 'nextofkin').'</th>
                                                <td>
                                                        <select name="tz_nok_int3" size="1">';
														$tz_int = 1;
														while ($tz_int < 11) {
														echo '<option '.($options['interval3']==$tz_int ? "selected='selected'" : '').' value="'.$tz_int.'">'.$tz_int.'</option>';
														$tz_int++;
														}
														echo '</select> '.__('Weeks', 'nextofkin').'
                                                </td>
                                        </tr>										
                                        </tbody>
                                </table>
                                <p></p>
                        </fieldset>';
						
						}
						?>
		
		<div class="submit">
  <input type="submit" name="tz_nok_update" value="<?php
    _e('Update Options', 'nextofkin')
	?> &raquo;" /></div>
  </form></div><?php
  
  		if (get_option('tz_nextofkin_active_'.$current_user->user_login) == true) {
		echo '<div class="wrap"><h2>'.__('Write your informative emails here <small>(blank emails will not be sent)</small>', 'nextofkin').'</h2><form method="post">
		<h3>'.sprintf(__('Message 1: <small>(will be sent to you after %s weeks of inactivity)</small>', 'nextofkin'), $options['interval1']).'</h3><br />
		<input type="text" name="tz_nok_subj1" value="' . $optionsm['subject1'] . '" size="50"/>
		<textarea style="width: 98%; font-size: 12px;" rows="6" cols="60" name="tz_nok_msg1" >'.$optionsm['email1'].'</textarea>
		<h3>'.sprintf(__('Message 2: <small>(will be sent to you after %s more weeks of inactivity)</small>', 'nextofkin'), $options['interval2']).'</h3><br />
		<input type="text" name="tz_nok_subj2" value="' . $optionsm['subject2'] . '" size="50"/>
		<textarea style="width: 98%; font-size: 12px;" rows="6" cols="60" name="tz_nok_msg2" >'.$optionsm['email2'].'</textarea>
		<h3>'.__('Message 3: <small>(will be sent to the email address(es) you chose to receive your details, at the same time as message 2)</small>', 'nextofkin').'</h3><br />
		<input type="text" name="tz_nok_subj3" value="' . $optionsm['subject3'] . '" size="50"/>
		<textarea style="width: 98%; font-size: 12px;" rows="6" cols="60" name="tz_nok_msg3" >'.$optionsm['email3'].'</textarea>
		<h3>'.sprintf(__('Message 4: <small>(will be sent to your chosen email address(es) after %s more weeks of inactivity)</small>', 'nextofkin'), $options['interval3']).'</h3><br />
		<input type="text" name="tz_nok_subj4" value="' . $optionsm['subject4'] . '" size="50"/>
		<textarea style="width: 98%; font-size: 12px;" rows="6" cols="60" name="tz_nok_msg4" >'.$optionsm['email4'].'</textarea>
		<br />
		<div class="submit">
  <input type="submit" name="tz_nok_update_mails" value="'.__('Update Messages', 'nextofkin').' &raquo;" /></div>
  </form></div>
  
  <div class="wrap">
  <form method="post">
  <div class="submit">
  <p>'.__('This button will reset last login timestamps and sent mail status. Use it in case you got the first warning email.', 'nextofkin').'</p>
  <input type="submit" name="tz_nok_reset" value="'.__('Reset', 'nextofkin').' &raquo;" /></div>
  </form></div>
  ';
		}
 } //end function of menu page
 
 add_action('admin_menu', 'tz_nextofkin_menu');
 add_action('activate_nextofkin/nextofkin.php', 'tz_nextofkin_activate');
 add_action('init', 'tz_nextofkin_init');

?>