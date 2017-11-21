<?php 

/*  FV Wordpress Flowplayer - HTML5 video player with Flash fallback  
    Copyright (C) 2013  Foliovision
		
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


/*
 *  Video Checker support email
 */

add_action('wp_ajax_fv_wp_flowplayer_support_mail', 'fv_wp_flowplayer_support_mail');

function fv_wp_flowplayer_support_mail() {
  if( isset( $_POST['notice'] ) && stripos( $_SERVER['HTTP_REFERER'], home_url() ) === 0 ) {

  	$current_user = wp_get_current_user();    

  	$content = '<p>User: '.$current_user->display_name." (".$current_user->user_email.")</p>\n";  	
  	$content .= '<p>User Agent: '.$_SERVER['HTTP_USER_AGENT']."</p>\n";  	
  	$content .= '<p>Referer: '.$_SERVER['HTTP_REFERER']."</p>\n";
  	$content .= "<p>Comment:</p>\n".wpautop( stripslashes($_POST['comment']) );  	  	
  	$notice = str_replace( '<span class="value"', ': <span class="value"', stripslashes($_POST['notice']) );
  	$notice .= str_replace( '<span class="value"', ': <span class="value"', stripslashes($_POST['details']) );  	
  	
  	$content .= "<p>Video analysis:</p>\n".$notice;  	  	
    
    global $fv_wp_flowplayer_support_mail_from, $fv_wp_flowplayer_support_mail_from_name; 
    
    //$headers = "Reply-To: \"$current_user->display_name\" <$current_user->user_email>\r\n";
    $fv_wp_flowplayer_support_mail_from_name = $current_user->display_name;
    $fv_wp_flowplayer_support_mail_from = $current_user->user_email;
  	
  	add_filter( 'wp_mail_content_type', create_function('', "return 'text/html';") );
  	
  	//add_action('phpmailer_init', 'fv_wp_flowplayer_support_mail_phpmailer_init' );
  	wp_mail( 'fvplayer@foliovision.com', 'FV Flowplayer Quick Support Submission', $content );
  	
  	die('1');
  }
}

function fv_wp_flowplayer_support_mail_phpmailer_init( $phpmailer ) {
	global $fv_wp_flowplayer_support_mail_from, $fv_wp_flowplayer_support_mail_from_name; 
	
	if( $fv_wp_flowplayer_support_mail_from_name ) {
		$phpmailer->FromName = trim( $fv_filled_in_phpmailer_init_from_name );
	}
	if( $fv_wp_flowplayer_support_mail_from ) {
		if( strcmp( trim($phpmailer->From), trim($fv_wp_flowplayer_support_mail_from) ) != 0 && !trim($phpmailer->Sender) ) {
			$phpmailer->Sender = trim($phpmailer->From);	
		}
		$phpmailer->From = trim( $fv_wp_flowplayer_support_mail_from );
	}	

}




/*
 *  Activating Extensions
 */
add_action('wp_ajax_fv_wp_flowplayer_activate_extension', 'fv_wp_flowplayer_activate_extension');

function fv_wp_flowplayer_activate_extension() {
  check_ajax_referer( 'fv_wp_flowplayer_activate_extension', 'nonce' );
  if( !isset( $_POST['plugin'] ) ) {
    die();
  }
  
  $activate = activate_plugin( $_POST['plugin'] );
  if ( is_wp_error( $activate ) ) {
    echo "<FVFLOWPLAYER>".json_encode( array( 'message' => $activate->get_error_message(), 'error' => $activate->get_error_message() ) )."</FVFLOWPLAYER>";
    die();
  }    

  echo "<FVFLOWPLAYER>".json_encode( array( 'message' => 'Success!', 'plugin' => $_POST['plugin'] ) )."</FVFLOWPLAYER>";
  die();
}




/*
 *  Template Check
 */
add_action('wp_ajax_fv_wp_flowplayer_check_template', 'fv_wp_flowplayer_check_template');

