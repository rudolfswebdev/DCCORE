<?php
/*  FV Wordpress Flowplayer - HTML5 video player with Flash fallback    
    Copyright (C) 2015  Foliovision

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

/**
 * Extension of original flowplayer class intended for frontend.
 */
class flowplayer_frontend extends flowplayer
{

  var $ajax_count = 0;
  
  var $autobuffer_count = 0;  
  
  var $expire_time = 0;
  
  var $aAds = array();
  
  var $aPlayers = array();
  
  var $aPlaylists = array();
  
  var $aPopups = array();
  
  var $aCurArgs = false;
  
  var $sHTMLAfter = false;
  
  var $count_tabs = 0;
  

  /**
   * Builds the HTML and JS code of single flowplayer instance on a page/post.
   * @param string $media URL or filename (in case it is in the /videos/ directory) of video file to be played.
   * @param array $args Array of arguments (name => value).
   * @return Returns array with 2 elements - 'html' => html code displayed anywhere on page/post, 'script' => javascript code displayed before </body> tag
   */
  function build_min_player($media,$args = array()) {

    $this->hash = md5($media.$this->_salt()); //  unique player id
    $this->aCurArgs = apply_filters( 'fv_flowplayer_args_pre', $args );
    $this->sHTMLAfter = false;
    $player_type = 'video';
    $rtmp = false;
    $youtube = false;
    $vimeo = false;
    $wistia = false;
    $scripts_after = '';
    
    $attributes = array();
    
    // returned array with new player's html and javascript content
    if( !isset($GLOBALS['fv_fp_scripts']) ) {
      $GLOBALS['fv_fp_scripts'] = array();
    }
    $this->ret = array('html' => '', 'script' => $GLOBALS['fv_fp_scripts'] );  //  note: we need the white space here, it fails to add into the string on some hosts without it (???)
    
      
    
    /*
     *  Set common variables
     */
    $width = $this->_get_option('width');
    $height = $this->_get_option('height');
    if (isset($this->aCurArgs['width'])&&!empty($this->aCurArgs['width'])) $width = trim($this->aCurArgs['width']);
    if (isset($this->aCurArgs['height'])&&!empty($this->aCurArgs['height'])) $height = trim($this->aCurArgs['height']);    
            
    $src1 = ( isset($this->aCurArgs['src1']) && !empty($this->aCurArgs['src1']) ) ? trim($this->aCurArgs['src1']) : false;
    $src2 = ( isset($this->aCurArgs['src2']) && !empty($this->aCurArgs['src2']) ) ? trim($this->aCurArgs['src2']) : false;  
    
    $splash_img = $this->get_splash();

    foreach( array( $media, $src1, $src2 ) AS $media_item ) {
      if( stripos( $media_item, 'rtmp://' ) === 0 ) {
        $rtmp = $media_item;
      }
    }

    if( ( !empty($this->aCurArgs['rtmp']) || $this->_get_option('rtmp') ) && !empty($this->aCurArgs['rtmp_path']) ) {
      $rtmp = trim( $this->aCurArgs['rtmp_path'] );
    }
  
    list( $media, $src1, $src2 ) = apply_filters( 'fv_flowplayer_media_pre', array( $media, $src1, $src2 ), $this );
    
    
    /*
     *  Which player should be used
     */
    foreach( array( $media, $src1, $src2 ) AS $media_item ) {
      if( !$this->_get_option('audio') ) {
        if( preg_match( '~\.(mp3|wav|ogg)([?#].*?)?$~', $media_item ) ) {
          $player_type = 'audio';
          break;
        }
      }
        
      global $post;
      if( $post ) {
        $fv_flowplayer_meta = get_post_meta( $post->ID, '_fv_flowplayer', true );
        if( $fv_flowplayer_meta && isset($fv_flowplayer_meta[sanitize_title($media_item)]['time']) ) {
          $this->expire_time = $fv_flowplayer_meta[sanitize_title($media_item)]['time'];
        }
      }
    }
    
    if( preg_match( "~(youtu\.be/|youtube\.com/(watch\?(.*&)?v=|(embed|v)/))([^\?&\"'>]+)~i", $media, $aYoutube ) ) {
      if( isset($aYoutube[5]) ) {
        $youtube = $aYoutube[5];
        $player_type = 'youtube';
      }
    } else if( preg_match( "~^[a-zA-Z0-9-_]{11}$~", $media, $aYoutube ) ) {
      if( isset($aYoutube[0]) ) {
        $youtube = $aYoutube[0];
        $player_type = 'youtube';
      }
    }

    if( preg_match( "~vimeo.com/(?:video/|moogaloop\.swf\?clip_id=)?(\d+)~i", $media, $aVimeo ) ) {
      if( isset($aVimeo[1]) ) {
        $vimeo = $aVimeo[1];
        $player_type = 'vimeo';
      }
    } else if( preg_match( "~^[0-9]{8}$~", $media, $aVimeo ) ) {
      if( isset($aVimeo[0]) ) {
        $vimeo = $aVimeo[0];
        $player_type = 'vimeo';
      }
    }
    
    //  https://account.wistia.com/medias/9km3qucr7g?embedType=async&videoFoam=true&videoWidth=1920
    if( preg_match( "~https?://\S*?\.wistia\.com/medias/([a-z0-9]+)~i", $media, $aWistia ) ) {
      $wistia = $aWistia[1];
      $player_type = 'wistia';
      
    //  http://fast.wistia.net/embed/iframe/avk9twrrbn
    } else if( preg_match( "~https?://\S*?\.wistia\.(?:com|net)/embed/(?:iframe|medias)/([a-z0-9]+)~i", $media, $aWistia ) ) {
      $wistia = $aWistia[1];
      $player_type = 'wistia';      
    }
    
    if( !isset($this->aCurArgs['liststyle']) || empty($this->aCurArgs['liststyle']) ){
      $this->aCurArgs['liststyle'] = $this->_get_option('liststyle');     
    }
    
        
    $aPlaylistItems = array();  //  todo: remove
    $aSplashScreens = array();
    $aCaptions = array();
    if( !$this->_get_option('old_code') || apply_filters('fv_flowplayer_playlist_items',array(),$this) || isset($this->aCurArgs['playlist']) && strlen(trim($this->aCurArgs['playlist'])) > 0 ) {     

      list( $playlist_items_external_html, $aPlaylistItems, $aSplashScreens, $aCaptions ) = $this->build_playlist( $this->aCurArgs, $media, $src1, $src2, $rtmp, $splash_img );
    }
    
    if( !$this->_get_option('old_code')  && count($aPlaylistItems) == 1 ) {
      $playlist_items_external_html = false;
      $attributes['data-item'] = json_encode( apply_filters( 'fv_player_item', $aPlaylistItems[0], 0, $this->aCurArgs ), JSON_HEX_APOS );
    }
    
    $this->aCurArgs = apply_filters( 'fv_flowplayer_args', $this->aCurArgs, $this->hash, $media, $aPlaylistItems );
    
    
    $player_type = apply_filters( 'fv_flowplayer_player_type', $player_type, $this->hash, $media, $aPlaylistItems, $this->aCurArgs );
    
    
    /*
     *  Allow plugins to create custom playlist styles
     */
    $res = apply_filters( 'fv_flowplayer_playlist_style', false, $this->aCurArgs, $aPlaylistItems, $aSplashScreens, $aCaptions );
    if( $res ) {
      return $res;
    }  
    
    
    /*
     *  Video player tabs
     */
 
    if( $player_type == 'video'  && $this->aCurArgs['liststyle'] == 'tabs' && count($aPlaylistItems) > 1 ) {
      return $this->get_tabs($aPlaylistItems,$aSplashScreens,$aCaptions);            
    }
    
    
    /*
     *  Autoplay
     */
    $autoplay = false;  //  todo: should be changed into a property
    if( $this->_get_option('autoplay') && $this->aCurArgs['autoplay'] != 'false'  ) {
      $autoplay = true;
    }  
    if( isset($this->aCurArgs['autoplay']) && $this->aCurArgs['autoplay'] == 'true') {
      $autoplay = true;
    }
    
    
    /*
     *  Video player
     */
    if( $player_type == 'video' ) {
      
        if (!empty($media)) {
          $media = $this->get_video_url($media);
        }
        if (!empty($src1)) {
          $src1 = $this->get_video_url($src1);
        }
        if (!empty($src2)) {
          $src2 = $this->get_video_url($src2);
        }
        $mobile = ( isset($this->aCurArgs['mobile']) && !empty($this->aCurArgs['mobile']) ) ? trim($this->aCurArgs['mobile']) : false;  
        if (!empty($mobile)) {
          $mobile = $this->get_video_url($mobile);
        }      
      
        if( is_feed() ) {
          $this->ret['html'] = '<p class="fv-flowplayer-feed"><a href="'.get_permalink().'" title="'.__('Click to watch the video').'">'.apply_filters( 'fv_flowplayer_rss_intro_splash', __('[This post contains video, click to play]') );
          if( $splash_img ) {
            $this->ret['html'] .= '<br /><img src="'.$splash_img.'" width="400" />';
          }
          $this->ret['html'] .= '</a></p>';
          
          $this->ret['html'] = apply_filters( 'fv_flowplayer_rss', $this->ret['html'], $this );
          
          return $this->ret;
        }
        
        $bHTTPs = false;
        foreach( apply_filters( 'fv_player_media', array( $mobile, $media, $src1, $src2), $this ) AS $media_item ) {
          if( stripos($media_item,'https://') === 0 ) {
            $bHTTPs = true;
          }
        }
        
        if( !$bHTTPs && function_exists('is_amp_endpoint') && is_amp_endpoint() || count($aPlaylistItems) > 1 && function_exists('is_amp_endpoint') && is_amp_endpoint() ) {          
          $this->ret['html'] = '<p class="fv-flowplayer-feed"><a href="'.get_permalink().'" title="'.__('Click to watch the video').'">'.apply_filters( 'fv_flowplayer_rss_intro_splash', __('[This post contains advanced video player, click to open the original website]') );
          if( $splash_img ) {
            $this->ret['html'] .= '<br /><img src="'.$splash_img.'" width="400" />';
          }
          $this->ret['html'] .= '</a></p>';
          
          $this->ret['html'] = apply_filters( 'fv_flowplayer_amp_link', $this->ret['html'], $this );
          
          return $this->ret;
        
        } else if( function_exists('is_amp_endpoint') && is_amp_endpoint() ) {          
          $this->ret['html'] .= "\t".'<video controls';      
          if (isset($splash_img) && !empty($splash_img)) {
            $this->ret['html'] .= ' poster="'.flowplayer::get_encoded_url($splash_img).'"';
          } 
          if( $autoplay == true ) {
            $this->ret['html'] .= ' autoplay';  
          }
          
          if( stripos($width,'%') == false && intval($width) > 0 ) {
            $this->ret['html'] .= ' width="'.$width.'"'; 
          }
          if( stripos($height,'%') == false && intval($height) > 0 ) {
            $this->ret['html'] .= ' height="'.$height.'"';
          }
          
          $this->ret['html'] .= ">\n";
          
          if (!empty($mobile)) {
            $this->ret['html'] .= $this->get_video_src($mobile, array( 'id' => 'wpfp_'.$this->hash.'_mobile' ) );
          } else {
             foreach( apply_filters( 'fv_player_media', array($media, $src1, $src2), $this ) AS $media_item ) {    
              $this->ret['html'] .= $this->get_video_src($media_item);
            }
          }
          
          $this->ret['html'] .= "\t".'</video>';
          
          $this->ret['html'] = apply_filters( 'fv_flowplayer_amp', $this->ret['html'], $this );
          
          return $this->ret;
        }    
    
        foreach( array( $media, $src1, $src2 ) AS $media_item ) {
          //if( ( strpos($media_item, 'amazonaws.com') !== false && stripos( $media_item, 'http://s3.amazonaws.com/' ) !== 0 && stripos( $media_item, 'https://s3.amazonaws.com/' ) !== 0  ) || stripos( $media_item, 'rtmp://' ) === 0 ) {  //  we are also checking amazonaws.com due to compatibility with older shortcodes
          
          if( !$this->_get_option('engine') && stripos( $media_item, '.m4v' ) !== false ) {
            $this->ret['script']['fv_flowplayer_browser_ff_m4v'][$this->hash] = true;
          }
          
        }
        
        $popup = '';
        
        $aSubtitles = $this->get_subtitles();
      
        $show_splashend = false;
        if (isset($this->aCurArgs['splashend']) && $this->aCurArgs['splashend'] == 'show' && isset($this->aCurArgs['splash']) && !empty($this->aCurArgs['splash'])) {      
          $show_splashend = true;
          $splashend_contents = '<div id="wpfp_'.$this->hash.'_custom_background" class="wpfp_custom_background" style="position: absolute; background: url(\''.$splash_img.'\') no-repeat center center; background-size: contain; width: 100%; height: 100%; z-index: 1;"></div>';
        }
        
        
  
        
        
        $attributes['class'] = 'flowplayer no-brand is-splash';
      
        if( $autoplay ) {
          $attributes['data-fvautoplay'] = 'true';
        }
        
        if( !empty($this->aCurArgs['splash_text']) ) {
          $attributes['class'] .= ' has-splash-text';
        }

        if( isset($this->aCurArgs['playlist_hide']) && strcmp($this->aCurArgs['playlist_hide'],'true') == 0 ) {
          $attributes['class'] .= ' playlist-hidden';
        }
        
        $bIsAudio = false;
        if( ( empty($splash_img) || $splash_img == $this->_get_option('splash') ) && preg_match( '~\.(mp3|wav|ogg)([?#].*?)?$~', $media ) ) {
          $bIsAudio = true;
          $attributes['class'] .= ' is-audio fixed-controls is-mouseover';
        }
        
        //  Fixed control bar
        $bFixedControlbar = $this->_get_option('ui_fixed_controlbar');
        if( isset($this->aCurArgs['controlbar']) ) {
          if( strcmp($this->aCurArgs['controlbar'],'yes') == 0 || strcmp($this->aCurArgs['controlbar'],'show') == 0 ) {
            $bFixedControlbar = true;
          } else if( strcmp($this->aCurArgs['controlbar'],'no') == 0 ) {
            $attributes['class'] .= ' no-controlbar';
          }
        }
        if( $bFixedControlbar ) {
          $attributes['class'] .= ' fixed-controls';
        }
        
        //  Play button
        $bPlayButton = $this->_get_option('ui_play_button');
        if( isset($this->aCurArgs['play_button']) ) {
          if( strcmp($this->aCurArgs['play_button'],'yes') == 0 ) {
            $bPlayButton = true;
          } else if( strcmp($this->aCurArgs['play_button'],'no') == 0 ) {
            $bPlayButton = false;
          }
        }
        if( $bPlayButton ) {
          $attributes['class'] .= ' fvp-play-button';
        }
        
        //  Align
        $attributes['class'] .= $this->get_align();
        
        if( $this->_get_option('engine') || $this->aCurArgs['engine'] == 'flash' ) {
          $attributes['data-engine'] = 'flash';
        }
        
        if( $this->_get_option( array( 'integrations', 'embed_iframe' ) ) ) {
          if( $this->aCurArgs['embed'] == 'false' || ( $this->_get_option('disableembedding') && $this->aCurArgs['embed'] != 'true' ) ) {
            
          } else {
            $attributes['data-fv-embed'] = $this->get_embed_url();
          }
        } else {
          if( $this->aCurArgs['embed'] == 'false' || ( $this->_get_option('disableembedding') && $this->aCurArgs['embed'] != 'true' ) ) {
            $attributes['data-embed'] = 'false';
          } 
        }

        if( isset($this->aCurArgs['logo']) && $this->aCurArgs['logo'] ) {
          $attributes['data-logo'] = ( strcmp($this->aCurArgs['logo'],'none') == 0 ) ? '' : $this->aCurArgs['logo'];
        }
        
        $attributes['style'] = '';
        if( !$bIsAudio ) {
          if( intval($width) == 0 ) $width = '100%';
          if( intval($height) == 0 ) $height = '100%';
          $cssWidth = stripos($width,'%') !== false ? $width : $width . 'px';
          $cssHeight = stripos($height,'%') !== false ? $height : $height. 'px';          
          if( $this->_get_option('fixed_size') ) {
            $attributes['style'] .= 'width: ' . $cssWidth . '; height: ' . $cssHeight . '; ';
          } else {
            $attributes['style'] .= 'max-width: ' . $cssWidth . '; max-height: ' . $cssHeight . '; ';
          }
        }
        
        global $fv_wp_flowplayer_ver;
        //$attributes['data-swf'] = FV_FP_RELATIVE_PATH.'/flowplayer/flowplayer.swf?ver='.$fv_wp_flowplayer_ver;  //  it's better to have this in flowplayer.conf
        //$attributes['data-flashfit'] = "true";
        
        if( $this->_get_option('googleanalytics') ) {
          $attributes['data-analytics'] = $this->_get_option('googleanalytics');
        }  
                
        list( $rtmp_server, $rtmp ) = $this->get_rtmp_server($rtmp);        
        if( /*count($aPlaylistItems) == 0 &&*/ $rtmp_server) {
          $attributes['data-rtmp'] = $rtmp_server;
        }
        
        if( !$bIsAudio ) {
          $this->get_video_checker_media($attributes, $media, $src1, $src2, $rtmp);
        }
    

        if( !$this->_get_option('allowfullscreen') ) {
          $attributes['data-fullscreen'] = 'false';
        }
        
        if( !$bIsAudio && stripos($width,'%') == false && intval($width) > 0 && stripos($height,'%') == false && intval($height) > 0 ) {
          $ratio = round($height / $width, 4);   
          $this->fRatio = $ratio;
  
          $attributes['data-ratio'] = str_replace(',','.',$ratio);
        }
        
        if( $this->_get_option('scaling') && $this->_get_option('fixed_size') ) {
          $attributes['data-flashfit'] = 'true';
        }
        
        if( isset($this->aCurArgs['live']) && $this->aCurArgs['live'] == 'true' ) {
          $attributes['data-live'] = 'true';
        }
        
        $playlist = '';
        $is_preroll = false;
        if( isset($playlist_items_external_html) ) {
          
          if( $bIsAudio ) {
            $playlist_items_external_html = str_replace( 'class="fp-playlist-external', 'class="fp-playlist-external is-audio', $playlist_items_external_html );
          }
          
          if( $this->aCurArgs['liststyle'] == 'prevnext' || ( isset($this->aCurArgs['playlist_hide']) && $this->aCurArgs['playlist_hide']== 'true' ) ) {
            $playlist_items_external_html = str_replace( 'class="fp-playlist-external', 'style="display: none" class="fp-playlist-external', $playlist_items_external_html );
          }
          
          if( count($aPlaylistItems) == 1 && !empty($this->aCurArgs['caption']) ) {
            $attributes['class'] .= ' has-caption';
            $this->sHTMLAfter .= apply_filters( 'fv_player_caption', "<p class='fp-caption'>".$this->aCurArgs['caption']."</p>", $this );
          }
          $this->sHTMLAfter .= $playlist_items_external_html;
          
          if( $this->_get_option('old_code') ) {
            $this->aPlaylists["wpfp_{$this->hash}"] = $aPlaylistItems;
          }

          if( !empty($splash_img) ) {
            $attributes['style'] .= "background-image: url({$splash_img});";
          }
          
        } else if( !empty($this->aCurArgs['caption']) ) {
          $attributes['class'] .= ' has-caption';
          $this->sHTMLAfter = apply_filters( 'fv_player_caption', "<p class='fp-caption'>".$this->aCurArgs['caption']."</p>", $this );
          
        }
        
        if( !empty($this->aCurArgs['redirect']) ) {
          $attributes['data-fv_redirect'] = trim($this->aCurArgs['redirect']);
        }
        
        if (isset($this->aCurArgs['loop']) && $this->aCurArgs['loop'] == 'true') {
          $attributes['data-fv_loop'] = true;
        }
        
        if( isset($this->aCurArgs['admin_warning']) ) {
          $this->sHTMLAfter .= wpautop($this->aCurArgs['admin_warning']);
        }
        
        if( $this->_get_option('ad_show_after') ) {
          $attributes['data-ad_show_after'] = $this->_get_option('ad_show_after');
        }
        if( count($aPlaylistItems) ) {
          if( isset($this->aCurArgs['playlist_advance']) && $this->aCurArgs['playlist_advance'] === 'false' ){
            $attributes['data-advance'] = 'false';
          }elseif(empty($this->aCurArgs['playlist_advance']) ) {
            if( $this->_get_option('playlist_advance') ) {
              $attributes['data-advance'] = 'false';
            }
          }
        }
        
        $attributes_html = '';
        $attributes = apply_filters( 'fv_flowplayer_attributes', $attributes, $media, $this );
        foreach( $attributes AS $attr_key => $attr_value ) {
          $attributes_html .= ' '.$attr_key.'="'.esc_attr( $attr_value ).'"';
        }
        
        $this->ret['html'] .= '<div id="wpfp_' . $this->hash . '"'.$attributes_html.'>'."\n";
        
        if( !$bIsAudio && isset($this->fRatio) ) {
          $this->ret['html'] .= "\t".'<div class="fp-ratio" style="padding-top: '.str_replace(',','.',$this->fRatio * 100).'%"></div>'."\n";
        }

        if( count($aPlaylistItems) == 0 ) {  // todo: this stops subtitles, mobile video, preload etc.
          $this->ret['html'] .= "\t".'<video class="fp-engine" preload="none"';
          if (isset($splash_img) && !empty($splash_img)) {
            $this->ret['html'] .= ' poster="'.flowplayer::get_encoded_url($splash_img).'"';
          } 

          $this->ret['html'] .= ">\n";

          if( isset($rtmp) && !empty($rtmp) ) {
            
            foreach( apply_filters( 'fv_player_media_rtmp', array($rtmp),$this ) AS $rtmp_item ) {            
              $rtmp_item = apply_filters( 'fv_flowplayer_video_src', $rtmp_item, $this );

              if( preg_match( '~([a-zA-Z0-9]+)?:~', $rtmp ) ) {
                $aTMP = preg_split( '~([a-zA-Z0-9]+)?:~', $rtmp, -1, PREG_SPLIT_DELIM_CAPTURE );
  
                if( isset($aTMP[1]) && isset($aTMP[2]) ) {             
                  $rtmp_file = $aTMP[2];
                  $extension = $this->get_mime_type($rtmp_file, $aTMP[1], true);
                } else {
                  $rtmp_file = $aTMP[1];
                  $extension = $this->get_mime_type($rtmp_file, false, true);                  
                }
              } else {
                $rtmp_url = parse_url($rtmp_item);
                $rtmp_file = $rtmp_url['path'] . ( ( !empty($rtmp_url['query']) ) ? '?'. str_replace( '&amp;', '&', $rtmp_url['query'] ) : '' );
                $extension = $this->get_mime_type($rtmp_url['path'], false, true);                
              }

              if( $extension ) {
                $extension .= ':';
              } else {
                //$extension = 'mp4:';  //  https://github.com/flowplayer/flowplayer/search?q=rtmp&type=Issues&utf8=%E2%9C%93
              }

              $this->ret['html'] .= "\t"."\t".'<source src="'.$extension.trim($rtmp_file, " \t\n\r\0\x0B/").'" type="video/flash" />'."\n";
            }
          }          
          
          foreach( apply_filters( 'fv_player_media', array($media, $src1, $src2), $this ) AS $media_item ) {    
            $this->ret['html'] .= $this->get_video_src($media_item, array( 'rtmp' => $rtmp ) );
          }
          if (!empty($mobile)) {
            $this->ret['script']['fv_flowplayer_mobile_switch'][$this->hash] = true;
            $this->ret['html'] .= $this->get_video_src($mobile, array( 'id' => 'wpfp_'.$this->hash.'_mobile', 'rtmp' => $rtmp ) );
          }      
          
          if (isset($aSubtitles) && !empty($aSubtitles)) {
            $aLangs = self::get_languages();
            $countSubtitles = 0;
            foreach( $aSubtitles AS $key => $subtitles ) {
              if( $key == 'subtitles' ) {                   
                $aLang = explode('-', get_bloginfo('language'));
                $sExtra = !empty($aLang[0]) ? 'srclang="'.$aLang[0].'" ' : '';
                $sCode = $aLang[0];
                
                $sCaption = '';
                if( !empty($sCode) && $sCode == 'en' ) {
                  $sCaption = 'English';
                
                } elseif( !empty($sCode) ) {
                  $translations = get_site_transient( 'available_translations' );
                  $sLangCode = str_replace( '-', '_', get_bloginfo('language') );
                  if( $translations && isset($translations[$sLangCode]) && !empty($translations[$sLangCode]['native_name']) ) {
                    $sCaption = $translations[$sLangCode]['native_name'];
                  }
                  
                }
                
                if( $sCaption ) {
                  $sExtra .= 'label="'.$sCaption.'" ';
                }
                
              } else {
                $sExtra = 'srclang="'.$key.'" label="'.$aLangs[strtoupper($key)].'" ';
              }
              
              if( $countSubtitles == 0 && $this->_get_option('subtitleOn') ) {
                $sExtra .= 'default ';
              }
              
              $countSubtitles++;
              $this->ret['html'] .= "\t"."\t".'<track '.$sExtra.'src="'.esc_attr($subtitles).'" />'."\n";
            }
          }     
          
          $this->ret['html'] .= "\t".'</video>';//."\n";
        }
        
        $this->ret['html'] .= $this->get_buttons();
        
        if( isset($splashend_contents) ) {
          $this->ret['html'] .= $splashend_contents;
        }
        if( $popup_contents = $this->get_popup_code() ) {
          $this->aPopups["wpfp_{$this->hash}"] = $popup_contents;  
        }
        if( $ad_contents = $this->get_ad_code() ) {
          $this->aAds["wpfp_{$this->hash}"] = $ad_contents;  
        }
        
        if( flowplayer::is_special_editor() ) {
          $this->ret['html'] .= '<div class="fp-ui"></div>';       
        } else if( current_user_can('manage_options') && !isset($playlist_items_external_html) ) {
          $this->ret['html'] .= '<div id="wpfp_'.$this->hash.'_admin_error" class="fvfp_admin_error"><div class="fvfp_admin_error_content"><h4>Admin JavaScript warning:</h4><p>I\'m sorry, your JavaScript appears to be broken. Please use "Check template" in plugin settings, read our <a href="https://foliovision.com/player/installation#fixing-broken-javascript" target="_blank">troubleshooting guide</a>, <a href="https://foliovision.com/troubleshooting-javascript-errors" target="_blank">troubleshooting guide for programmers</a> or <a href="http://foliovision.com/wordpress/pro-install" target="_blank">order our pro support</a> and we will get it fixed for you.</p></div></div>';       
        }
        
        $this->ret['html'] .= apply_filters( 'fv_flowplayer_inner_html', null, $this );
        
        if( !$bIsAudio ) {
          $this->ret['html'] .= $this->get_sharing_html()."\n";
        }
        
        if( !empty($this->aCurArgs['splash_text']) ) {
          $aSplashText = explode( ';', $this->aCurArgs['splash_text'] );         
          $this->ret['html'] .= "<div class='fv-fp-splash-text'><span class='custom-play-button'>".$aSplashText[0]."</span></div>\n"; //  needed for soap customizations of play button!
        }

        if( current_user_can('manage_options') && !$this->_get_option('disable_videochecker') ) {
          $this->ret['html'] .= $this->get_video_checker_html()."\n";
        }
        
        if ($this->aCurArgs['liststyle'] == 'prevnext' && count($aPlaylistItems)) {
          $this->ret['html'].='<a class="fp-prev" title="prev">&lt;</a><a class="fp-next" title="next">&gt;</a>'; 
        }          
        
        $this->ret['html'] .= '</div>'."\n";
        
        $this->ret['html'] .= $this->sHTMLAfter.$scripts_after;
        
                 if( get_query_var('fv_player_embed') ) {  //  this is needed for iframe embedding only
                   $this->ret['html'] .= "<!--fv player end-->";
                 }
        
        //  change engine for IE9 and 10
        if( $this->aCurArgs['engine'] == 'false' ) {
          $this->ret['script']['fv_flowplayer_browser_ie'][$this->hash] = true;
        }        
        
    } //  end Video player
    
    
    /*
     *  Youtube player
     */
    else if( $player_type == 'youtube' ) {
        
      $sAutoplay = ($autoplay) ? 'autoplay=1&amp;' : '';
      $this->ret['html'] .= "<iframe id='fv_ytplayer_{$this->hash}' type='text/html' width='{$width}' height='{$height}'
    src='//www.youtube.com/embed/$youtube?{$sAutoplay}origin=".urlencode(get_permalink())."' frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>\n";
      
    }
    
    
    /*
     *  Vimeo player
     */
    else if( $player_type == 'vimeo' ) {
    
      $sAutoplay = ($autoplay) ? " autoplay='1'" : "";
      $this->ret['html'] .= "<iframe id='fv_vimeo_{$this->hash}' src='//player.vimeo.com/video/{$vimeo}' width='{$width}' height='{$height}' frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen{$sAutoplay}></iframe>\n";
      
    }
    
    
    /*
     *  Wistia player
     */
    else if( $player_type == 'wistia' ) {
      $ratio = $width > 0 ? ' data-ratio="'.($height/$width).'"' : '';
      $this->ret['html'] .= "</script><script src='//fast.wistia.com/assets/external/E-v1.js' async></script><div class='wistia_embed wistia_async_{$wistia}' style='height:{$height}px;max-width:{$width}px'".$ratio.">&nbsp;</div>\n";
      
    }
    
    
    /*
     *  Audio player
     */
    else {  //  $player_type == 'video' ends
      $this->build_audio_player( $media, $width, $autoplay );
    }
    
    if( isset($this->aCurArgs['liststyle']) && $this->aCurArgs['liststyle'] == 'vertical' && count($aPlaylistItems) > 1 ){
      $this->ret['html'] = '<div class="fp-playlist-vertical-wrapper">'.$this->ret['html'].'</div>';
    }
    if( isset($this->aCurArgs['liststyle']) && $this->aCurArgs['liststyle'] == 'text' && count($aPlaylistItems) > 1 ){
      $this->ret['html'] = '<div class="fp-playlist-text-wrapper">'.$this->ret['html'].'</div>';
    }
    $this->ret['html'] = apply_filters( 'fv_flowplayer_html', $this->ret['html'], $this );

    
    
    $this->ret['script'] = apply_filters( 'fv_flowplayer_scripts_array', $this->ret['script'], 'wpfp_' . $this->hash, $media );
      
      return $this->ret;
  }
  
  
  function build_audio_player( $media, $width, $autoplay ) {          
      $this->load_mediaelement = true;
      
      $preload = ($autoplay == true) ? '' : ' preload="none"'; 
          
      $this->ret['script']['mediaelementplayer'][$this->hash] = true;
      $this->ret['html'] .= '<div id="wpfp_' . $this->hash . '" class="fvplayer fv-mediaelement">'."\n";      
      $this->ret['html'] .= "\t".'<audio src="'.$this->get_video_src( $media, array( 'url_only' => true ) ).'" type="audio/'.$this->get_mime_type($media, false, true).'" controls="controls" '.$preload.' style="width:100%;height:100%"></audio>'."\n";  
      $this->ret['html'] .= '</div>'."\n";  
  }
  
  
  function get_ad_code() {
    $ad_contents = false;
    
    if(
      ( trim($this->_get_option('ad')) || ( isset($this->aCurArgs['ad']) && !empty($this->aCurArgs['ad']) ) ) 
      && !strlen($this->aCurArgs['ad_skip'])        
    ) {
      if (isset($this->aCurArgs['ad']) && !empty($this->aCurArgs['ad'])) {
        $ad = trim($this->aCurArgs['ad']);
        if( stripos($ad,'<!--fv_flowplayer_base64_encoded-->') !== false ) {
          $ad = str_replace('<!--fv_flowplayer_base64_encoded-->','',$ad);
          $ad = html_entity_decode( str_replace( array('\"','\[','\]'), array('"','[',']'), base64_decode($ad) ) );
        } else {
          $ad = html_entity_decode( str_replace('&#039;',"'",$ad ) );
        }

        $ad_width = ( isset($this->aCurArgs['ad_width']) && intval($this->aCurArgs['ad_width']) > 0 ) ? intval($this->aCurArgs['ad_width']).'px' : '100%';  
        $ad_height = ( isset($this->aCurArgs['ad_height']) && intval($this->aCurArgs['ad_height']) > 0 ) ? intval($this->aCurArgs['ad_height']).'px' : '';          
      }
      else {
        $ad = trim( $this->_get_option('ad') );      
        $ad_width = ( $this->_get_option('ad_width') ) ? $this->_get_option('ad_width').'px' : '100%';  
        $ad_height = ( $this->_get_option('ad_height') ) ? $this->_get_option('ad_height').'px' : '';
      }
     
      
      if( $this->_get_option('ad_show_after') ){
        $ad_display = 'none';
      }else{
        $ad_display = 'block' ;
      }
      
      
      
      $ad = apply_filters( 'fv_flowplayer_ad_html', $ad);
      if( strlen(trim($ad)) > 0 ) {      
        $ad_contents = array(
                             'html' => "<div class='wpfp_custom_ad_content' style='width: $ad_width; height: $ad_height; display:$ad_display;'>\n\t\t<div class='fv_fp_close'><a href='#' onclick='jQuery(\"#wpfp_".$this->hash."_ad\").fadeOut(); return false'></a></div>\n\t\t\t".$ad."\n\t\t</div>",
                             'width' => $ad_width,
                             'height' => $ad_height
                            );                 
      }
    }
    //var_dump($ad_contents);die();
    return $ad_contents;
  }
  
  
  function get_align() {
    $sClass = false;
    if( isset($this->aCurArgs['align']) && ( empty($this->aCurArgs['liststyle']) || $this->aCurArgs['liststyle'] != 'vertical' ) ) {
      if( $this->aCurArgs['align'] == 'left' ) {
        $sClass .= ' alignleft';
      } else if( $this->aCurArgs['align'] == 'right' ) {
        $sClass .= ' alignright';
      } else if( $this->aCurArgs['align'] == 'center' ) {
        $sClass .= ' aligncenter';
      } 
    }
    return $sClass;
  }
  
    
  function get_buttons() {
    add_filter( 'fv_flowplayer_buttons_center', array( $this, 'get_speed_buttons' ) );
    
    $sHTML = false;
    foreach( array('left','center','right','controlbar') AS $key ) {
      $aButtons = apply_filters( 'fv_flowplayer_buttons_'.$key, array() );
      if( !$aButtons || !count($aButtons) ) continue;

      $sButtons = implode( '', $aButtons );
      $sHTML .= "<div class='fv-player-buttons fv-player-buttons-$key'>$sButtons</div>";
    }
    if( $sHTML ) {
      $sHTML = "<div class='fv-player-buttons-wrap'>$sHTML</div>";
    }

//var_dump($sHTML);die();
    return $sHTML;
  }
  
  
  function get_embed_url() {
    if( empty($this->aPlayers[get_the_ID()]) ) {
      $this->aPlayers[get_the_ID()] = 1;
      $append = 'fvp';
      $append_num = 1;
    } else {
      $this->aPlayers[get_the_ID()]++;
      $append_num = $this->aPlayers[get_the_ID()];
      $append = 'fvp'.$append_num;      
    }
    
    $rewrite = get_option('rewrite_rules');
    if( empty($rewrite) ) {
      return add_query_arg( 'fv_player_embed', $append_num, get_permalink() );
    } else {
      return user_trailingslashit( trailingslashit( get_permalink() ).$append );
    }
  }
  
  
  function get_popup_code() {
    if (!empty($this->aCurArgs['popup'])) {
      $popup = trim($this->aCurArgs['popup']);
    } else {
      $popup = $this->_get_option('popups_default');
    }
    if (stripos($popup, '<!--fv_flowplayer_base64_encoded-->') !== false) {
      $popup = str_replace('<!--fv_flowplayer_base64_encoded-->', '', $popup);
      $popup = html_entity_decode(str_replace(array('\"', '\[', '\]'), array('"', '[', ']'), base64_decode($popup)));
    } else {
      $popup = html_entity_decode(str_replace('&#039;', "'", $popup));
    }

    if ($popup === 'no') {
      return false;
    }

    $iPopupIndex = 1;
    if ($popup === 'random' || is_numeric($popup)) {
      $aPopupData = get_option('fv_player_popups');
      if ($popup === 'random') {
        $iPopupIndex = rand(1, count($aPopupData));
      } elseif (is_numeric($popup)) {
        $iPopupIndex = intval($popup);
      }

      if (isset($aPopupData[$iPopupIndex])) {
        $popup = $aPopupData[$iPopupIndex]['html'];
      } else {
        return false;
      }
    }

    $sClass = ' fv_player_popup-' . $iPopupIndex;

    $popup = apply_filters('fv_flowplayer_popup_html', $popup);
    if (strlen(trim($popup)) > 0) {
      $popup_contents = array(
          'html' => '<div class="fv_player_popup' . $sClass . ' wpfp_custom_popup_content">' . $popup . '</div>'
      );
      return $popup_contents;
    }

    return false;
  }

