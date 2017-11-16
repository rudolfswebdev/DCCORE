<?php

class FoldingCategoryList extends WP_Widget 
{
  var $dbOptions = 'widget_focalOptions'; // The options key name
  var $wdOptions = 'widget_focalWidget_'; // Append widget instance no to end
  var $wdCache = 'widget_focalCache_';    // Append widget instance no to end
  var $pageCatCF = 'category';            // Custom field for pages
  var $cacheVersion = '1.1.0';            // This does not change unless cache format changes
  var $focalVersion = 'v2.0.5';
  var $debug = false;
  var $defaultOptions;
  var $widgetOptions;
  var $theTree;
  var $theTreeParental;
  var $categoryFamilyTree;
  var $currentCategory;
  var $setting_number;
  var $hasAdminMessage = false;
  var $adminMessage = '';
  var $itemsBefore = array();
  var $itemsAfter = array();
  /** Wordpress WP_Widget extensions **/ 
  
  /* Constructor - set everything up */
  public function __construct() 
  {
	  $widget_ops = array('classname' => 'widget_focal', 'description' => __('Enhanced Folding Category List for Wordpress 2.8+', 'focal'));
	  parent::__construct( 'widget_focal', 'Folding Category Widget', $widget_ops );
	  //$this->WP_Widget('focal', 'FoCaL', $widget_ops);
	  
      $this->GetOptions();
	
    /* Setup actions to rebuild the cache */
    if ($this->defaultOptions['AutoBuildCache'] === true)
    {
      $this->add_action('publish_post', 'RebuildCache');
      $this->add_action('edit_post', 'RebuildCache');
      $this->add_action('delete_post', 'RebuildCache');
      $this->add_action('create_taxonomy', 'RebuildCache');
      $this->add_action('edit_taxonomy', 'RebuildCache');
      $this->add_action('delete_taxonomy', 'RebuildCache');
    }
	
	if (!is_admin())
	{    
		
		if ($this->defaultOptions['IncludeSuperfishJS'])
		{
		  wp_enqueue_script('superfish', WP_PLUGIN_URL . '/folding-category-widget/js/superfish.js', array('jquery'));
		  $this->add_action('wp_footer', array(&$this, 'superfish_activator'));
		}

		if ($this->defaultOptions['IncludeSuperfishCSS'])
		  wp_enqueue_style('superfish', WP_PLUGIN_URL . '/folding-category-widget/css/superfish.css');

		if ($this->defaultOptions['IncludeAccordianJS'])
		{
	      wp_enqueue_script('accordian', WP_PLUGIN_URL . '/folding-category-widget/js/accordian.js', array('jquery'));
		  $this->add_action('wp_footer', array(&$this, 'accordian_activator'));
		}

		if ($this->defaultOptions['IncludeAccordianCSS'])
		  wp_enqueue_style('accordian', WP_PLUGIN_URL . '/folding-category-widget/css/accordian.css');
	}
	
    /* put the focal menu item on the settings menu */     
    $this->add_action('admin_menu', 'widget_FocalAdminMenu');
  }
 
  function add_action($action, $function = '', $priority = 10, $accepted_args = 1) 
  {
    add_action($action, array(&$this, $function == '' ? $action : $function), $priority, $accepted_args);
  }
 
  
 
  /* render the widget to the sidebar */ 
  function widget($args, $instance) 
  {
	if ($this->debug) echo "<!-- FoCal Debug: Start Widget -->";
	
	extract($args, EXTR_SKIP);
    $title = $instance['title'];
    $this->setting_number = $instance['definition'];
    
	if (!$this->setting_number)
	  $this->setting_number = 'default';
	
	if ($this->debug) echo "<!-- FoCal Debug: Setting - '".$this->setting_number."' -->";
	
	$this->GetOptions($this->setting_number);

    $tree = $this->DrawTheTree();
    	
    if (!empty($tree))
	{
      echo $before_widget;
	  if (!empty($title))
        echo $before_title . $title . $after_title; 
	  echo $tree;
      echo $after_widget;
	}
	else
	{
	  if ($this->debug) echo "<!-- FoCal Debug: No Tree Found -->";
	}
	if ($this->debug) echo "<!-- FoCal Debug: End Widget -->";
  }
 
  /* event triggers when admin panel updated */
  function update($new_instance, $old_instance) 
  {
    $instance = $old_instance;
	$instance['definition'] = strip_tags($new_instance['definition']);
	$instance['title'] = strip_tags($new_instance['title']);
	return $instance;
  }
 
  /* widget admin panel menu config */
  function form($instance) 
  {
	$instance = wp_parse_args((array) $instance, array('title' => '', 'definition' => 'default'));
	$title = strip_tags($instance['title']);
	$setting_number = strip_tags($instance['definition']);

	$definitions = $this->widget_GetSettingDefinitions($setting_number);
	$options = $definitions[1];
	
?>
	<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'focal'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
	<p><label for="<?php echo $this->get_field_id('definition'); ?>"><?php _e('Setting Definition:', 'focal'); ?> <select id="<?php echo $this->get_field_id('definition'); ?>" name="<?php echo $this->get_field_name('definition'); ?>"><?php echo $options; ?></select></label></p>
	<p><small><?php _e('Please goto the <a href="/wp-admin/options-general.php?page=folding-category-widget/focal_wp28.php">settings page</a> to configure definitions.', 'focal'); ?></small></p> 
<?php
  }
	
  function addMenuItemBeforeCategory($html, $id)
  {
    $this->itemsBefore[$id] = $html;
  }
  
  function addMenuItemAfterCategory($html, $id)
  {
    $this->itemsAfter[$id] = $html;
  }
  
	
	
	
  /****** Options ******/
  /* These following methods will perform actions on the options - Get, Set, Update, Defaults */
  
  function GetOptions($instanceNo = null)
  {
	$this->defaultOptions = $this->GetDefaultOptions();    
    $optionsFromTable = get_option($this->dbOptions, false);
    
	if (!$optionsFromTable)
	  $optionsFromTable = array();
	
	
    $this->defaultOptions = array_merge($this->defaultOptions,$optionsFromTable);

	if ($instanceNo != null)
    {	
      $instanceNo = strtolower($instanceNo);
	  $default = $this->GetDefaultWidgetOptions();
      $widgetsFromTable = get_option($this->wdOptions.$instanceNo, false);

      if (($widgetsFromTable != false) && (is_array($widgetsFromTable)))
      {
        $options = $default;
		foreach ($widgetsFromTable as $key => $value)
        {
          $options[$key] = $value;
        }  
      }
      else
      {
        $options = $default;
      }
      $this->widgetOptions = $options;
	}
  }

  function GetDefaultOptions()
  {
    $options['AutoBuildCache'] = true;
    $options['UseCache'] = true;
    $options['DontUseCategoryDescription'] = false;
    $options['IncludeSuperfishJS'] = false;
    $options['IncludeSuperfishCSS'] = false;
    $options['IncludeAccordianJS'] = false;
    $options['IncludeAccordianCSS'] = false;
  
    return $options;
  }
  function GetDefaultWidgetOptions()
  {
    $widget['ApplyCSSTo'] = 'li';
    $widget['RootCategory'] = 0;
    $widget['ExcludeCats'] = array(1);
    $widget['HightlightPriority'] = '';
    $widget['ExpandAllCats'] = false;
    $widget['ExpandAllMember'] = false;
    $widget['OrderBy'] = 'name';
    $widget['OrderByDirection'] = 'asc';
    $widget['UseDescriptionAsCategoryText'] = false;
    $widget['UseDescriptionAsCategoryTitle'] = true;
    $widget['LinksBefore'] = '';
    $widget['LinksAfter'] = '';
    $widget['ShowPostCount'] = true;
    $widget['ShowPostCountUnique'] = true;
    $widget['OutputCatIDs'] = false;
    $widget['ShowEmptyCats'] = false;
    $widget['DontLinkCurrentCategory'] = false;
    $widget['CountBefore'] = '&nbsp;(';
    $widget['CountAfter'] = ')';
    $widget['NoFollowAllLinks'] = false;
    $widget['NoFollowParents'] = false;
    $widget['IncOrEx'] = 'ex';
    $widget['MultiLingual'] = false;
    $widget['Superfish'] = false;
    $widget['Accordian'] = false;
    
    return $widget;
  }
  function SetGlobalOptions()
  {
    $option = $this->dbOptions;
    
	if (get_option($option, false)) 
      update_option($option, $this->defaultOptions);
    else 
      add_option($option, $this->defaultOptions);
  }
  function SetWidgetOptions($instanceNo)
  {
    $instanceNo = strtolower($instanceNo);
	$option = $this->wdOptions.$instanceNo;
    if (get_option($option, false)) 
      update_option($option, $this->widgetOptions);
    else 
      add_option($option, $this->widgetOptions);
  } 
  function RemoveWidgetOptions($instanceNo)
  {
    $instanceNo = strtolower($instanceNo);
	$option = $this->wdOptions.$instanceNo;
    delete_option($option); 
  }  
  
  
  