function fv_wp_flowplayer_check_template() {
	$ok = array();
	$errors = array();
	
  if( stripos( $_SERVER['HTTP_REFERER'], home_url() ) === 0 ) {    
  	$response = wp_remote_get( home_url().'?fv_wp_flowplayer_check_template=yes' );
  	if( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			$output = array( 'error' => $error_message );
		} else {
      
      $active_plugins = get_option( 'active_plugins' );
			foreach( $active_plugins AS $plugin ) {
				if( stripos( $plugin, 'wp-minify' ) !== false ) {
					$errors[] = "You are using <strong>WP Minify</strong>, so the script checks would not be accurate. Please check your videos manually.";
					$output = array( 'errors' => $errors, 'ok' => $ok/*, 'html' => $response['body'] */);
					echo '<FVFLOWPLAYER>'.json_encode($output).'</FVFLOWPLAYER>';
					die();
				}
			}
			
			if( function_exists( 'w3_instance' ) && $minify = w3_instance('W3_Plugin_Minify') ) {			
				if( $minify->_config->get_boolean('minify.js.enable') ) {
					$errors[] = "You are using <strong>W3 Total Cache</strong> with JS Minify enabled. The template check might not be accurate. Please check your videos manually.";
          $output = array( 'errors' => $errors, 'ok' => $ok/*, 'html' => $response['body'] */);
					echo '<FVFLOWPLAYER>'.json_encode($output).'</FVFLOWPLAYER>';
				}
			}
      
			if( stripos( $response['body'], '/html5.js') === FALSE && stripos( $response['body'], '/html5shiv.js') === FALSE ) {
        $errors[] = 'html5.js not found in your template! Videos might not play in old browsers, like Internet Explorer 6-8. Please follow our instructions <a href="https://foliovision.com/player/installation#html5js">here</a>.';
			}      
			
      $ok[] = __('Template checker has changed. Just open any of your videos on your site and see if you get a red warning message about JavaScript not working.', 'fv-wordpress-flowplayer');
      
			$response['body'] = preg_replace( '$<!--[\s\S]+?-->$', '', $response['body'] );	//	handle HTML comments
			
			//	check Flowplayer scripts
			preg_match_all( '!<script[^>]*?src=[\'"]([^\'"]*?flowplayer[0-9.-]*?(?:\.min)?\.js[^\'"]*?)[\'"][^>]*?>\s*?</script>!', $response['body'], $flowplayer_scripts );
			if( count($flowplayer_scripts[1]) > 0 ) {
				if( count($flowplayer_scripts[1]) > 1 ) {
					$errors[] = "It appears there are <strong>multiple</strong> Flowplayer scripts on your site, your videos might not be playing, please check. There might be some other plugin adding the script.";
				}
				foreach( $flowplayer_scripts[1] AS $flowplayer_script ) {
					$check = fv_wp_flowplayer_check_script_version( $flowplayer_script );
					if( $check == - 1 ) {
						$errors[] = "Flowplayer script <code>$flowplayer_script</code> is old version and won't play. You need to get rid of this script.";
					} else if( $check == 1 ) {
            $ok[] = __('FV Flowplayer script found: ', 'fv-wordpress-flowplayer') . "<code>$flowplayer_script</code>!";
						$fv_flowplayer_pos = strpos( $response['body'], $flowplayer_script );
					}
				}
			} else if( count($flowplayer_scripts[1]) < 1 ) {
				$errors[] = "It appears there are <strong>no</strong> Flowplayer scripts on your site, your videos might not be playing, please check. Check your template's header.php file if it contains wp_head() function call and footer.php should contain wp_footer()!";			
			}
			

			//	check jQuery scripts						
			preg_match_all( '!<script[^>]*?src=[\'"]([^\'"]*?/jquery[0-9.-]*?(?:\.min)?\.js[^\'"]*?)[\'"][^>]*?>\s*?</script>!', $response['body'], $jquery_scripts );
			if( count($jquery_scripts[1]) > 0 ) {   
				foreach( $jquery_scripts[1] AS $jkey => $jquery_script ) {
          $ok[] = __('jQuery library found: ', 'fv-wordpress-flowplayer') . "<code>$jquery_script</code>!";
					$jquery_pos = strpos( $response['body'], $jquery_script );
				}
      
				if( count($jquery_scripts[1]) > 1 ) {
					$errors[] = "It appears there are <strong>multiple</strong> jQuery libraries on your site, your videos might not be playing or may play with defects, please check.\n";
				}
			} else if( count($jquery_scripts[1]) < 1 ) {
				$errors[] = "It appears there are <strong>no</strong> jQuery library on your site, your videos might not be playing, please check.\n";			
			}
			
						
			if( $fv_flowplayer_pos > 0 && $jquery_pos > 0 && $jquery_pos > $fv_flowplayer_pos && count($jquery_scripts[1]) < 1 ) {
				$errors[] = "It appears your Flowplayer JavaScript library is loading before jQuery. Your videos probably won't work. Please make sure your jQuery library is loading using the standard Wordpress function - wp_enqueue_scripts(), or move it above wp_head() in your header.php template.";
			}
      
			$output = array( 'errors' => $errors, 'ok' => $ok/*, 'html' => $response['body'] */);
		}
		echo '<FVFLOWPLAYER>'.json_encode($output).'</FVFLOWPLAYER>';
		die();
  }
  
  die('-1');
}

//	enter script URL, return false if it's not version 5
function fv_wp_flowplayer_check_script_version( $url ) {
	$url_mod = preg_replace( '!\?.+!', '', $url );
	if( preg_match( '!flowplayer-([\d\.]+)!', $url_mod, $version ) && $version[1] ) {
		if( strpos( $version[1], '5' ) !== 0 ) {
			return -1;			
		}
	}
	
	global $fv_wp_flowplayer_ver;
	if( strpos( $url, '/fv-wordpress-flowplayer/flowplayer/fv-flowplayer.min.js?ver='.$fv_wp_flowplayer_ver ) !== false ) {
		return 1;
	}
	return 0;
}