  function get_rtmp_server($rtmp) {
    $rtmp_server = false;
    if( !empty($this->aCurArgs['rtmp']) ) {
      $rtmp_server = trim( $this->aCurArgs['rtmp'] );
    } else if( isset($rtmp) && stripos( $rtmp, 'rtmp://' ) === 0 && stripos($this->_get_option('rtmp'), $rtmp ) === false  ) {
      if( preg_match( '~/([a-zA-Z0-9]+)?:~', $rtmp ) ) {
        $aTMP = preg_split( '~/([a-zA-Z0-9]+)?:~', $rtmp, -1, PREG_SPLIT_DELIM_CAPTURE );
        $rtmp_server = $aTMP[0];
      } else {
        $rtmp_info = parse_url($rtmp);
        if( isset($rtmp_info['host']) && strlen(trim($rtmp_info['host']) ) > 0 ) {
          $rtmp_server = 'rtmp://'.$rtmp_info['host'].'/cfx/st';
        }
      }
    } else if( $this->_get_option('rtmp') ) {
      $rtmp_server = $this->_get_option('rtmp');
      if( stripos( $rtmp_server, 'rtmp://' ) === 0 ) {        
        $rtmp = str_replace( $rtmp_server, '', $rtmp );
      } else {
        $rtmp_server = 'rtmp://' . $rtmp_server . '/cfx/st/';
      }
    }
    return array( $rtmp_server, $rtmp );
  }
  
  
  