  /****** Options ******/
  /* These functions deal with the main tree cache */

  function TreeID()
  {
    // When (if) multilingual cache, this method should return the language, for now ml is handled in the drawtree function
	return '1';
  }
  
  function GetATree()
  {
    $tree_id = $this->TreeID();
    if ($this->defaultOptions['UseCache'] == true)
    {
      $option = $this->wdCache.$tree_id;
      $cache = get_option($option, null);
  
      if ($cache != null) 
      {
        if ((count($cache) == 2) && ($cache['version'] == $this->cacheVersion))
        {
          $themenu = $cache['cache'];
        }
        else  
        {
          $themenu = $this->BuildTheTree();
        }
      }  
      else
      {
        $themenu = $this->BuildTheTree();
      }
    }
    else
    {   
      $themenu = $this->BuildTheTree();
    }
  
    $this->theTree = $themenu;
  }
  
  function SaveATree($tree)
  {
    $tree_id = $this->TreeID();
    if ($this->defaultOptions['UseCache'] == true)
    {
      $cache['cache'] = $tree;      
      $cache['version'] = $this->cacheVersion;
      $option = $this->wdCache.$tree_id;
      
      if (get_option($option, false)) 
      {
        update_option($option, $cache);
      } 
      else 
      {
        add_option($option, $cache, ' ', 'no');
      }
    }  
  }
  function DeleteATree()
  {
    $tree_id = $this->TreeID();
    $option = $this->wdCache.$tree_id;
    delete_option($option);
  }
 
  
  function BuildTheTree()
  {  
    global $wpdb, $wp_query, $post;
	  $result = array();
    
    $args = array(
      'type'                     => 'post',
      'hide_empty'               => 0,
      'taxonomy'                 => 'category'
    );            

	  $categorylist = get_categories($args);
	
    foreach ($categorylist as $item)
    {
      $count = $this->GetCount($item->term_id);
      $countu = $count[1];
      $counta = $count[0];
	  
	    $args = array(
        'type'                     => 'post',
        'hide_empty'               => 0,
        'taxonomy'                 => 'category',
        'parent'                   => $item->term_id
      );
    
      $subcategories = get_categories($args);

      if (count($subcategories) > 0)
        $haschildren = '1';
      else
        $haschildren = '0';
      
      if ($this->defaultOptions['DontUseCategoryDescription'] == false)
        $description = $item->description;
      else 
        $description = '';       
      
	    $linkurl = get_category_link($item->term_id);	
	    if (has_filter('focal_cat_link_url'))
	    {
		    $linkurl = apply_filters('focal_cat_link_url', $linkurl);
	    }

      $result[] = $item->term_id . '#|#' . $item->cat_name . '#|#' . $description . '#|#' . $item->parent . '#|#' . $counta . '#|#' . $countu . '#|#' . $item->slug . '#|#' . $linkurl . '#|#' . $haschildren . '#|#' . $item->term_order; 
    }          
                     
    $this->SaveATree($result); 
    return $result;  
  }


  /****** Build & Render ******/
  /* Draw the actual folding category list */
  function DrawTheTree()
  {
    $this->theTreeParental = null;
    $this->currentCategory = $this->GetCurrentCategory();
    $this->BuildParentalList();
    $this->GetATree();

    if ($this->widgetOptions['RootCategory'] == -1)
    {
      if (is_category())
      {
        global $wp_query;
        $this->widgetOptions['RootCategory'] = $wp_query->get_queried_object_id();
      }
      else if (is_single())
      {
        $this->widgetOptions['RootCategory'] = $this->currentCategory;
      }
    } 
           
    /* sort if required */ 
    if (($this->widgetOptions['OrderBy'] != 'name') || ($this->widgetOptions['OrderByDirection'] != 'asc'))
    {
      switch ($this->widgetOptions['OrderBy'])
      {
        case 'name': $sortby = 1; break; 
        case 'id': $sortby = 0; break; 
        case 'description': $sortby = 2; break; 
        case 'slug': $sortby = 6; break; 
        case 'titlelength': $sortby = 7; break; 
        case 'mycategoryorder': $sortby = 9; break; 
        case 'postcount': 
          if ($this->widgetOptions['ShowPostCountUnique'])
            $sortby = 5; 
          else
            $sortby = 4; 
          break; 
        default: $sortby = 1;
      } 

      if (is_array($this->theTree))
      {
        foreach ($this->theTree as $menuitem)
          $itemsexpanded[] = explode('#|#', $menuitem);  
  
        $itemsexpanded = $this->QuicksortTheTree($itemsexpanded, $sortby, 0, $this->widgetOptions['OrderByDirection']);
      
        foreach ($itemsexpanded as $menuitem)
          $newtree[] = implode('#|#', $menuitem);
          
        $this->theTree = $newtree;
      }  
    }
    
    $tree_id = $this->TreeID();
	  if ($this->debug) echo "<!-- FoCal Debug: Pre Output Tree -->";
    if ($this->debug) echo "<!-- FoCal Debug: Root Category = ".$this->widgetOptions['RootCategory']." -->";

    return $this->OutputTheTree($tree_id,$this->widgetOptions['RootCategory'],0);
  }
  