function fv_wp_flowplayer_check_jquery_version( $url, &$array, $key ) {	
	$url_mod = preg_replace( '!\?.+!', '', $url );
	if( preg_match( '!(\d+.[\d\.]+)!', $url_mod, $version ) && $version[1] ) {
		if( version_compare($version[1], '1.7.1') == -1 ) {
			return -1;
		} else {
			return 1;
		}
	}
	
	//	if jQuery is in the Wordpress install, we know that the ?ver= says what version it is
	if( strpos( $url, site_url().'/wp-includes/js/jquery/jquery.js' ) !== false ) {
		if( preg_match( '!(\d+.[\d\.]+)!', $url, $version ) && $version[1] ) {
			if( version_compare($version[1], '1.7.1') == -1 ) {
				return -1;
			} else {
				return 1;
			}
		}
	}

	return 0;
}




/*
 *  Check video files
 */
add_action('wp_ajax_fv_wp_flowplayer_check_files', 'fv_wp_flowplayer_check_files');

function fv_wp_flowplayer_check_files() {
  if( stripos( $_SERVER['HTTP_REFERER'], home_url() ) === 0 ) {    
  	global $wpdb;
  	define('VIDEO_DIR', '/videos/');
    
    $bNotDone = false;
    $tStart = microtime(true);
    $tMax = ( @ini_get('max_execution_time') ) ? @ini_get('max_execution_time') - 5 : 25;
  	
  	$videos1 = $wpdb->get_results( "SELECT ID, post_content FROM $wpdb->posts WHERE post_type != 'revision' AND post_status != 'trash' AND post_content LIKE '%[flowplayer %'" );
  	$videos2 = $wpdb->get_results( "SELECT ID, post_content FROM $wpdb->posts WHERE post_type != 'revision' AND post_status != 'trash' AND post_content LIKE '%[fvplayer %'" );  
  	
  	$videos = array_merge( $videos1, $videos2 );
  		
  	$source_servers = array();
  	
  	$shortcodes_count = 0;
  	$src_count = 0;
  	if( count($videos) ) {
			foreach( $videos AS $post ) {
			
				$shortcodes_count += preg_match_all( '!\[(?:flowplayer|fvplayer)[^\]]+\]!', $post->post_content, $post_videos );
				if( count($post_videos[0]) ) {
					foreach( $post_videos[0] AS $post_video ) {
						$post_video = preg_replace( '!popup=\'.*\'!', '', $post_video );
						$src_count += preg_match_all( '!(?:src|src1|src2|src3|mp4|webm|ogv)=[\'"](.*?(?:mp4|m4v))[\'"]!', $post_video, $sources1 );
						$src_count += preg_match_all( '!(?:src|src1|src2|src3|mp4|webm|ogv)=([^\'"].*?(?:mp4|m4v|flv))[\s\]]!', $post_video, $sources2 );
						$sources = array_merge( $sources1[1], $sources2[1] );
						if( count($sources) ) {
							foreach($sources AS $src ) {
								if( strpos( $src, '//' ) === 0 ) {
									$src = 'http:'.$src;
								} else if( strpos( $src, '/' ) === 0 ) {
									$src = home_url().$src;
								} else if( !preg_match( '!^\S+://!', $src ) )  {
									$src = home_url().VIDEO_DIR.$src;
								} 
								
								$server = preg_replace( '!(.*?//.*?)/.+!', '$1', $src );
																
								$source_servers[$server][] = array( 'src' => $src, 'post_id' => $post->ID );
							}
						}
					}
				}
				
			}
  	}
  	
  	$ok = array();
  	$errors = array();

  	$count = 0;
  	foreach( $source_servers AS $server => $videos ) {
      
      $tCurrent = microtime(true);
      if( $tCurrent - $tStart > $tMax ) {
        $bNotDone = true;
        break;
      }
      
      if( stripos( $videos[0]['src'], '.mp4' ) === FALSE /*&& stripos( $videos[0]['src'], '.m4v' ) === FALSE*/ ) {
        continue;
      }
      
      global $FV_Player_Checker;
 
			if( stripos( trim($videos[0]['src']), 'rtmp://' ) === false ) {
        list( $header, $message_out ) = $FV_Player_Checker->http_request( trim($videos[0]['src']), array( 'quick_check' => 10, 'size' => 65536 ) );
        if( $header ) {        
          $headers = WP_Http::processHeaders( $header );          
          list( $new_errors, $mime_type, $fatal ) = $FV_Player_Checker->check_headers( $headers, trim($videos[0]['src']), rand(0,999), array( 'talk_bad_mime' => 'Server <code>'.$server.'</code> uses incorrect mime type for MP4 ', 'wrap' => false ) );
          if( $fatal ) {            
            continue;
          }
          if( $new_errors ) {
            $sPostsLinks = false;
            foreach( $videos AS $video ) {
              $sPostsLinks .= '<a href="'.home_url().'?p='.$video['post_id'].'">'.$video['post_id'].'</a> ';	
            }
            $errors[] = implode( " ",$new_errors ).'(<a href="#" onclick="jQuery(\'#fv-flowplayer-warning-'.$count.'\').toggle(); return false">click to see a list of posts</a>) <div id="fv-flowplayer-warning-'.$count.'" style="display: none; ">'.$sPostsLinks.'</div>';
            $count++;
            continue;           
          } else {
            $ok[] = 'Server <code>'.$server.'</code> appears to serve correct mime type <code>'.$mime_type.'</code> for MP4 videos.';
          }
        }
  		}    						
  	}
    
    if( $bNotDone ) {
      $ok[] = '<strong>Not all the servers were checked as you use a lot of them, increase your PHP execution time or check your other videos by hand.</strong>';
    }
  	
  	$output = array( 'errors' => $errors, 'ok' => $ok/*, 'html' => $response['body'] */);
		echo '<FVFLOWPLAYER>'.json_encode($output).'</FVFLOWPLAYER>';
		die();
  }
  die('-1');
}