  function get_speed_buttons( $aButtons ) {
    $bShow = false;
    if( $this->_get_option('ui_speed') || isset($this->aCurArgs['speed']) && $this->aCurArgs['speed'] == 'buttons' ) {
      $bShow = true;
    }
    
    if( isset($this->aCurArgs['speed']) && $this->aCurArgs['speed'] == 'no' ) {
      $bShow = false;
    }

    if( $bShow ) {   
      $aButtons[] = "<ul class='fv-player-speed'><li><a class='fv_sp_slower'>&#45;</a></li><li><a class='fv_sp_faster'>&#43;</a></li></ul>";
    }
    
    return $aButtons;
  }
  
  
  function get_splash() {
    $splash_img = false;
    if (isset($this->aCurArgs['splash']) && !empty($this->aCurArgs['splash'])) {
      $splash_img = $this->aCurArgs['splash'];
      if( strpos($splash_img,'http://') === false && strpos($splash_img,'https://') === false ) {
        $http = is_ssl() ? 'https://' : 'http://';
        
        //$splash_img = VIDEO_PATH.trim($this->aCurArgs['splash']);
        if($splash_img[0]=='/') $splash_img = substr($splash_img, 1);
          if((dirname($_SERVER['PHP_SELF'])!='/')&&(file_exists($_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF']).VIDEO_DIR.$splash_img))){  //if the site does not live in the document root
            $splash_img = $http.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).VIDEO_DIR.$splash_img;
          }
          else
          if(file_exists($_SERVER['DOCUMENT_ROOT'].VIDEO_DIR.$splash_img)){ // if the videos folder is in the root
            $splash_img = $http.$_SERVER['SERVER_NAME'].VIDEO_DIR.$splash_img;//VIDEO_PATH.$media;
          }
          else {
            //if the videos are not in the videos directory but they are adressed relatively
            $splash_img_path = str_replace('//','/',$_SERVER['SERVER_NAME'].'/'.$splash_img);
            $splash_img = $http.$splash_img_path;
          }
      }
      else {
        $splash_img = trim($this->aCurArgs['splash']);
      }            
    } else if( $this->_get_option('splash') ) {
      $splash_img = $this->_get_option('splash');
    }    
    
    $splash_img = apply_filters( 'fv_flowplayer_splash', $splash_img, $this );
    return $splash_img;
  }
  
  
  function get_subtitles($index = 0) {
    $aSubtitles = array();

    if( $this->aCurArgs && count($this->aCurArgs) > 0 ) {
      $protocol = is_ssl() ? 'https' : 'http';
      foreach( $this->aCurArgs AS $key => $subtitles ) {
        if( stripos($key,'subtitles') !== 0 || empty($subtitles) ) {
          continue;
        }
        
        $subtitles = explode( ";",$subtitles);
        if( empty($subtitles[$index]) ) return $aSubtitles;
        
        $subtitles = $subtitles[$index];
  
        if( strpos($subtitles,'http://') === false && strpos($subtitles,'https://') === false ) {
          //$splash_img = VIDEO_PATH.trim($this->aCurArgs['splash']);
          if($subtitles[0]=='/') $subtitles = substr($subtitles, 1);
            if((dirname($_SERVER['PHP_SELF'])!='/')&&(file_exists($_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF']).VIDEO_DIR.$subtitles))){  //if the site does not live in the document root
              $subtitles = $protocol.'://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).VIDEO_DIR.$subtitles;
            }
            else
            if(file_exists($_SERVER['DOCUMENT_ROOT'].VIDEO_DIR.$subtitles)){ // if the videos folder is in the root
              $subtitles = $protocol.'://'.$_SERVER['SERVER_NAME'].VIDEO_DIR.$subtitles;//VIDEO_PATH.$media;
            }
            else {
              //if the videos are not in the videos directory but they are adressed relatively
              $subtitles = str_replace('//','/',$_SERVER['SERVER_NAME'].'/'.$subtitles);
              $subtitles = $protocol.'://'.$subtitles;
            }
        }
        else {
          $subtitles = trim($subtitles);
        }
        
        $aSubtitles[str_replace( 'subtitles_', '', $key )] = $subtitles;
        
      }
    }

    return $aSubtitles;
  }
  
  
  function get_tabs($aPlaylistItems,$aSplashScreens,$aCaptions) {
    global $post;
    
    $this->count_tabs++;
    $output = new stdClass;
    $output->ret = array();
    $output->ret['html'] = '<script>document.body.className += " fv_flowplayer_tabs_hide";</script><div class="fv_flowplayer_tabs tabs woocommerce-tabs"><div id="tabs-'.$post->ID.'-'.$this->count_tabs.'" class="fv_flowplayer_tabs_content">';
    $output->ret['script'] = '';
    
    $output->ret['html'] .= '<ul>';
    foreach( $aPlaylistItems AS $key => $aSrc ) {
      $sCaption = !empty($aCaptions[$key]) ? $aCaptions[$key] : $key;
      $output->ret['html'] .= '<li><a href="#tabs-'.$post->ID.'-'.$this->count_tabs.'-'.$key.'">'.$sCaption.'</a></li>';
    }
    $output->ret['html'] .= '</ul><div class="fv_flowplayer_tabs_cl"></div>';

    $aStartend = !empty($this->aCurArgs['startend']) ? explode(";",$this->aCurArgs['startend']) : false;  //  todo: somehow move to Pro?
    
    foreach( $aPlaylistItems AS $key => $aSrc ) {
      $this->aCurArgs['startend'] = isset($aStartend[$key]) ? $aStartend[$key] : false;
      
      unset($this->aCurArgs['playlist']);
      $this->aCurArgs['src'] = $aSrc['sources'][0]['src'];  //  todo: remaining sources!
      
      $this->aCurArgs['splash'] = isset($aSplashScreens[$key])?$aSplashScreens[$key]:'';
      unset($this->aCurArgs['caption']);
      $this->aCurArgs['liststyle']='none';
      
      $aPlayer = $this->build_min_player( $this->aCurArgs['src'],$this->aCurArgs );
      $sClass = $key == 0 ? ' class="fv_flowplayer_tabs_first"' : '';
      $output->ret['html'] .= '<div id="tabs-'.$post->ID.'-'.$this->count_tabs.'-'.$key.'"'.$sClass.'>'.$aPlayer['html'].'</div>';
      foreach( $aPlayer['script'] AS $key => $value ) {
        $output->ret['script'][$key] = array_merge( isset($output->ret['script'][$key]) ? $output->ret['script'][$key] : array(), $aPlayer['script'][$key] );
      }
    }
    $output->ret['html'] .= '<div class="fv_flowplayer_tabs_cl"></div><div class="fv_flowplayer_tabs_cr"></div></div></div>';
          
    $this->load_tabs = true;
          
    return $output->ret;    
  }
  
  
  function get_video_checker_media($attributes, $media, $src1, $src2, $rtmp) {

    if( current_user_can('manage_options') && $this->ajax_count < 100 && !$this->_get_option('disable_videochecker') && ( $this->_get_option('video_checker_agreement') || $this->_get_option('key_automatic') ) ) {
      $this->ajax_count++;
      
      if( stripos($rtmp,'rtmp://') === false && $rtmp ) {
        list( $rtmp_server, $rtmp ) = $this->get_rtmp_server($rtmp);
        $rtmp = trailingslashit($rtmp_server).$rtmp;
      }
    
      $aTest_media = array();
      foreach( array( $media, $src1, $src2, $rtmp ) AS $media_item ) {
        if( $media_item ) {
          $aTest_media[] = $this->get_video_src( $media_item, array( 'flash' => false, 'url_only' => true, 'dynamic' => true ) );
          //break;
        } 
      }
      
      if( !empty($this->aCurArgs['mobile']) ) {
        $aTest_media[] = $this->get_video_src($this->aCurArgs['mobile'], array( 'flash' => false, 'url_only' => true, 'dynamic' => true ) );
      }

      if( isset($aTest_media) && count($aTest_media) > 0 ) { 
        $this->ret['script']['fv_flowplayer_admin_test_media'][$this->hash] = $aTest_media;
      }
    }            

  }
  
  
  function get_sharing_html() {
    global $post;
    
    $sSharingText = $this->_get_option('sharing_email_text' );
    $bVideoLink = empty($this->aCurArgs['linking']) ? !$this->_get_option('disable_video_hash_links' ) : $this->aCurArgs['linking'] === 'true';
    
    if( isset($this->aCurArgs['share']) && $this->aCurArgs['share'] ) { 
      $aSharing = explode( ';', $this->aCurArgs['share'] );
      if( count($aSharing) == 2 ) {
        $sPermalink = urlencode($aSharing[1]);
        $sMail = rawurlencode( apply_filters( 'fv_player_sharing_mail_content',$sSharingText.': '.$aSharing[1] ) );
        $sTitle = urlencode( $aSharing[0].' ');
        $bVideoLink = false;
      } else if( count($aSharing) == 1 && $this->aCurArgs['share'] != 'yes' && $this->aCurArgs['share'] != 'no' ) {
        $sPermalink = urlencode($aSharing[0]);
        $sMail = rawurlencode( apply_filters( 'fv_player_sharing_mail_content', $sSharingText.': '.$aSharing[0] ) );
        $sTitle = urlencode( get_bloginfo().' ');
        $bVideoLink = false;
      }
    }
    
    $sLink = get_permalink();
    if( !isset($sPermalink) || empty($sPermalink) ) {       
      $sPermalink = urlencode(get_permalink());
      $sMail = rawurlencode( apply_filters( 'fv_player_sharing_mail_content', $sSharingText.': '.get_permalink() ) );
      $sTitle = urlencode( html_entity_decode( is_singular() ? get_the_title().' ' : get_bloginfo() ).' ');
    }

          
    $sHTMLSharing = '<ul class="fvp-sharing">
    <li><a class="sharing-facebook" href="https://www.facebook.com/sharer/sharer.php?u=' . $sPermalink . '" target="_blank">Facebook</a></li>
    <li><a class="sharing-twitter" href="https://twitter.com/home?status=' . $sTitle . $sPermalink . '" target="_blank">Twitter</a></li>
    <li><a class="sharing-google" href="https://plus.google.com/share?url=' . $sPermalink . '" target="_blank">Google+</a></li>
    <li><a class="sharing-email" href="mailto:?body=' . $sMail . '" target="_blank">Email</a></li></ul>';
    
    if( isset($post) && isset($post->ID) ) {
      $sHTMLVideoLink = $bVideoLink ? '<div><a class="sharing-link" href="' . $sLink . '" target="_blank">Link</a></div>' : '';
    } else {
      $sHTMLVideoLink = false;
    }
    
    if( $this->aCurArgs['embed'] == 'false' ) {
      $sHTMLVideoLink = false;
    }

    $sHTMLEmbed = '<div><label><a class="embed-code-toggle" href="#"><strong>Embed</strong></a></label></div><div class="embed-code"><label>Copy and paste this HTML code into your webpage to embed.</label><textarea></textarea></div>';


    if( $this->aCurArgs['embed'] == 'false' || ( $this->_get_option('disableembedding') && $this->aCurArgs['embed'] != 'true' ) ) {
      $sHTMLEmbed = '';
    }
    
    if( isset($this->aCurArgs['share']) && $this->aCurArgs['share'] == 'no' ) {
      $sHTMLSharing = '';
    } else if( isset($this->aCurArgs['share']) && $this->aCurArgs['share'] && $this->aCurArgs['share'] != 'no' ) {
      
    } else if( $this->_get_option('disablesharing') ) {
      $sHTMLSharing = '';
    }

    $sHTML = false;
    if( $sHTMLSharing || $sHTMLEmbed || $sHTMLVideoLink) {
      $sHTML = "<div class='fvp-share-bar'>$sHTMLSharing$sHTMLVideoLink$sHTMLEmbed</div>";
    }

    return $sHTML;
  }
  
  
  function get_video_checker_html() {
    global $fv_wp_flowplayer_ver;
    $sSpinURL = site_url('wp-includes/images/wpspin.gif');

    $sHTML = <<< HTML
<div title="This note is visible to logged-in admins only." class="fv-wp-flowplayer-notice-small fv-wp-flowplayer-ok" id="wpfp_notice_{$this->hash}" style="display: none">
  <div class="fv_wp_flowplayer_notice_head" onclick="fv_wp_flowplayer_admin_show_notice('{$this->hash}', this.parent); return false">Report Issue</div>
  <small>Admin: <span class="video-checker-result">Checking the video file...</span></small>
  <div style="display: none;" class="fv_wp_fp_notice_content" id="fv_wp_fp_notice_{$this->hash}">
    <div class="mail-content-notice">
    </div>
    <div class="support-{$this->hash}">
      <textarea style="width: 98%; height: 150px" onclick="if( this.value == 'Enter your comment' ) this.value = ''" class="wpfp_message_field" id="wpfp_support_{$this->hash}">Enter your comment</textarea>
      <p><a class="techinfo" href="#" onclick="jQuery('.more-{$this->hash}').toggle(); return false">Technical info</a> <img style="display: none; " src="{$sSpinURL}" id="wpfp_spin_{$this->hash}" /> <input type="button" value="Send report to Foliovision" onclick="fv_wp_flowplayer_admin_support_mail('{$this->hash}', this); return false" /></p></div>
    <div class="more-{$this->hash} mail-content-details" style="display: none; ">
      <p>Plugin version: {$fv_wp_flowplayer_ver}</p>
      <div class="fv-wp-flowplayer-notice-parsed level-0"></div></div>
  </div>
</div>
HTML;

    return $sHTML;
  }
  
  
}