  function OutputTheTree($tree_id, $parent, $level)
  {  
    $haveDoneItems = false;
    if ($level == 0)
    {
      if ($this->widgetOptions['Superfish'])
        $extraClass = ' sf-menu';
	  else if ($this->widgetOptions['Accordian'])
	    $extraClass = ' menu';
      else
        $extraClass = '';
      
      $result = '<!-- Folding Category List (FoCaL) ' . $this->focalVersion . ' for Wordpress by Tim Trott --><ul class="FoldingCategoryList'.$extraClass.' nodeLevel0" id="focalist_'.$this->setting_number.'">';
      
      foreach($this->itemsBefore as $newItem)
		$result .= '<li>' . $newItem . '</li>';
	  
	  $result .= $this->DoExtraLinks($this->widgetOptions['LinksBefore']);
    }
    else
    {
	  if ($this->widgetOptions['Accordian'])
	    $extraClass = ' acitem';
      else
        $extraClass = '';
		
	  $result = '<ul class="nodeLevel'.$level.$extraClass.'">';
    }	

    if (is_array($this->theTree))
    {  
      foreach ($this->theTree as $element) 
      {
		list($term_id, $Ename, $Edescription, $catparent, $Etotalcount, $Euniquecount, $slug, $url, $haschildren) = explode('#|#', $element);
        
        // Dont show item or children if excluded
        if (!is_array($this->widgetOptions['ExcludeCats']))
          $this->widgetOptions['ExcludeCats'] = array();
        
        $processThis = true;
        
        if ($this->widgetOptions['IncOrEx'] == 'ex')
          $processThis = !in_array($term_id, $this->widgetOptions['ExcludeCats']);
        else
          $processThis = in_array($term_id, $this->widgetOptions['ExcludeCats']);
        
        if ($processThis)
        {
          $itemClass = array();
          
          if ($parent == $catparent)
          {  
			// Sort out ML title & descriptions
			if ($this->widgetOptions['MultiLingual'])
            {
			  if ($this->widgetOptions['UseDescriptionAsCategoryText'] == true)
              {
  			    $title = strip_tags(category_description($term_id));
				$description = strip_tags(category_description($term_id));
				$linktitle = ' title="' . $description . '"';
			  }
              else
              {
  			    $title = strip_tags(get_cat_name($term_id));
				$description = strip_tags(category_description($term_id));
				$linktitle = '';
			  }
            
			  if ($title == '')
                $title = strip_tags(get_cat_name($term_id));
			}
			else
            {
			  if ($this->widgetOptions['UseDescriptionAsCategoryText'] == true)
              {
  			    $title = strip_tags($Edescription);
				$description = strip_tags($Edescription);
				$linktitle = ' title="' . $description . '"';
			  }
              else
              {
 			    $title = strip_tags($Ename);
				$description = strip_tags($Edescription);
				$linktitle = '';
			  }
            
			  if ($title == '')
                $title = strip_tags($Ename);
            }
				
            // Show post count after text
            if ($this->widgetOptions['ShowPostCount'] == true)
            {
              if ($this->widgetOptions['ShowPostCountUnique'] == true)
                $count = $Euniquecount;
              else
                $count = $Etotalcount;
        
              $linkcounter = $this->widgetOptions['CountBefore'] . $count . $this->widgetOptions['CountAfter'];
            } 
            else 
            {
              $linkcounter = '';
            }
            
            // use cat id as cssid
            if ($this->widgetOptions['OutputCatIDs'] == true)
               $li_postid = ' id="cat-' . $term_id . '"';
            else 
               $li_postid = '';
            
            
            // Select Current Item
            if (in_array($term_id,$this->currentCategory))
            {
			  if ($this->widgetOptions['Accordian'])
			  {
                $itemClass[] = 'active';
                $linkSelected = ' class="focalLinkSelected active"';
			  }
			  else
			  {
                $itemClass[] = 'selected';
                $linkSelected = ' class="focalLinkSelected"';
			  }
            }
            else
              $linkSelected = '';

            // Does this item have child categories?
            if ($haschildren)
              $itemClass[] = 'haschildren';
            else 
              $itemClass[] = 'nochildren';

			// does the category have child categories, or do we show empty cats
            if (($Etotalcount > 0) || ($this->widgetOptions['ShowEmptyCats'] == true))
            {
              // Add rel="nofollow" to all links if enabled  
              if ($this->widgetOptions['NoFollowAllLinks'])
                  $rel = ' rel="nofollow"';
			  else
                $rel = '';

              // Is this item an ancestor of a selected item?
              if ((!in_array('selected', $itemClass)) && (is_array($this->theTreeParental)))
              {
                if (in_array($slug, $this->theTreeParental))  
                {
  				  $itemClass[] = 'selectedparent';
				  if ($this->widgetOptions['Accordian'])
			      {
				    $itemClass[] = 'active';
				    $itemClass[] = 'expand';
				  }
				}
              } 
              
              if (($this->currentCategory != null) && ($this->widgetOptions['NoFollowParents']))
              {
                if (!in_array($catparent,$this->currentCategory))
                  $rel = ' rel="nofollow"';
              }
              
			  
              // Output the list item
              $result .= '<li ';
              if ($this->widgetOptions['ApplyCSSTo'] == 'li')
              {
                if (count($itemClass)>0)
                {
                  $result .= 'class="';
                  foreach ($itemClass as $attribute)
                    $result .= ' '. $attribute;
                  $result .= '"';
                }
              }
              $result .= $li_postid . '>';
              
			  
              if ($this->widgetOptions['ApplyCSSTo'] == 'span')
              {
                $result .= '<span ';
                if (count($itemClass)>0)
                {
                  $result .= 'class="';
                  foreach ($itemClass as $attribute)
                    $result .= ' '. $attribute;
                  $result .= '"';
                }
                $result .= '>';
              }
              
              if ($this->widgetOptions['MultiLingual'])
              {
                $title = get_cat_name($term_id);
                $url = get_category_link($term_id);
              }
              
              if (has_filter('focal_cattitle'))
              {
                $title = apply_filters('focal_cattitle', $title);
              }

              if ((in_array($term_id,$this->currentCategory))  && ($this->widgetOptions['DontLinkCurrentCategory'] == true))
              { 
                $result .= $title;
              }
              else
              {
                if (($this->widgetOptions['Accordian']) && ($haschildren))
				{
				  $url = "#";
				}
				
				$result .= '<a ' . $rel . ' href="' . $url . '"' . $linktitle;
                if ($this->widgetOptions['ApplyCSSTo'] == 'a')
                {
                  if (count($itemClass)>0)
                  {
					if ($linkSelected)
					{
						$linkSelected = str_replace('class="', '', $linkSelected);
						$linkSelected = str_replace('"', '', $linkSelected);
					}
					
					$result .= ' class="'.$linkSelected;
                    foreach ($itemClass as $attribute)
                      $result .= ' '. $attribute;
                    $result .= '"';
                  }
                }
				else
				{
				  $result .= ' ' . $linkSelected . ' ';
				}
                
                $result .= '>' . $title . '</a>';
              }
              
              $result .= $linkcounter;
              
              if ($this->widgetOptions['ApplyCSSTo'] == 'span')
              {
                $result .= '</span>';
              }
              
              if (is_array($this->theTreeParental))
              {
				if ((in_array($slug, $this->theTreeParental)) || ($this->widgetOptions['ExpandAllCats'] == true))  
                  $result .= $this->OutputTheTree($tree_id, $term_id, $level+1);
              }
			  else
			  {
				if ($this->debug) echo "<!-- FoCal Debug: theTreeParental not array -->";
			  }

              $result .= '</li>';
              $haveDoneItems = true;
            }
          }
        }
		else
		{
			if ($this->debug) echo "<!-- FoCal Debug: Processing term id:$term_id aborted -->";
		}
      }
    }
	else
	{
		if ($this->debug) echo '<!-- FoCal Debug: $this->theTree not an array! -->';
	}
	
    if ($level == 0)
    {
      $result .= $this->DoExtraLinks($this->widgetOptions['LinksAfter']);

      foreach($this->itemsAfter as $newItem)
	    $result .= '<li>'.$newItem.'</li>';
	}
		
    $result .= '</ul>';

    if ($level == 0)
    {
	  $result = str_replace('&amp;amp;','&amp;',$result); 
      $result = str_replace('class=" ','class="',$result); 
	}
	
    if ($haveDoneItems)
      return $result;
    else 
      return '';   
  } 
 
  function str_lreplace($search, $replace, $subject)
  {
    $pos = strrpos($subject, $search);

    if($pos === false)
        return $subject;
    else
        return substr_replace($subject, $replace, $pos, strlen($search));
  }

  function DoExtraLinks($links)
  {
    $result = '';
    if ($links != '')
    {
      $links = explode("\n", $links);
      $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
      
      // 0          1               2                 3           4          5
      // link title|http://link url|popup description|text before|text after|rel=nofollow
      
      $result = '';
      if (count($links) > 0)
      {
        foreach($links as $linke)
        {
          $link = explode('|', $linke);
          if (strtolower($link[1]) == strtolower($url))
            $css = ' class="selected"';
          else
            $css = '';
              		  
          $result .= '<li'.$css.'>';
            
          if ($link[3] != '')
            $result .= $link[3];
  
          $result .= '<a href="' . $link[1] . '"'.$css;
          
          if ($link[2] != '')
            $result .= ' title="' . $link[2] . '"';
          
          if ($link[5] == '1')
            $result .= ' rel="nofollow"';

          $result .= '>' . $link[0] . '</a>';
            
          if ($link[4] != '')
            $result .= $link[4];
           
           
          $result .= '</li>';
        }
      }
    }
    return $result;
  }

  function BuildParentalList()
  {
    if ($this->theTreeParental == null)
    {
      $parentlist = '';
      foreach ($this->currentCategory as $cat)
      {
        $par = get_category_parents($cat,FALSE,'|',TRUE);
  
        if (!is_a($par,'WP_Error'))
          $parentlist .= $par;
      }
      $this->theTreeParental = explode('|',$parentlist);
    }  
  }