/*
 *  Apply Pro Upgrade button
 */
add_action('wp_ajax_fv_wp_flowplayer_check_license', 'fv_wp_flowplayer_check_license');

function fv_wp_flowplayer_check_license() {
  if( stripos( $_SERVER['HTTP_REFERER'], home_url() ) === 0 ) {
    if( fv_wp_flowplayer_admin_key_update() ) {
      $output = array( 'errors' => false, 'ok' => array(__('License key acquired successfully. <a href="">Reload</a>', 'fv-wordpress-flowplayer')) );
      fv_wp_flowplayer_install_extension();
    } else {
      $message = get_option('fv_wordpress_flowplayer_deferred_notices');
      if( !$message ) $message = get_option('fv_wordpress_flowplayer_persistent_notices');
      $output = array( 'errors' => array($message), 'ok' => false );            
    }
    echo '<FVFLOWPLAYER>'.json_encode($output).'</FVFLOWPLAYER>';
		die();
  }
  die('-1');
}



/*
 *  Run this when new version is installed
 */
add_action('admin_init', 'fv_player_admin_update');

function fv_player_admin_update() {
  global $fv_fp, $fv_wp_flowplayer_ver, $fv_wp_flowplayer_core_ver;
  
  $aOptions = get_option( 'fvwpflowplayer' );
  if( !isset($aOptions['version']) || version_compare( $fv_wp_flowplayer_ver, $aOptions['version'] ) ) {
    //update_option( 'fv_wordpress_flowplayer_deferred_notices', 'FV Flowplayer upgraded - please click "Check template" and "Check videos" for automated check of your site at <a href="'.site_url().'/wp-admin/options-general.php?page=fvplayer">the settings page</a> for automated checks!' );
    
    if( $aOptions['version'] == '6.0.5.20' && $aOptions['playlist_advance'] == 'true' ) { //  version 6.0.5 used reverse login for this option!
      $aOptions['playlist_advance'] = false;
      $fv_fp->conf = $aOptions;
    }
    
    $aOptions['version'] = $fv_wp_flowplayer_ver;
    update_option( 'fvwpflowplayer', $aOptions );
    
    fv_wp_flowplayer_pro_settings_update_for_lightbox();
    $fv_fp->css_writeout();
    
    fv_wp_flowplayer_delete_extensions_transients();
    delete_option('fv_flowplayer_extension_install');
  }  
}

function fv_wp_flowplayer_pro_settings_update_for_lightbox(){
  global $fv_fp;
  if(isset($fv_fp->conf['pro']) && isset($fv_fp->conf['pro']['interface']['lightbox']) && $fv_fp->conf['pro']['interface']['lightbox'] == true ){
    $fv_fp->conf['interface']['lightbox'] = true;
    $fv_fp->conf['pro']['interface']['lightbox'] = false;
    $options = get_option('fvwpflowplayer');
    unset($options['pro']['interface']['lightbox']); 
    $options['interface']['lightbox'] = true;
    update_option('fvwpflowplayer', $options);
  }
  if(isset($fv_fp->conf['pro']) && isset($fv_fp->conf['pro']['lightbox_images']) && $fv_fp->conf['pro']['lightbox_images'] == true ){
    $fv_fp->conf['lightbox_images'] = true;
    $fv_fp->conf['pro']['lightbox_images'] = false;
    $options = get_option('fvwpflowplayer');
    unset($options['pro']['lightbox_images']);
    $options['lightbox_images'] = true;
    update_option('fvwpflowplayer', $options);
  }
   
}

function fv_wp_flowplayer_delete_extensions_transients( $delete_delay = false ){
  $aTransientsLike = array('fv_flowplayer_license','fv-player-pro_license','fv-player-vast_license','fv-player-pro_fp-private-updates','fv-player-vast_fp-private-updates');
  
  global $wpdb;
  $aWhere = array();
  foreach( $aTransientsLike AS $sKey ) {
    $aWhere[] = 'option_name LIKE "%'.$sKey.'%"';
  }
  $sWhere = implode(" OR ", $aWhere);
  $aOptions = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '%_transient_fv%' AND ( ".$sWhere." )" );
  
  foreach( $aOptions AS $sKey ) {
    if( !$delete_delay ){
      delete_transient( str_replace('_transient_','',$sKey) );
    } else {
      fv_wp_flowplayer_change_transient_expiration( str_replace('_transient_','',$sKey), $delete_delay );
    }
  }

  $aUpdates = get_site_transient('update_plugins');
  set_site_transient('update_plugins', $aUpdates );
  
}



add_action('admin_init', 'fv_player_lchecks');

function fv_player_lchecks() {
	if( isset($_GET['fv-licensing']) && $_GET['fv-licensing'] == "check" ){
    delete_option("fv_wordpress_flowplayer_persistent_notices");
    
    //license will expire in 5 seconds in the function:
    fv_wp_flowplayer_admin_key_update();
	}

  global $fv_fp, $fv_wp_flowplayer_core_ver;
  if( preg_match( '!^\$\d+!', $fv_fp->conf['key'] ) ) {    
    $version = get_option( 'fvwpflowplayer_core_ver' );
    if( version_compare( $fv_wp_flowplayer_core_ver, $version ) == 1 ) {
      fv_wp_flowplayer_admin_key_update();
      fv_wp_flowplayer_delete_extensions_transients();
    }      
  }
  
  if( flowplayer::is_licensed() ) {
    if ( false === ( $aCheck = get_transient( 'fv_flowplayer_license' ) ) ) {
      $aCheck = fv_wp_flowplayer_license_check( array('action' => 'check') );  
      if( $aCheck ) {
        set_transient( 'fv_flowplayer_license', $aCheck, 60*60*24 );
      } else {
        set_transient( 'fv_flowplayer_license', json_decode(json_encode( array('error' => 'Error checking license') ), FALSE), 60*60*24 );
      }
    }

    $aCheck = get_transient( 'fv_flowplayer_license' );
    $aInstalled = get_option('fv_flowplayer_extension_install');
    if( isset($aCheck->valid) && $aCheck->valid){
    
      if( !isset($aInstalled['fv_player_pro']) || ( isset($_REQUEST['nonce_fv_player_pro_install']) && wp_verify_nonce( $_REQUEST['nonce_fv_player_pro_install'], 'fv_player_pro_install') ) ) {
        fv_wp_flowplayer_install_extension('fv_player_pro');
      }
      delete_option('fv_wordpress_flowplayer_persistent_notices');
    }

    if( isset($aCheck->expired) && $aCheck->expired && stripos( implode(get_option('active_plugins')), 'fv-player-pro' ) !== false ) {
      add_filter( 'site_transient_update_plugins', 'fv_player_remove_update' );
    }
  }
}

function fv_player_remove_update( $objUpdates ) {
  if( !$objUpdates || !isset($objUpdates->response) || count($objUpdates->response) == 0 ) return $objUpdates;

  foreach( $objUpdates->response AS $key => $objUpdate ) {
    if( stripos($key,'fv-wordpress-flowplayer') === 0 ) {
      unset($objUpdates->response[$key]);      
    }
  }
  
  return $objUpdates;
}

function fv_wp_flowplayer_admin_key_update() {
	global $fv_fp, $fv_wp_flowplayer_core_ver;
	
	$data = fv_wp_flowplayer_license_check( array('action' => 'key_update') );
	if( isset($data->domain) ) {  //  todo: test
		if( $data->domain && $data->key && stripos( home_url(), $data->domain ) !== false ) {
			$fv_fp->conf['key'] = $data->key;
			update_option( 'fvwpflowplayer', $fv_fp->conf );
			update_option( 'fvwpflowplayer_core_ver', $fv_wp_flowplayer_core_ver );
      
      fv_wp_flowplayer_change_transient_expiration("fv_flowplayer_license",5);
      fv_wp_flowplayer_delete_extensions_transients(5);
			return true;
		}                            
	} else if( isset($data->expired) && $data->expired && isset($data->message) ){
    update_option( 'fv_wordpress_flowplayer_persistent_notices', $data->message );
    update_option( 'fvwpflowplayer_core_ver', $fv_wp_flowplayer_core_ver ); 
    return false;
	} else {
    update_option( 'fv_wordpress_flowplayer_deferred_notices', 'FV Flowplayer License upgrade failed - please check if you are running the plugin on your licensed domain.' );
    update_option( 'fvwpflowplayer_core_ver', $fv_wp_flowplayer_core_ver ); 
		return false;
	}
}

function fv_wp_flowplayer_license_check( $aArgs ) {
	global $fv_wp_flowplayer_ver, $fv_wp_flowplayer_core_ver;  
  
	$args = array(
		'body' => array( 'plugin' => 'fv-wordpress-flowplayer', 'version' => $fv_wp_flowplayer_ver, 'core_ver' => $fv_wp_flowplayer_core_ver, 'type' => home_url(), 'action' => $aArgs['action'], 'admin-url' => admin_url() ),
		'timeout' => 10,
		'user-agent' => 'fv-wordpress-flowplayer-'.$fv_wp_flowplayer_ver.' ('.$fv_wp_flowplayer_core_ver.')'
	);
	$resp = wp_remote_post( 'https://foliovision.com/?fv_remote=true', $args );

  if( !is_wp_error($resp) && isset($resp['body']) && $resp['body'] && $data = json_decode( preg_replace( '~[\s\S]*?<FVFLOWPLAYER>(.*?)</FVFLOWPLAYER>[\s\S]*?~', '$1', $resp['body'] ) ) ) {
    return $data;
  
  } else if( is_wp_error($resp) && stripos($resp->get_error_message(),'SSL' ) !== false ) {
    $args = array( 'sslverify' => false );
    $resp = wp_remote_post( 'https://foliovision.com/?fv_remote=true', $args );
  
    if( !is_wp_error($resp) && isset($resp['body']) && $resp['body'] && $data = json_decode( preg_replace( '~[\s\S]*?<FVFLOWPLAYER>(.*?)</FVFLOWPLAYER>[\s\S]*?~', '$1', $resp['body'] ) ) ) {    
      return $data;
    }
    
  }
  
  return false;
}