  function GetCurrentCategory()
  {
    global $wp_query,$post;
  
    $curcat = array();
    $found = false;
    
    if (is_page()) 
    {
        $curcat[0] = get_post_meta($wp_query->post->ID, $this->pageCatCF, true);
        $found = true;
  	}
    else if (is_category()) 
    {
  		$curcat[0] = $wp_query->get_queried_object_id();
        $found = true;
  	}                          
    else if (is_single())
    {
      $postCat = get_post_meta($wp_query->post->ID, $this->pageCatCF, true);
      if ($postCat)
      {
        $curcat[0] = $postCat;
        $found = true;
      }
      
      if (!$found)
      {
        $cats = get_the_category($post->ID);
    
        // Only one, so this is easy...
        // A post is always in a category, even if its uncategorized.
        if (count($cats) == 1)
        {
          $curcat[0] = $cats[0]->term_id;
          $found = true;
        }
        else if ($this->widgetOptions['ExpandAllMember'] == true)
        {
          // Expand all categories, this is also easy...
          foreach ($cats as $cat)
          {
            $curcat[] = $cat->term_id;
            $found = true;
          }
        }
        else
        {
          $priority = explode(',',$this->widgetOptions['HightlightPriority']);
          if (count($priority) > 0)
          { 
            // Use the priority list, selecting if we find a match.
            foreach ($priority as $p)
            {
              if (!$found)
              {
                foreach ($cats as $cat)
                {
                  if (!$found)
                  {
                    if ($cat->term_id == $p)
                    {
                      $curcat[0] = $p;
                      $found = true;
                    }
                    else
                    {
                      $parents = $this->GetCatParents($cat->term_id);
                      if (in_array($p, $parents))
                      {
                        $curcat[0] = $cat->term_id;
                        $found = true;
                      }
                    }
                  }
                }
              }
            }
          }
          else
          {
            // not priority list
            if ($this->widgetOptions['RootCategory'] > 0)
            {
              // Parent is not zero, so try and locate a category under parent
              foreach ($cats as $cat)
              {
                if (!$found)
                {
                  if ($cat->term_id == $this->widgetOptions['RootCategory'])
                  {
                    $curcat[0] = $cat->term_id; 
                    $found = true;              
                  }
                  else
                  {
                    $parents = $this->GetCatParents($cat->term_id);
                    if (in_array($this->widgetOptions['RootCategory'], $parents))
                    {
                      $curcat[0] = $cat->term_id; 
                      $found = true;              
                    }
                  }
                }
              }
            }
            else
            {
              // Select lowest ID from Wordpress
              $curcat[0] = $cats[0]->term_id; 
              $found = true;
            }
          }
        }
      }
      
      if (!$found)
      {
        // Error?
        $curcat[0] = $cats[0]->term_id;
      }
    }  
	
	if ($this->debug) echo "<!-- FoCal Debug: Current Cat = $curcat -->";
    return $curcat;	  
  } 

  function GetParental()
  {
    if ($this->categoryFamilyTree == null)
    {
      global $wpdb, $wp_query;
      $query = 'SELECT * FROM `'.$wpdb->prefix.'term_taxonomy` JOIN `'.$wpdb->prefix.'terms` ON '.$wpdb->prefix.'terms.term_id = '.$wpdb->prefix.'term_taxonomy.term_id WHERE '.$wpdb->prefix.'term_taxonomy.taxonomy = "category"';
      $query = $wpdb->prepare($query, null);
      $categorylist = $wpdb->get_results($query, ARRAY_A);
      foreach ($categorylist as $cat)
        $parental[$cat['term_id']] = $cat['parent'];
        
      $this->categoryFamilyTree = $parental;
    }
  }	
  	
  function GetCatParents($cat)
  {
    if ($this->categoryFamilyTree == null)
      $this->GetParental();

    $result = array();
    
    if ($cat > 0)
    {
      do
      {
        $cat = $this->categoryFamilyTree[$cat];
        $result[] = $cat; 
      }
      while (($cat > 0) && (count($result)<1000));
    }  
    return $result;   
  }



  function GetCount($category) 
  {
  	global $wpdb;
  	$post_count = 0;
  
    $children = get_categories('pad_counts=false&child_of=' . $category);
    $children[] = get_category($category);
    $subcatposts = array();
    $date = current_time('mysql');
	
  	foreach($children as $cat) 
  	{
  	  $querystr = "
  			SELECT DISTINCT $wpdb->posts.ID
  			FROM $wpdb->term_taxonomy, $wpdb->posts, $wpdb->term_relationships
  			WHERE $wpdb->posts.ID = $wpdb->term_relationships.object_id
  			AND $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
  			AND $wpdb->term_taxonomy.term_id = $cat->term_id
  			AND $wpdb->posts.post_status = 'publish'
  			AND $wpdb->posts.post_type = 'post'
  			AND $wpdb->posts.post_date < '$date'
  		";
  	  $querystr = $wpdb->prepare($querystr, null); 
      $result = $wpdb->get_results($querystr, ARRAY_A);
  			
      if($result)
        foreach ($result as $id)
          $subcatposts[] = $id['ID'];
    } 
  
    $subcatpostsu = array_unique($subcatposts);    
    return array(count($subcatposts),count($subcatpostsu));
  }
  
  function QuicksortTheTree($q = array(), $sortUsBy, $level = 0, $orderBy='asc')
  {
  	$greater = array();
  	$less = array();
  	$pivotList = array();
  	
    if (count($q) <= 1)
  		return $q;
  		
  	$pivot = $q[0];
  	foreach ($q as $treeitem) 
    {
  		$value = '';
  		$compareValue = '';
    	
		if ($sortUsBy == 7)
		{
		  $value = strlen($treeitem[1]);
		  $compareValue = strlen($pivot[1]);
		}
		else
		{
		  $value = $treeitem[$sortUsBy];
  		  $compareValue = $pivot[$sortUsBy];
		}
  
  		if ($value <$compareValue) $less[] = $treeitem;
  		if ($value == $compareValue) $pivotList[] = $treeitem;
  		if ($value> $compareValue) $greater[] = $treeitem;
  	}
  
  	$return = array_merge($this->QuicksortTheTree($less,$sortUsBy, $level+1), $pivotList, $this->QuicksortTheTree($greater,$sortUsBy, $level+1));
  	
    if ($level == 0) 
    {
  	  if($orderBy == 'desc')  
        $return = array_reverse($return);
    }
    return $return;
  }

  /****** Config ******/
  /**** Process any options submitted to a form ****/
  function ProcessPostSubmit()
  {
    if(!empty($_POST['focalRebuildCache']))
    {
      $this->RebuildCache();
    }
    else if(!empty($_POST['focalDeleteCache']))
    {
      $this->DeleteCache();
    }
    else if(!empty($_POST['focalUninstall']))
    {
      $this->Uninstall();
    }
    else if(!empty($_POST['focalSubmitOptions']))
    {
      $selectedWidget = $_POST['focalNumber'];     
	  $this->UpdateOptions($selectedWidget);
    } 
    else if(!empty($_POST['focalDeleteSetting']))
    {
      $selectedWidget = $_POST['focalNumber'];
      $this->RemoveWidgetOptions($selectedWidget); 
    } 
	  else if (!empty($_POST['focalNewDefinitionSubmit']))
	  {
	    if (!empty($_POST['focalNewDefinition']))
	    {
	      $newInstance = $_POST['focalNewDefinition'];
        $newInstance = strip_tags($newInstance);
		    $newInstance = preg_replace('/[^a-zA-Z0-9\s]/', '', $newInstance);
		    $newInstance = str_replace(' ', '', $newInstance);

		    // Load options into new instance
		    $this->GetOptions($newInstance);
		
		    // Save instance so it can be modified later
		    $this->UpdateOptions($newInstance);
	    }
	  }
  }
  
  function RebuildCache()
  {
  	$this->DeleteATree();
    $this->BuildTheTree();
    $this->hasAdminMessage = true;
    $this->adminMessage = __('Cache Rebuilt', 'focal');
  } 
  function DeleteCache()
  {
    $this->DeleteATree();
    $this->hasAdminMessage = true;
    $this->adminMessage = __('Cache Deleted', 'focal');
  }
  function Uninstall()
  {
    $this->DeleteATree();
    delete_option($this->dbOptions);
    delete_option($this->wdOptions);
    
	global $wpdb;
	$querystr = 'SELECT option_name FROM '.$wpdb->prefix.'options WHERE option_name LIKE "'.$this->wdOptions.'%"';
    $result = $wpdb->get_results($querystr, ARRAY_A);
	if ($result)
    {
      foreach($result as $widgets)
      {
	    delete_option($widgets['option_name']);
	  }
	}
	
    $this->hasAdminMessage = true;
    $this->adminMessage = __('Plugin options deleted from database. You can now deactivate the plugin.', 'focal');
  }  
  function UpdateOptions($widget)
  {
    if (empty($widget))
      $selectedWidget = $_POST['focal_selectedWidget'];
	else
	  $selectedWidget = $widget;
	
	if (trim($selectedWidget) == '')
		$selectedWidget = 'default';
	
	if ($selectedWidget)
    {
      if (is_numeric($_POST['focal_widgetParent']))
        $this->widgetOptions['RootCategory'] = $_POST['focal_widgetParent'];
      else if ($_POST['focal_widgetParent'] == 'showAll')
        $this->widgetOptions['RootCategory'] = 0;
      else if ($_POST['focal_widgetParent'] == 'showChild')
        $this->widgetOptions['RootCategory'] = -1;
      
      if (is_array($_POST['post_category']))
      {
        $this->widgetOptions['ExcludeCats'] = $_POST['post_category'];
      }
      else
      {
        if (isset($_POST['focal_widgetExcluded']))
          $this->widgetOptions['ExcludeCats'] = explode(',', $_POST['focal_widgetExcluded']);
        else 
          $this->widgetOptions['ExcludeCats'] = array();
      }
      
      $this->widgetOptions['HightlightPriority'] = str_replace(' ', '', $_POST['focal_widegtPriority']);

      if ($_POST['focal_widgetShowMethod'] == 'normal')
      {
        $this->widgetOptions['ExpandAllCats'] = false;
        $this->widgetOptions['ExpandAllMember'] = false;
      }
      else if ($_POST['focal_widgetShowMethod'] == 'members')
      {
        $this->widgetOptions['ExpandAllCats'] = false;
        $this->widgetOptions['ExpandAllMember'] = true;
      }
      else if ($_POST['focal_widgetShowMethod'] == 'all')
      {
        $this->widgetOptions['ExpandAllCats'] = true;
        $this->widgetOptions['ExpandAllMember'] = false;
      }
      
      if ($_POST['focal_applyCSSTo'] == 'li')
        $this->widgetOptions['ApplyCSSTo'] = 'li';
      else if ($_POST['focal_applyCSSTo'] == 'a')
        $this->widgetOptions['ApplyCSSTo'] = 'a';
      else if ($_POST['focal_applyCSSTo'] == 'span')
        $this->widgetOptions['ApplyCSSTo'] = 'span';

      if ($_POST['focal_widgetSortBy'] == 'name')
        $this->widgetOptions['OrderBy'] = 'name';
      else if ($_POST['focal_widgetSortBy'] == 'id')
        $this->widgetOptions['OrderBy'] = 'id';
      else if ($_POST['focal_widgetSortBy'] == 'description')
        $this->widgetOptions['OrderBy'] = 'description';
      else if ($_POST['focal_widgetSortBy'] == 'slug')
        $this->widgetOptions['OrderBy'] = 'slug';
      else if ($_POST['focal_widgetSortBy'] == 'postcount')
        $this->widgetOptions['OrderBy'] = 'postcount';
      else if ($_POST['focal_widgetSortBy'] == 'mycategoryorder')
        $this->widgetOptions['OrderBy'] = 'mycategoryorder';
      else if ($_POST['focal_widgetSortBy'] == 'titlelength')
        $this->widgetOptions['OrderBy'] = 'titlelength';

		if ($_POST['focal_widgetSortBySeq'] == 'asc')
        $this->widgetOptions['OrderByDirection'] = 'asc';
      else if ($_POST['focal_widgetSortBySeq'] == 'desc')
        $this->widgetOptions['OrderByDirection'] = 'desc';

      if ($_POST['focal_IncExCategories'] == 'ex')
        $this->widgetOptions['IncOrEx'] = 'ex';
      else 
        $this->widgetOptions['IncOrEx'] = 'inc';
        
      $this->widgetOptions['UseDescriptionAsCategoryText'] = ($_POST['focal_UseDescriptionAsCategoryText'] == 'on');
      $this->widgetOptions['UseDescriptionAsCategoryTitle'] = ($_POST['focal_UseDescriptionAsCategoryTitle'] == 'on');
      $this->widgetOptions['LinksBefore'] = $_POST['focal_widgetLinksBefore']; 
      $this->widgetOptions['LinksAfter'] = $_POST['focal_widgetLinksAfter']; 
      $this->widgetOptions['ShowPostCount'] = ($_POST['focal_ShowPostCount'] == 'on');
      $this->widgetOptions['ShowPostCountUnique'] = ($_POST['focal_ShowPostCountUnique'] == 'on');
      $this->widgetOptions['OutputCatIDs'] = ($_POST['focal_OutputCatIDs'] == 'on');   
      $this->widgetOptions['ShowEmptyCats'] = ($_POST['focal_ShowEmptyCats'] == 'on');
      $this->widgetOptions['NoFollowAllLinks'] = ($_POST['focal_NoFollowAllLinks'] == 'on');
      $this->widgetOptions['NoFollowParents'] = ($_POST['focal_NoFollowParents'] == 'on');
      $this->widgetOptions['DontLinkCurrentCategory'] = ($_POST['focal_DontLinkCurrentCategory'] == 'on');
      $this->widgetOptions['MultiLingual'] = ($_POST['focal_MultiLingual'] == 'on');
      $this->widgetOptions['CountBefore'] = $_POST['focal_widegtCountBefore'];
      $this->widgetOptions['CountAfter'] = $_POST['focal_widegtCountAfter'];
      $this->widgetOptions['Superfish'] = $_POST['focal_Superfish'];
      $this->widgetOptions['Accordian'] = $_POST['focal_Accordian'];

      if ($this->widgetOptions['Superfish'] || $this->widgetOptions['Accordian'])
      {
        $this->widgetOptions['ExpandAllCats'] = true;
        $this->widgetOptions['ExpandAllMember'] = false;      
      }

      $this->defaultOptions['AutoBuildCache'] = ($_POST['focal_AutoRebuildCache'] == 'on');
      $this->defaultOptions['UseCache'] = ($_POST['focal_UseCache'] == 'on');
      $this->defaultOptions['DontUseCategoryDescription'] = !($_POST['focal_UseDescriptions'] == 'on');

	  $this->defaultOptions['IncludeSuperfishJS'] = ($_POST['focal_SuperfishJS'] == 'on');
      $this->defaultOptions['IncludeSuperfishCSS'] = ($_POST['focal_SuperfishCSS'] == 'on');
      $this->defaultOptions['IncludeAccordianJS'] = ($_POST['focal_AccordianJS'] == 'on');
      $this->defaultOptions['IncludeAccordianCSS'] = ($_POST['focal_AccordianCSS'] == 'on');
      $this->SetGlobalOptions();
	  
      $this->SetWidgetOptions($selectedWidget);
      $this->GetOptions($selectedWidget);                                       
      $this->hasAdminMessage = true;
      $this->adminMessage = __('Settings updated.', 'focal');
    }
  } 
  
  
  function widget_GetSettingDefinitions($selectedWidget)
  {
    global $wpdb;
    
	  // TODO - Escape this properly. $wpdb->prepare does not like LIKE % atm
    $querystr = 'SELECT option_name FROM '.$wpdb->prefix.'options WHERE option_name LIKE "'.$this->wdOptions.'%"';
    $result = $wpdb->get_results($querystr, ARRAY_A);
	
	$first = '';
	$found = false;
	  
	if ($result)
    {
      foreach($result as $widgets)
      {
		$name = $widgets['option_name'];
        $name = str_replace($this->wdOptions, '', $name);
		$name = strtolower($name);

		if (($name != null) && ($name != ''))
        {         
           if ($name == $selectedWidget)
           {
             $sel = 'selected="selected"';
             $found = true;
           }
           else
           {
             $sel = '';
           } 
          
           $options .= '<option value="' . $name . '" ' . $sel . '>' . $name . '</option>';
		   
		       if ($first == '')
		         $first = $name;
        }
      }
    }

	if (!$found)
	{
	  $first = 'default';
	  $options = '<option value="default" selected="selected">default</option>';
	}
	
	
	  if (($selectedWidget != '') && ($found))
        $first = $selectedWidget;
	  
	  return array($first, $options);
  }
  
  
  