function fv_wp_flowplayer_change_transient_expiration( $transient_name, $time ){
  $transient_val = get_transient($transient_name);
  if( $transient_val ){
    set_transient($transient_name,$transient_val,$time);
    return true;
  }
  return false;
}




add_action('wp_ajax_flowplayer_conversion_script', 'flowplayer_conversion_script');

function flowplayer_conversion_script() {
  global $wpdb;
  
  $posts = $wpdb->get_results("SELECT ID, post_content FROM {$wpdb->posts} WHERE post_type != 'revision'");
  
  $old_shorttag = '[flowplayer';
  $new_shorttag = '[fvplayer';
  $counter = 0;
  
  echo '<ol>';
  foreach($posts as $fv_post) {
    if ( stripos( $fv_post->post_content, $old_shorttag ) !== false ) {
      $update_post = array();
      $update_post['ID'] = $fv_post->ID;
      $update_post['post_content'] = str_replace( $old_shorttag, $new_shorttag, $fv_post->post_content ); 
      wp_update_post( $update_post );      
      echo '<li><a href="' . get_permalink($fv_post->ID) . '">' . get_the_title($fv_post->ID) . '</a> updated</li>';
      $counter++;
    } 
  }
  echo '</ol>';
  
  echo '<strong>Conversion was succesful. Total number of converted posts: ' . $counter . '</strong>';
  
  delete_option('fvwpflowplayer_conversion');
  
  die();
}




add_action('admin_notices', 'fv_wp_flowplayer_admin_notice');

function fv_wp_flowplayer_admin_notice() {
	if( $notices = get_option('fv_wordpress_flowplayer_deferred_notices') ) {
  	echo '<div class="updated inline">
       			<p>'.$notices.'</p>
    			</div>';
    delete_option('fv_wordpress_flowplayer_deferred_notices');
  }

  $conversion = false; //(bool)get_option('fvwpflowplayer_conversion');
  if ($conversion) {
    echo '<div class="updated" id="fvwpflowplayer_conversion_notice"><p>'; 
    printf(__('FV Player has found old shortcodes in the content of your posts. <a href="%1$s">Run the conversion script.</a>'), get_admin_url() . 'options-general.php?page=fvplayer');
    echo "</p></div>";
  }
  
  if( isset($_GET['fv-licensing']) && $_GET['fv-licensing'] == "check" ){
    echo '<div class="updated inline">
            <p>Thank you for purchase. Your license will be renewed in couple of minutes.<br/>
            Please make sure you upgrade <strong>FV Player Pro</strong> and <strong>FV Player VAST</strong> if you are using it.</p>
          </div>';
  }
  
  global $FV_Player_Pro;
  if( $FV_Player_Pro && version_compare($FV_Player_Pro->version,'0.5') == -1 ) : 
  ?>
  <div class="error">
      <p><?php _e( 'FV Player: Your pro extension is installed, but it\'s not compatible with FV Player 6! Make sure you upgrade your FV Player Pro to version 0.5 or above.', 'my-text-domain' ); ?></p>
  </div>
  <?php
  endif;
  
  /*if( isset($_GET['page']) && $_GET['page'] == 'backend.php' ) {
	  $options = get_option( 'fvwpflowplayer' );
    if( $options['key'] == 'false' ) {
  		echo '<div class="updated"><p>'; 
    	printf(__('Brand new version of Flowplayer for HTML5. <a href="http://foliovision.com/wordpress/plugins/fv-wordpress-flowplayer/buy">Licenses half price</a> in May.' ) );
    	echo "</p></div>";
    }
  }*/
}







/*
 *  Check the extension info from plugin license transient and activate the plugin
 */
function fv_wp_flowplayer_install_extension( $plugin_package = 'fv_player_pro' ) {
  global $hook_suffix;

  $aInstalled = ( get_option('fv_flowplayer_extension_install' ) ) ? get_option('fv_flowplayer_extension_install' ) : array();
  $aInstalled = array_merge( $aInstalled, array( $plugin_package => false ) );
  update_option('fv_flowplayer_extension_install', $aInstalled );
  
  $aPluginInfo = get_transient( 'fv_flowplayer_license' );
  $plugin_basename = $aPluginInfo->{$plugin_package}->slug; 
  $download_url = $aPluginInfo->{$plugin_package}->url;
  
  $sPluginBasenameReal = fv_flowplayer_get_extension_path( str_replace( '_', '-', $plugin_package ) );
  if( $sPluginBasenameReal ) {
    return; //  already installed
  }
  
  $plugin_basename = $sPluginBasenameReal ? $sPluginBasenameReal : $plugin_basename;

  $url = wp_nonce_url( site_url().'/wp-admin/options-general.php?page=fvplayer', 'fv_player_pro_install', 'nonce_fv_player_pro_install' );

  set_current_screen();
  
  ob_start();
  if ( false === ( $creds = request_filesystem_credentials( $url, '', false, false, false ) ) ) {
    $form = ob_get_clean();
    include( ABSPATH . 'wp-admin/admin-header.php' );
    echo fv_wp_flowplayer_install_extension_talk($form);
    include( ABSPATH . 'wp-admin/admin-footer.php' );
    die;
  }	

  if ( ! WP_Filesystem( $creds ) ) {
    ob_start();
    request_filesystem_credentials( $url, $method, true, false, false );
    $form = ob_get_clean();
    include( ABSPATH . 'wp-admin/admin-header.php' );
    echo fv_wp_flowplayer_install_extension_talk($form);
    include( ABSPATH . 'wp-admin/admin-footer.php' );
    die;
  }

  require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
     
  $sTaskDone = __('FV Flowplayer Pro extension installed - check the new ', 'fv-wordpress-flowplayer') . '<a href="'.site_url().'/wp-admin/options-general.php?page=fvplayer#fv_player_pro">' . __('Pro features', 'fv-wordpress-flowplayer') . '</a>!';
  if( !$sPluginBasenameReal || is_wp_error(validate_plugin($plugin_basename)) ) {
    echo '<div style="display: none;">';
    $objInstaller = new Plugin_Upgrader();
    $objInstaller->install( $download_url );
    echo '</div>';
    wp_cache_flush();
    
    if ( is_wp_error( $objInstaller->skin->result ) ) {
      
      update_option( 'fv_wordpress_flowplayer_deferred_notices', __('FV Flowplayer Pro extension install failed - ', 'fv-wordpress-flowplayer') . $objInstaller->skin->result->get_error_message() );
      $bResult = false;
    } else {    
      if ( $objInstaller->plugin_info() ) {
        $plugin_basename = $objInstaller->plugin_info();
        
      }
      
      $activate = activate_plugin( $plugin_basename );
      if ( is_wp_error( $activate ) ) {
        update_option( 'fv_wordpress_flowplayer_deferred_notices', __('FV Flowplayer Pro extension install failed - ', 'fv-wordpress-flowplayer') . $activate->get_error_message());
        $bResult = false;
      }
    }
    
  } else if( $sPluginBasenameReal ) {
    $sTaskDone = __('FV Flowplayer Pro extension upgraded successfully!', 'fv-wordpress-flowplayer');

    echo '<div style="display: none;">';
    $objInstaller = new Plugin_Upgrader();
    $objInstaller->upgrade( $sPluginBasenameReal );    
    echo '</div></div>';  //  explanation: extra closing tag just to be safe (in case of "The plugin is at the latest version.")
    wp_cache_flush();
    
    if ( is_wp_error( $objInstaller->skin->result ) ) {
      update_option( 'fv_wordpress_flowplayer_deferred_notices', 'FV Flowplayer Pro extension upgrade failed - '.$objInstaller->skin->result->get_error_message() );
      $bResult = false;
    } else {    
      if ( $objInstaller->plugin_info() ) {
        $plugin_basename = $objInstaller->plugin_info();
        
      }
      
      $activate = activate_plugin( $plugin_basename );
      if ( is_wp_error( $activate ) ) {
        update_option( 'fv_wordpress_flowplayer_deferred_notices', 'FV Flowplayer Pro extension upgrade failed - '.$activate->get_error_message() );
        $bResult = false;
      }
    }    
    
  }

  if( !isset($bResult) ) {
    if( !isset($_GET['page']) || strcmp($_GET['page'],'fvplayer') != 0 ) {
      update_option( 'fv_wordpress_flowplayer_deferred_notices', $sTaskDone );
    }
    $bResult = true;
  }
  
  $aInstalled = ( get_option('fv_flowplayer_extension_install' ) ) ? get_option('fv_flowplayer_extension_install' ) : array();
  $aInstalled = array_merge( $aInstalled, array( $plugin_package => $bResult ) );
  update_option('fv_flowplayer_extension_install', $aInstalled );

  return $bResult;
}


function fv_wp_flowplayer_install_extension_talk( $content ) {
  $content = preg_replace( '~<h3.*?</h3>~', '<h3>FV Player Pro auto-installation</h3><p>As a FV Flowplayer license holder, we would like to automatically install our Pro extension for you.</p>', $content );
  $content = preg_replace( '~(<input[^>]*?type="submit"[^>]*?>)~', '$1 <a href="'.site_url().'/wp-admin/options-general.php?page=fvplayer'.'">Skip the Pro addon install</a>', $content );
  return $content;
}