  /* widget administration */
  function widget_FocalAdminPage() 
  {
    $this->ProcessPostSubmit();
     
    if(!empty($_POST['focalChangeWidget']))
      $selectedWidget = $_POST['focalNumber'];
    else
      $selectedWidget = $_POST['focal_selectedWidget'];

	if ($selectedWidget == null)
	  $selectedWidget = 'default';
        
	$definitions = $this->widget_GetSettingDefinitions($selectedWidget);
    $selectedWidget = $definitions[0];
	$options = $definitions[1];
    $this->GetOptions($selectedWidget); 
?>
    <div><div class="wrap">
      <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <h2><?php _e('Folding Category List: Options', 'focal'); ?></h2>
        <p><?php _e('The Folding Category List provides a fast and efficient replacement for the category list which allows categories to be folded, or collapsed, allowing more room on your sidebar. These options allow you to customise each widget independently.', 'focal'); ?></p> 
<?php if ($this->hasAdminMessage) { ?>        
        <div id="message" class="updated fade"><p><?php echo $this->adminMessage; ?></p></div>
<?php } ?>        
        <h3><?php _e('Global Settings', 'focal'); ?></h3>
      	
        <table class="form-table">
      		<tr>
      			<th scope="row" valign="top"><?php _e('Enable Cache', 'focal'); ?></th>
      			<td>
      				<input type="checkbox" id="focal_UseCache" name="focal_UseCache" <?php echo ($this->defaultOptions['UseCache']) ? "checked" : '' ;?>/><br />
      				<span><?php _e('This widget will build a category list from all the categories and store it in the database. All future requests use the cached version.', 'focal'); ?></span><br/>
      				<input type="submit" name="focalRebuildCache" value="<?php _e('Rebuild Cache', 'focal'); ?>" class="button"/> &nbsp; <input type="submit" name="focalDeleteCache" value="<?php _e('Delete Cache', 'focal'); ?>" class="button"/> &nbsp; <input type="submit" name="focalUninstall" value="<?php _e('Uninstall/Reset', 'focal'); ?>" class="button"/>
      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('Auto Rebuild Cache', 'focal'); ?></th>
      			<td>
      				<input type="checkbox" id="focal_AutoRebuildCache" name="focal_AutoRebuildCache" <?php echo ($this->defaultOptions['AutoBuildCache']) ? "checked" : '' ;?>/><br />
      				<span><?php _e('Rebuild the cache when posting, deleting or modifying posts. Can slow down posting, so you may wish to disable if posting multiple entries. Remember to rebuild cache when done.', 'focal'); ?></span>
      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('Use Descriptions', 'focal'); ?></th>
      			<td>
      				<input type="checkbox" id="focal_UseDescriptions" name="focal_UseDescriptions" <?php echo ($this->defaultOptions['DontUseCategoryDescription']) ? '' : "checked" ;?>/><br />
      				<span><?php _e('By default category descriptions are used for the title attribute on links, but if you have long descriptions or hundreds of categories this can lead to a large database. If so, disable this option and category descriptions will be removed. Changing this value will cause the cache to be rebuilt, if it\'s enabled.'); ?></span>
      			</td>
      		</tr>
        </table> 
        
        <br/>
        <span style="float:right"><input type="submit" name="focalSubmitOptions" value="<?php _e('Save Changes', 'focal'); ?>" class="button"/></span>
        <div style="clear:both"></div>
        <br/>
        <h3><?php _e('Individual Widgets', 'focal'); ?></h3> 
        
        <table class="form-table">
      		<tr>
      		  <th scope="row" valign="top"><?php _e('Setting Definitions', 'focal'); ?></th>
      		  <td>
                <select id="focalNumber" name="focalNumber"><?php echo $options; ?></select> &nbsp; <input type="submit" name="focalChangeWidget" value="<?php _e('Load Definition', 'focal'); ?>" class="button"/>
                &nbsp; <input type="submit" name="focalDeleteSetting" value="<?php _e('Delete Definition', 'focal'); ?>" class="button"/>
                &nbsp; <input type="text" name="focalNewDefinition" value=''/> &nbsp; <input type="submit" name="focalNewDefinitionSubmit" value="<?php _e('New Definition', 'focal'); ?>" class="button"/>
				<br/>
     		    <input type="hidden" name="focal_selectedWidget" value="<?php echo $selectedWidget; ?>"/>
      		    <span><?php _e('Select a definition from the list. Load or delete it, or enter a name and create a new definition.', 'focal'); ?>.</span>   				
      		  </td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('Root Category', 'focal'); ?></th>
      			<td>
<?php
      if ($this->widgetOptions['RootCategory'] > 0)
      {
        $list = wp_dropdown_categories('hierarchical=1&echo=0&name=focal_widgetParent&selected=' . $this->widgetOptions['RootCategory']);
        $pos = strpos($list, '<option');
        $list = substr($list,0,$pos) . '<option value="showAll">' . __('Show all categories', 'focal') . '</option><option value="showChild">' . __('Show child categories only', 'focal') . '</option>' . substr($list,$pos);
      }
      else if ($this->widgetOptions['RootCategory'] == 0)
      {
        $list = wp_dropdown_categories('hierarchical=1&echo=0&name=focal_widgetParent');
        $pos = strpos($list, '<option');
        $list = substr($list,0,$pos) . '<option selected value="showAll">' . __('Show all categories', 'focal') . '</option><option value="showChild">' . __('Show child categories only', 'focal') . '</option>' . substr($list,$pos);
      }
      else if ($this->widgetOptions['RootCategory'] == -1)
      {
        $list = wp_dropdown_categories('hierarchical=1&echo=0&name=focal_widgetParent');
        $pos = strpos($list, '<option');
        $list = substr($list,0,$pos) . '<option value="showAll">' . __('Show all categories', 'focal') . '</option><option selected value="showChild">' . __('Show child categories only', 'focal') . '</option>' . substr($list,$pos);
      }
      
      echo $list;
?><br/>
      				<span><?php _e('Select the root (parent) category to show, show all categories (default), or only show child categories of the current category.', 'focal'); ?></span>
      			</td>
      		</tr>
      		<tr>
      		  <th scope="row" valign="top"><?php _e('Categories to Show', 'focal'); ?></th>
      			<td>
                <label><input type='radio' name='focal_IncExCategories' value='ex' <?php if ($this->widgetOptions['IncOrEx'] == 'ex') echo ' checked' ;?>> <span class="setting-description"><?php _e('The categories ticked below will be <strong>Excluded</strong> from FoCaL.', 'focal'); ?></span></label><br />
                <label><input type='radio' name='focal_IncExCategories' value='inc'<?php if ($this->widgetOptions['IncOrEx'] == 'inc') echo ' checked' ;?>> <span class="setting-description"><?php _e('Only the categories ticked below will be <strong>Included</strong> in FoCaL.', 'focal'); ?></span></label><br /><br />
                <?php 
                  if (function_exists('wp_category_checklist'))
                  { ?>
              <div id="categorydiv" style="height:170px; width:220px; overflow:auto; background-color:white;padding:8px;border:1px solid #dfdfdf;"><ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
              <?php wp_category_checklist(0,0,$this->widgetOptions['ExcludeCats']); ?>
              </ul></div>
               <?php }
                  else
                  {
?>
              <input type="text" name="focal_widgetExcluded" size="20" value="<?php echo implode(",",$this->widgetOptions['ExcludeCats']) ?>"/><br />
      				<span><?php _e('Comma seperated list for category ID to exclude from the list. Child categories will not be shown either.', 'focal'); ?></span>

<?php                  }               
                  ?>
      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('Category Expansion', 'focal'); ?></th>
      			<td>
                <?php if ($this->widgetOptions['ExpandAllCats'])
                      {
                        $normal = '';
                        $member = '';
                        $all = 'selected';
                        $value = 'all';
                      }
                      else if ($this->widgetOptions['ExpandAllMember'])
                      {
                        $normal = '';
                        $member = 'selected';
                        $all = '';
                        $value = 'members';
                      }
                      else
                      {
                        $normal = 'selected';
                        $member = '';
                        $all = '';
                        $value = 'normal';
                      }?>
              <select name="focal_widgetShowMethod" id="focal_widgetShowMethod" value="<?php echo $value;?>">
                <option value="normal" <?php echo $normal ?>><?php _e('Normal (default)', 'focal'); ?></option>
                <option value="members" <?php echo $member ?>><?php _e('Expand all post categories', 'focal'); ?></option>
                <option value="all" <?php echo $all ?>><?php _e('Expand all', 'focal'); ?></option>
              </select>
              <br />
      				<span><?php _e('This option defines how the Folding Category List will expand. Setting this option to \'Normal\' will only expand the current category (or the category with the highest priority). Setting to \'Expand all post categories\' will expand all categories that the current post is a member of. Setting to \'Expand All\' will show a fully expanded list with all categories shown.', 'focal'); ?></span>
      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('Category Priority', 'focal'); ?></th>
      			<td>
      				<input type="text" name="focal_widegtPriority" id="focal_widegtPriority" size="40" value="<?php echo $this->widgetOptions['HightlightPriority'] ?>"/><br />
      				<span><?php _e('This option is a comma separated list of category ID\'s that is used to determine which category to highlight in the event that a post is assigned to multiple categories. If a post category is a member of, or a child of, a category in this list then it will be expanded. If a category is not on the priority list then Wordpress will select the lowest category id. Category ID\'s can be found on the Manage Categories screen. &quot;I know this is a little janky, it\'ll be better in future releases.&quot;', 'focal'); ?></span><br/>
      				<span><?php _e('You can override this list by adding a custom field to a post or page called &quot;'.$this->pageCatCF.'&quot; containing the numerical id of the category to expand.', 'focal'); ?></span>
      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('Order By', 'focal'); ?></th>
      			<td><?php 
      			
if ($this->widgetOptions['OrderBy'] == 'name') { $name = ' selected'; $value= 'name'; }
if ($this->widgetOptions['OrderBy'] == 'id') { $id = ' selected'; $value= 'id'; }
if ($this->widgetOptions['OrderBy'] == 'description') { $description = ' selected'; $value= 'description'; }
if ($this->widgetOptions['OrderBy'] == 'slug') { $slug = ' selected'; $value= ''; }
if ($this->widgetOptions['OrderBy'] == 'postcount') { $postcount = ' selected'; $value= 'postcount'; }
if ($this->widgetOptions['OrderBy'] == 'mycategoryorder') { $mycategoryorder = ' selected'; $value= 'mycategoryorder'; }
if ($this->widgetOptions['OrderBy'] == 'titlelength') { $titlelength = ' selected'; $value= 'titlelength'; }
if ($this->widgetOptions['OrderByDirection'] == 'asc') { $asc = ' selected'; $valuea= 'asc'; }
if ($this->widgetOptions['OrderByDirection'] == 'desc') { $desc = ' selected'; $valuea= 'desc'; }

      			?>
      				<select name="focal_widgetSortBy" id="focal_widgetSortBy" value="<?php echo $value; ?>">
      				  <option<?php echo $name; ?> value="name"><?php _e('Category Name (default)', 'focal'); ?></option>
      				  <option<?php echo $titlelength; ?> value="titlelength"><?php _e('Category Name Length', 'focal'); ?></option>
      				  <option<?php echo $id; ?> value="id"><?php _e('Category ID', 'focal'); ?></option>
      				  <option<?php echo $description; ?> value="description"><?php _e('Category Description', 'focal'); ?></option>
      				  <option<?php echo $slug; ?> value="slug"><?php _e('Category Slug', 'focal'); ?></option>
      				  <option<?php echo $postcount; ?> value="postcount"><?php _e('Category Post Count', 'focal'); ?></option>
<?php if (function_exists("mycategoryorder_init")) { ?>      				  <option<?php echo $mycategoryorder; ?> value="mycategoryorder"><?php _e('My Category Order', 'focal'); ?></option><?php } ?>
              </select> &nbsp; <select name="focal_widgetSortBySeq" id="focal_widgetSortBySeq" value="<?php echo $valuea; ?>">
                <option<?php echo $asc; ?> value="asc"><?php _e('Ascending (default)', 'focal'); ?></option>
                <option<?php echo $desc; ?> value="desc"><?php _e('Descending', 'focal'); ?></option>
              </select><br/>                
      				<span><?php _e('How should the list be sorted?', 'focal'); ?> <?php if (function_exists("mycategoryorder_init")) { ?><em><?php _e('nb: if selecting My Category Order you must manually rebuild the cache after changing the order.', 'focal'); ?></em><?php } ?></span>
      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('Post Count', 'focal'); ?></th>
      			<td>
      				<input type="checkbox" id="focal_ShowPostCount" name="focal_ShowPostCount" <?php echo ($this->widgetOptions['ShowPostCount']) ? "checked" : '' ;?>/>&nbsp;<label for="focal_ShowPostCount"><?php _e('Show post count', 'focal'); ?></label><br />
      				<input type="checkbox" id="focal_ShowPostCountUnique" name="focal_ShowPostCountUnique" <?php echo ($this->widgetOptions['ShowPostCountUnique']) ? "checked" : '' ;?>/>&nbsp;<label for="focal_ShowPostCountUnique"><?php _e('Show unique posts only', 'focal'); ?></label><br />
      				<span><?php _e('Shows the number of posts contained within a category. Unique posts removes duplicates, for example if a post is a member of two child categories of the one being counted.', 'focal'); ?></span>
      				<br/>
         			<label for="focal_widegtCountBefore"><?php _e('Before post count:', 'focal'); ?> </label><input type="text" name="focal_widegtCountBefore" id="focal_widegtCountBefore" size="20" value="<?php echo $this->widgetOptions['CountBefore'] ?>"/><br/>
         			<label for="focal_widegtCountAfter"><?php _e('After post count:', 'focal'); ?> </label><input type="text" name="focal_widegtCountAfter" id="focal_widegtAfterBefore" size="20" value="<?php echo $this->widgetOptions['CountAfter'] ?>"/><br/>
      				<span><?php _e('Text to show before and after the post count. By default this is a pair of parenthesis, but you can use angle brackets, arrows, or any html.', 'focal'); ?></span>
      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('SEO', 'focal'); ?></th>
      			<td>
      				<input type="checkbox" id="focal_UseDescriptionAsCategoryText" name="focal_UseDescriptionAsCategoryText" <?php echo ($this->widgetOptions['UseDescriptionAsCategoryText']) ? "checked" : '' ;?>/>&nbsp;<label for="focal_UseDescriptionAsCategoryText"><?php _e('Use category description as the link text.', 'focal'); ?></label><br />
      				<input type="checkbox" id="focal_UseDescriptionAsCategoryTitle" name="focal_UseDescriptionAsCategoryTitle" <?php echo ($this->widgetOptions['UseDescriptionAsCategoryTitle']) ? "checked" : '' ;?>/>&nbsp;<label for="focal_UseDescriptionAsCategoryTitle"><?php _e('Use category description as the popup (title) text.', 'focal'); ?></label><br />
      				<span><?php _e('How should the category description be used? Note: you must have the description enabled on the global settings for these to take effect.', 'focal'); ?></span><br /><br />
      				<input type="checkbox" id="focal_DontLinkCurrentCategory" name="focal_DontLinkCurrentCategory" <?php echo ($this->widgetOptions['DontLinkCurrentCategory']) ? "checked" : '' ;?>/>&nbsp;<label for="focal_DontLinkCurrentCategory"><?php _e('Don\'t add link for current (selected) category', 'focal'); ?></label><br />
      				<input type="checkbox" id="focal_NoFollowAllLinks" name="focal_NoFollowAllLinks" <?php echo ($this->widgetOptions['NoFollowAllLinks']) ? "checked" : '' ;?>/>&nbsp;<label for="focal_NoFollowAllLinks"><?php _e('Add rel="nofollow" to all links', 'focal'); ?></label><br/>
      				<input type="checkbox" id="focal_NoFollowParents" name="focal_NoFollowParents" <?php echo ($this->widgetOptions['NoFollowParents']) ? "checked" : '' ;?>/>&nbsp;<label for="focal_NoFollowParents"><?php _e('Add rel="nofollow" to all links NOT below current category (allow follow on child category)', 'focal'); ?></label><br/>
      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('Additional Links', 'focal'); ?></th>
      			<td>
      				<span><?php _e('This section allows additional static links to be placed in the list, before or after the category list. You must enter a pipe delimited ("|") string in the following format, with each link on a new line.', 'focal'); ?></span></br>
      				<span><pre><?php _e('link title|http://www.link.url|popup title description|some text before|some text after', 'focal'); ?></pre></span></br>
      				<span><?php _e('Text before and after the links are not included as the anchor tag, so you can have "Visit <span style="text-decoration:underline">my photos</span> page".</span>', 'focal'); ?></br>
      				</br>
              <p><span><?php _e('Links before the category list:', 'focal'); ?></span><br/>
              <textarea name="focal_widgetLinksBefore" id="focal_widgetLinksBefore" style="width:80%; height:80px"><?php echo ($this->widgetOptions['LinksBefore']); ?></textarea></p>
              <p><span><?php _e('Links after the category list:', 'focal'); ?></span><br/>
      				<textarea name="focal_widgetLinksAfter" id="focal_widgetLinksAfter" style="width:80%; height:80px"><?php echo ($this->widgetOptions['LinksAfter']); ?></textarea></p>
      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('CSS', 'focal'); ?></th>
      			<td>
      				<input type="checkbox" id="focal_OutputCatIDs" name="focal_OutputCatIDs" <?php echo ($this->widgetOptions['OutputCatIDs']) ? "checked" : '' ;?>/>&nbsp;<label for="focal_OutputCatIDs"><?php _e('Use category ID as CSS ID on &lt;li&gt; tags, e.g. &lt;li id=&quot;cat-53&quot;&gt;', 'focal'); ?></label><br/>
<?php 
if ($this->widgetOptions['ApplyCSSTo'] == 'li') { $li = ' selected'; $value= 'li'; }
if ($this->widgetOptions['ApplyCSSTo'] == 'a') { $a = ' selected'; $value= 'a'; }
if ($this->widgetOptions['ApplyCSSTo'] == 'span') { $span = ' selected'; $value= 'span'; }
?>      				
              <?php _e('Apply CSS classes to:', 'focal'); ?> 
              <select name="focal_applyCSSTo" id="focal_applyCSSTo" value="<?php echo $value; ?>">
      				  <option<?php echo $li; ?> value="li"><?php _e('List item tags (li) (default)', 'focal'); ?></option>
      				  <option<?php echo $a; ?> value="a"><?php _e('Links/Anchor tags (a)', 'focal'); ?></option>
      				  <option<?php echo $span; ?> value="span"><?php _e('Wrap contents in a span tag', 'focal'); ?></option>
              </select>
      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('jQuery Animation', 'focal'); ?></th>
      			<td>
      				<input type="checkbox" id="focal_Superfish" name="focal_Superfish" <?php echo ($this->widgetOptions['Superfish']) ? "checked" : '' ;?>/>&nbsp;<label for="focal_Superfish"><?php _e('<a href="http://users.tpg.com.au/j_birch/plugins/superfish/">Superfish Menu</a> support', 'focal'); ?></label><br/>
      				&nbsp; &nbsp; <input type="checkbox" id="focal_SuperfishJS" name="focal_SuperfishJS" <?php echo ($this->defaultOptions['IncludeSuperfishJS']) ? "checked" : '' ;?>/>&nbsp;<label for="focal_SuperfishJS"><?php _e('Include Javascript (optional, will work with just CSS)', 'focal'); ?></label><br/>
      				&nbsp; &nbsp; <input type="checkbox" id="focal_SuperfishCSS" name="focal_SuperfishCSS" <?php echo ($this->defaultOptions['IncludeSuperfishCSS']) ? "checked" : '' ;?>/>&nbsp;<label for="focal_SuperfishCSS"><?php _e('Include CSS (default only, you will need to copy CSS to your themes stylesheet to customise. Please refer to the <a href="http://www.lonewolfdesigns.co.uk/focal/" rel="nofollow">plugin homepage</a> for details.', 'focal'); ?></label><br/><br/>
      				
					<input type="checkbox" id="focal_Accordian" name="focal_Accordian" <?php echo ($this->widgetOptions['Accordian']) ? "checked" : '' ;?>/>&nbsp;<label for="focal_Accordian"><?php _e('<a href="http://www.i-marco.nl/weblog/jquery-accordion-3/">jQuery Accordian</a> support', 'focal'); ?></label><br/>
      				&nbsp; &nbsp; <input type="checkbox" id="focal_AccordianJS" name="focal_AccordianJS" <?php echo ($this->defaultOptions['IncludeAccordianJS']) ? "checked" : '' ;?>/>&nbsp;<label for="focal_AccordianJS"><?php _e('Include Javascript', 'focal'); ?></label><br/>
      				&nbsp; &nbsp; <input type="checkbox" id="focal_AccordianCSS" name="focal_AccordianCSS" <?php echo ($this->defaultOptions['IncludeAccordianCSS']) ? "checked" : '' ;?>/>&nbsp;<label for="focal_AccordianCSS"><?php _e('Include CSS (default only, you will need to copy CSS to your themes stylesheet to customise. Please refer to the <a href="http://www.lonewolfdesigns.co.uk/focal/" rel="nofollow">plugin homepage</a> for details.', 'focal'); ?></label><br/><br/>
      			</td>
      		</tr>   		   		
      		<tr>
      			<th scope="row" valign="top"><?php _e('Miscellaneous', 'focal'); ?></th>
      			<td>
      				<input type="checkbox" id="focal_ShowEmptyCats" name="focal_ShowEmptyCats" <?php echo ($this->widgetOptions['ShowEmptyCats']) ? "checked" : '' ;?>/>&nbsp;<label for="focal_ShowEmptyCats"><?php _e('Show empty categories.', 'focal'); ?></label><br/>
      				<input type="checkbox" id="focal_MultiLingual" name="focal_MultiLingual" <?php echo ($this->widgetOptions['MultiLingual']) ? "checked" : '' ;?>/>&nbsp;<label for="focal_MultiLingual"><?php _e('Allow Multilingual Translations.', 'focal'); ?></label><br/>
      			</td>
      		</tr>   		   		
        </table>      
        <br/>
        <span style="float:right"><input type="submit" name="focalSubmitOptions" value="<?php _e('Save Changes', 'focal'); ?>" class="button"/></span>
        <br/><br/>
        <div style="clear:both"></div>
      </form>
    </div></div>
  <?php 
  }
  /* the admin menu callback*/
  function widget_FocalAdminMenu() 
  {
    add_options_page('Folding Category List', 'FoCaL', 8, __FILE__, array(&$this, 'widget_FocalAdminPage'));
  }
  
  function superfish_activator() 
  {
    $content = '<script> 
    jQuery.noConflict();  
	jQuery(document).ready(function() { 
        jQuery(\'ul.sf-menu\').superfish(); 
    }); 
</script>';
    echo $content;
  }
  
  function accordian_activator() 
  {
    $content = '<script> 
    jQuery.noConflict();  
	jQuery(document).ready(function() { 
	    jQuery(\'ul.menu\').initMenu();
	});
</script>';	
    echo $content;
  }
}


/* wp 2.8+ activator */
if (function_exists('register_widget'))
{
  function register_FoldingCategoryList()
  {
    register_widget('FoldingCategoryList');
  }
  add_action('widgets_init', 'register_FoldingCategoryList', 1);
}

?>