//search for plugin path with {slug}.php
function fv_flowplayer_get_extension_path( $slug ){
  $aPluginSlugs = get_transient('plugin_slugs');
  $aPluginSlugs = is_array($aPluginSlugs) ? $aPluginSlugs : array( 'fv-player-pro/fv-player-pro.php');
  $aActivePlugins = get_option('active_plugins');
  $aInactivePlugins = array_diff($aPluginSlugs,$aActivePlugins);
  
  if( !$aPluginSlugs )
    return false;

  foreach( $aActivePlugins as $item ){
    if( stripos($item,$slug.'.php') !== false )
      return $item;
  }
  
  $sPluginFolder = plugin_dir_path( dirname( dirname(__FILE__) ) );
  foreach( $aInactivePlugins as $item ){
    if( stripos($item,$slug.'.php') !== false && file_exists($sPluginFolder.$item) )
      return $item;
  }  
  
  return false;
}




function fv_player_disable_object_cache($value=null){
    global $_wp_using_ext_object_cache, $fv_player_wp_using_ext_object_cache_prev;
    $fv_player_wp_using_ext_object_cache_prev = $_wp_using_ext_object_cache;
    $_wp_using_ext_object_cache = false;
    return $value;
}

function fv_player_enable_object_cache($value=null){
    global $_wp_using_ext_object_cache, $fv_player_wp_using_ext_object_cache_prev;
    $_wp_using_ext_object_cache = $fv_player_wp_using_ext_object_cache_prev;
    return $value;
}

add_filter( 'pre_set_transient_fv_flowplayer_license', 'fv_player_disable_object_cache' );
add_filter( 'pre_transient_fv_flowplayer_license', 'fv_player_disable_object_cache' );
add_action( 'delete_transient_fv_flowplayer_license', 'fv_player_disable_object_cache' );
add_action( 'set_transient_fv_flowplayer_license', 'fv_player_disable_object_cache' );
add_filter( 'transient_fv_flowplayer_license', 'fv_player_enable_object_cache' );
add_action( 'deleted_transient_fv_flowplayer_license', 'fv_player_disable_object_cache' );




function flowplayer_deactivate() {
  if( flowplayer::is_licensed() ) {  
    delete_transient( 'fv_flowplayer_license' );
  }
  delete_option( 'fv_flowplayer_extension_install' );
  wp_clear_scheduled_hook('fv_flowplayer_checker_event');
}




add_action( 'admin_notices', 'fv_player_admin_notice_expired_license' );
add_action( 'fv_player_settings_pre', 'fv_player_admin_notice_expired_license' );

function fv_player_admin_notice_expired_license() {
  global $FV_Player_Pro;
  $screen = get_current_screen();
  if( ( $screen && $screen->id == 'plugins' || did_action('fv_player_settings_pre') ) && isset($FV_Player_Pro) && isset($FV_Player_Pro->version) && version_compare( str_replace( '.beta', '', $FV_Player_Pro->version), '0.8.17') == -1 ) {
    $aCheck = get_transient( 'fv-player-pro_license' );
    if( !empty($aCheck->expired) || !empty($aCheck->error) ) { ?>
      <div class="updated">
        <?php echo $aCheck->message; ?>
        <?php if( !empty($aCheck->changelog) ) echo $aCheck->changelog; ?>
      </div>
    <?php }
  }
}


add_action( 'admin_footer', 'fv_player_block_update', 999 );

function fv_player_block_update( $arg ) {
  global $pagenow;
  if( isset($pagenow) && $pagenow == 'plugins.php' ) {
    $plugin_path = str_replace( trailingslashit(plugins_url()), '', plugins_url('',dirname(__FILE__)) ).'/flowplayer.php';
    $aUpdates = get_site_transient('update_plugins');
    if( !$aUpdates || empty($aUpdates->response) || empty($aUpdates->response[$plugin_path]) ) return;
    
    $sMessage = 'You are about to upgrade to FV Player 7 which uses the new core video player with some visual changes.\n\n';
    $aCheckProLicense = get_transient( 'fv_flowplayer_license' );
    $aCheckPlayerLicense = get_transient( 'fv-player-pro_license' );
    if( !empty($aCheckProLicense->expired) || !empty($aCheckProLicense->error) ) {
      $sMessage .= 'Since your license is expired, so you will loose your custom logo and Pro features might not work.\n\n';
    } if( !empty($aCheckPlayerLicense->expired) || !empty($aCheckPlayerLicense->error) ) {
      $sMessage .= 'Since your license is expired, so you will loose your custom logo.\n\nAre you sure you want to upgrade?\n\n';
    }
    
    $sMessage .= 'Are you sure you want to upgrade?';
    
    if( stripos($aUpdates->response[$plugin_path]->new_version,'7.') === 0 ) {
      ?>
      <script>
      ( function($) {        
        $('[data-plugin=<?php echo str_replace( array('/','.'), array('\\\/','\\\.'), $plugin_path ); ?>]').find('.update-link').click( function(e) {
          e.preventDefault();
          e.stopPropagation();
          return confirm("<?php echo $sMessage; ?>");
        });
      })(jQuery);
      </script>
      <?php
    }
  }
}
