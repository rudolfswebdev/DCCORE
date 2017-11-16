<?php

Class FoldingCategoryList 
{
  var $dbOptions = 'widget_focalOptions';
  var $wdOptions = 'widget_focalWidgets';
  var $wdCache = 'widget_focalCache_';
  var $pageCatCF = 'category';
  var $cacheVersion = '1.0.0';
  var $defaultOptions;
  var $widgetOptions;
  var $theTree;
  var $theTreeParantal;
  var $categoryFamilyTree;
  var $currentCategory;
  var $hasAdminMessage = false;
  var $adminMessage = "";
  
  function FoldingCategoryList() 
  {
    // Get the current options from the db
    $this->GetOptions();

    // Process any submit forms if logged in as admin                                          
    if (is_admin())
      $this->ProcessPostSubmit();
    
    /* register widgets */
 		$this->RegisterWidgets();
		
		/* setup actions to rebuild the cache */
    if ($this->defaultOptions['AutoBuildCache'] == true)
    {
      add_action('publish_post', array(&$this, 'RebuildCache'));
      add_action('edit_post', array(&$this, 'RebuildCache'));
      add_action('delete_post', array(&$this, 'RebuildCache'));
      add_action('create_$taxonomy', array(&$this, 'RebuildCache'));
      add_action('edit_$taxonomy', array(&$this, 'RebuildCache'));
      add_action('delete_$taxonomy', array(&$this, 'RebuildCache'));
    }
    
    add_action('sidebar_admin_page', array(&$this, 'widget_FocalHowMany'));
    add_action('admin_menu', array(&$this, 'widget_FocalAdminMenu'));
  }
  
  /* Options */
  /* These following methods will perform actions on the options - Get and Set */
  function GetOptions()
  {
    $this->defaultOptions = $this->GetDefaultOptions();
    $optionsFromTable = get_option($this->dbOptions);
    if (($optionsFromTable != false) && (is_null($optionsFromTable['version'])))
      $this->defaultOptions = array_merge((array)$this->defaultOptions,(array)$optionsFromTable);

    $default = $this->GetDefaultWidgetConfig();
    $widgetsFromTable = get_option($this->wdOptions);
    $options = array();
    
    if (($widgetsFromTable != false) && (is_null($widgetsFromTable['widgettitle'])))
    {
      for ($i=1; $i<=$this->defaultOptions['NumberOfWidgets']; $i++)
      {
        $options[$i] = $default;
        if ($i <= count($widgetsFromTable))
        {
          foreach ($widgetsFromTable[$i] as $key => $value)
          {
            $options[$i][$key] = $value;
          }
        }
      }
    }
    else
    {
      $options[1] = $default;
    }
    $this->widgetOptions = $options;
  }

  function GetDefaultOptions()
  {
    $options['AutoBuildCache'] = true;
    $options['NumberOfWidgets'] = 1;
    $options['UseCache'] = true;
    $options['DontUseCategoryDescription'] = false;
  
    return $options;
  }
  function GetDefaultWidgetConfig()
  {
    $widget['Title'] = 'Navigation';
    $widget['CSSTheme'] = 'none';
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
     
    return $widget;
  }
  function SetDefaultOptions()
  {
    $option = $this->dbOptions;
    if (get_option($option)) 
    {
      update_option($option, $this->defaultOptions);
    } 
    else 
    {
      $deprecated=' ';
      $autoload='no';
      add_option($option, $this->defaultOptions, $deprecated, $autoload);
    }
  }
  function SetDefaultWidgets()
  {
    $option = $this->wdOptions;
    if (get_option($option)) 
    {
      update_option($option, $this->widgetOptions);
    } 
    else 
    {
      $deprecated=' ';
      $autoload='no';
      add_option($option, $this->widgetOptions, $deprecated, $autoload);
    }  
  } 
   
  /* Cache */ 
  /* These functions deal with the main tree cache */
  function GetATree($number)
  {
    // We have the ability to store a different cache on a per widget basis
    // but is it really necessary? This line forces use of 1 cache object.
    $number = 1;
    if ($this->defaultOptions['UseCache'] == true)
    {
      $option = $this->wdCache.$number;
      $cache = get_option($option);
  
      if ($cache != null) 
      {
        if ((count($cache) == 2) && ($cache['version'] == $this->cacheVersion))
        {
          $themenu =  $cache['cache'];
        }
        else  
        {
          $themenu = $this->BuildTheTree($number);
        }
      }  
      else
      {
        $themenu = $this->BuildTheTree($number);
      }
    }
    else
    {   
      $themenu = $this->BuildTheTree($number);
    }
  
    $this->theTree = $themenu;
  }
  
  function SaveATree($tree, $number)
  {
    // We have the ability to store a different cache on a per widget basis
    // but is it really necessary? This line forces use of 1 cache object.
    $number = 1;
    if ($this->defaultOptions['UseCache'] == true)
    {
      $cache['cache'] = $tree;      
      $cache['version'] = $this->cacheVersion;
      $option = $this->wdCache.$number;
      
      if (get_option($option)) 
      {
        update_option($option, $cache);
      } 
      else 
      {
        $deprecated=' ';
        $autoload='no';
        add_option($option, $cache, $deprecated, $autoload);
      }
    }  
  }
  function DeleteATree($number)
  {
    // We have the ability to store a different cache on a per widget basis
    // but is it really necessary? This line forces use of 1 cache object.
    $number = 1;
    $option = $this->wdCache.$number;
    delete_option($option);
  }
 
  
  function BuildTheTree($number)
  {  
    global $wpdb, $wp_query;
    
    if ( ($this->widgetOptions[$number]['OrderBy'] == "mycategoryorder") && (function_exists("mycategoryorder_init")) )
    {
      $query = 'SELECT * FROM `'.$wpdb->prefix.'term_taxonomy` JOIN `'.$wpdb->prefix.'terms` ON '.$wpdb->prefix.'terms.term_id = '.$wpdb->prefix.'term_taxonomy.term_id WHERE '.$wpdb->prefix.'term_taxonomy.taxonomy = "category" ORDER BY '.$wpdb->prefix.'terms.term_order ASC';
    }
    else
    {
      $query = 'SELECT * FROM `'.$wpdb->prefix.'term_taxonomy` JOIN `'.$wpdb->prefix.'terms` ON '.$wpdb->prefix.'terms.term_id = '.$wpdb->prefix.'term_taxonomy.term_id WHERE '.$wpdb->prefix.'term_taxonomy.taxonomy = "category" ORDER BY '.$wpdb->prefix.'terms.name ASC';
    }  
    
    $query = $wpdb->prepare($query);
    $categorylist = $wpdb->get_results($query, ARRAY_A);
    $result = array();
    $a = 0;
  
    foreach ($categorylist as $item)
    {
      $count = $this->GetCount($item['term_id']);
      $categories = get_categories('child_of='.$item['term_id']);
      if (count($categories) > 0)
        $haschildren = true;
      else
        $haschildren = false;
      
      $countu = $count[1];
      $counta = $count[0];
      
      if ($this->defaultOptions['DontUseCategoryDescription'] == false)
        $description = $item['description'];
      else 
        $description = "";       
      
      $result[] = $item['term_id'] . '#|#' . $item['name'] . '#|#' . $description . '#|#' . $item['parent'] . '#|#' . $counta . '#|#' . $countu . '#|#' . $item['slug'] . '#|#' . $haschildren; 
    }          
    
    $this->SaveATree($result, $number);
    return $result;  
  }

  /* Draw the actucal folding category list */
  function DrawTheTree($number)
  {
    $this->theTreeParantal = null;
    $this->currentCategory = $this->GetCurrentCategory($number);
    $this->BuildParentalList();
    $this->GetATree($number);

    if ($this->widgetOptions[$number]['RootCategory'] == -1)
    {
      if (is_category())
      {
        global $wp_query;
        $this->widgetOptions[$number]['RootCategory'] = $wp_query->get_queried_object_id();
      }
      else if (is_single())
      {
        $this->widgetOptions[$number]['RootCategory'] = $this->currentCategory;
      }
    } 
           
    /* sort if required */ 
    
    if ($this->widgetOptions[$number]['OrderBy'] != "mycategoryorder")
    {
      if ( ($this->widgetOptions[$number]['OrderBy'] != "name") || ($this->widgetOptions[$number]['OrderByDirection'] != "asc") )
      {
        switch ($this->widgetOptions[$number]['OrderBy'])
        {
          case "name": $sortby = 1; break; 
          case "id": $sortby = 0; break; 
          case "description": $sortby = 2; break; 
          case "slug": $sortby = 6; break; 
          case "postcount": 
            if ($this->widgetOptions[$number]['ShowPostCountUnique'])
              $sortby = 5; 
            else
              $sortby = 4; 
            break; 
          default: $sortby = 1;
        } 
  
        if (is_array($this->theTree))
        {
          foreach ($this->theTree as $menuitem)
            $itemsexpanded[] = explode("#|#", $menuitem);  
    
          $itemsexpanded = $this->QuicksortTheTree($itemsexpanded, $sortby, 0, $this->widgetOptions[$number]['OrderByDirection']);
        
          foreach ($itemsexpanded as $menuitem)
            $newtree[] = implode("#|#", $menuitem);
            
          $this->theTree = $newtree;
        }  
      }
    }
    
    return $this->OutputTheTree($number,$this->widgetOptions[$number]['RootCategory'],0);
    $this->theTreeParantal = null;
  }
  
  function OutputTheTree($number, $parent, $level)
  {
    $haveDoneItems = false;
    if ($level == 0)
    {
      $result = '<!-- Folding Category List for Wordpress by Tim Trott --><ul class="FoldingCategoryList nodeLevel0" id="focalist_'.$number.'">';
      $result .= $this->DoExtraLinks($this->widgetOptions[$number]['LinksBefore']);
    }
    else
      $result = '<ul class="nodeLevel'.$number.'">';

    if (is_array($this->theTree))
    {  
      foreach ($this->theTree as $element) 
      {
        list($term_id, $Ename, $Edescription, $catparent, $Etotalcount, $Euniquecount, $Eslug, $haschildren) = explode("#|#", $element);
        
        // Dont show item or children if excluded
        if (!in_array($term_id, $this->widgetOptions[$number]['ExcludeCats']))
        {
          $itemClass = array();
          
          if ($parent == $catparent)
          {  
            if ($this->widgetOptions[$number]['UseDescriptionAsCategoryText'] == true)
            {
              $title = htmlspecialchars($Edescription);
              if ($title == "")
                $title = htmlspecialchars($Ename);
            }  
            else
              $title = htmlspecialchars($Ename);
            
            $description = htmlspecialchars($Edescription);
            $slug = $Eslug;
            
            // Show post count after text
            if ($this->widgetOptions[$number]['ShowPostCount'] == true)
            {
              if ($this->widgetOptions[$number]['ShowPostCountUnique'] == true)
                $count = $Euniquecount;
              else
                $count = $Etotalcount;
        
              $linkcounter = $this->widgetOptions[$number]['CountBefore'] . $count . $this->widgetOptions[$number]['CountAfter'];
            } 
            else 
            {
              $linkcounter = '';
            }
            
            // Description as title for links
            if ($this->widgetOptions[$number]['UseDescriptionAsCategoryTitle'] == true)
            {
              if ($description != "")
                $linktitle = ' title="' . $description . '"';
              else
                $linktitle = '';
            }
            else
              $linktitle = '';
            
            // use cat id as cssid
            if ($this->widgetOptions[$number]['OutputCatIDs'] == true)
               $li_postid = ' id="cat-' . $term_id . '"';
            else 
               $li_postid = '';
            
            
            // Select Current Item
            if (in_array($term_id,$this->currentCategory))
            {
              $itemClass[] = 'selected';
              $linkSelected = ' class="focalLinkSelected"';
            }
            else
              $linkSelected = '';

            // Does this item have child categories?
            if ($haschildren)
              $itemClass[] = 'haschildren';
            else 
              $itemClass[] = 'nochildren';

            
            if (($Etotalcount > 0) || ($this->widgetOptions[$number]['ShowEmptyCats'] == true))
            {
              // Is this item an ancestor of a selected item?
              if ((!in_array('selected', $itemClass)) && (is_array($this->theTreeParantal)))
              {
                if (in_array($slug, $this->theTreeParantal))  
                  $itemClass[] = 'selectedparent'; 
              } 
              
              // Output the list item
              $result .= '<li ';
              
              if ($this->widgetOptions[$number]['ApplyCSSTo'] == 'li')
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
              
              if ($this->widgetOptions[$number]['ApplyCSSTo'] == 'span')
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
              
              
              
              if ((in_array($term_id,$this->currentCategory))  && ($this->widgetOptions[$number]['DontLinkCurrentCategory'] == true) )
              { 
                $result .= $title;
              }
              else
              {
                $result .= '<a ' . $linkSelected . ' href="' . get_category_link($term_id) . '"' . $linktitle;
                if ($this->widgetOptions[$number]['ApplyCSSTo'] == 'a')
                {
                  if (count($itemClass)>0)
                  {
                    $result .= ' class="';
                    foreach ($itemClass as $attribute)
                      $result .= ' '. $attribute;
                    $result .= '"';
                  }
                }
                $result .= '>' . $title . '</a>';
                //$result .= '>' . get_cat_name($term_id) . '</a>';
              }
              
              $result .= $linkcounter;
              
              if ($this->widgetOptions[$number]['ApplyCSSTo'] == 'span')
              {
                $result .= '</span>';
              }

              if (is_array($this->theTreeParantal))
              {
                
                if ((in_array($slug, $this->theTreeParantal)) || ($this->widgetOptions[$number]['ExpandAllCats'] == true))  
                  $result .= $this->OutputTheTree($number, $term_id, $level+1);
              }

              $result .= '</li>';
              $haveDoneItems = true;
            }
          }
        }
      }
    }
    if ($level == 0)
      $result .= $this->DoExtraLinks($this->widgetOptions[$number]['LinksAfter']);
    
    $result .= '</ul>';

    if ($level == 0)
      $result = str_replace('&amp;amp;','&amp;',$result); 

    if ($haveDoneItems)
      return $result;
    else 
      return "";   
  } 

  function DoExtraLinks($links)
  {
    if ($links != "")
    {
      $links = explode("\n", $links);
      $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
      
      // 0          1               2                 3           4
      // link title|http://link url|popup description|text before|text after
      
      $result = "";
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
            
          if ($link[3] != "")
            $result .= $link[3];
  
          $result .= '<a href="' . $link[1] . '"'.$css;
          
          if ($link[2] != "")
            $result .= ' title="' . $link[2] . '"';
          
          $result .= '>' . $link[0] . '</a>';
            
          if ($link[4] != "")
            $result .= $link[4];
           
          $result .= '</li>';
        }
      }
    }
    return $result;
  }

  function BuildParentalList()
  {
    if ($this->theTreeParantal == null)
    {
      $parentlist = "";
      foreach ($this->currentCategory as $cat)
      {
        $par = get_category_parents($cat,FALSE,'|',TRUE);
  
        if (!is_a($par,WP_Error))
          $parentlist .= $par;
      }
      $this->theTreeParantal = explode('|',$parentlist);
    }  
  }

  function GetCurrentCategory($number)
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
      $cats = get_the_category($post->ID);
  
      // Only one, so this is easy...
      // A post is always in a category, even if its uncategorized.
      if (count($cats) == 1)
      {
        $curcat[0] = $cats[0]->term_id;
        $found = true;
      }
      else if ($this->widgetOptions[$number]['ExpandAllMember'] == true)
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
        $priority = explode(",",$this->widgetOptions[$number]['HightlightPriority']);
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
          if ($this->widgetOptions[$number]['RootCategory'] > 0)
          {
            // Parent is not zero, so try and locate a category under parent
            foreach ($cats as $cat)
            {
              if (!$found)
              {
                if ($cat->term_id == $this->widgetOptions[$number]['RootCategory'])
                {
                  $curcat[0] = $cat->term_id; 
                  $found = true;              
                }
                else
                {
                  $parents = $this->GetCatParents($cat->term_id);
                  if (in_array($this->widgetOptions[$number]['RootCategory'], $parents))
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
      
      if (!$found)
      {
        // Error?
        $curcat[0] = $cats[0]->term_id;
      }
    }  
    return $curcat;	  
  } 

  function GetParental()
  {
    if ($this->categoryFamilyTree == null)
    {
      global $wpdb, $wp_query;
      $query = 'SELECT * FROM `'.$wpdb->prefix.'term_taxonomy` JOIN `'.$wpdb->prefix.'terms` ON '.$wpdb->prefix.'terms.term_id = '.$wpdb->prefix.'term_taxonomy.term_id WHERE '.$wpdb->prefix.'term_taxonomy.taxonomy = "category"';
      $query = $wpdb->prepare($query);
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
  
    $children = get_categories('child_of=' . $category);
    $children[] = get_category($category);
    $subcatposts = array();
    
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
  		";
  		$querystr = $wpdb->prepare($querystr); 
      $result = $wpdb->get_results($querystr, ARRAY_A);
  			
      if($result)
        foreach ($result as $id)
          $subcatposts[] = $id["ID"];
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
    	$value = $treeitem[$sortUsBy];
  		$compareValue = $pivot[$sortUsBy];
  
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





  /**** The widget itself ****/
  function widget_FocalWidget($args = '', $number = 1)
  {
    switch($this->widgetOptions[$number]['CSSTheme'])
    {
      case 'indent':
  ?><style type="text/css">
  .FoldingCategoryList li {margin:1px; padding:0; list-style-type:none;font-weight:normal;}
  .FoldingCategoryList li ul {margin-left:8px !important}
  .selectedparent {font-style:italic}
  .selectedparent ul {font-style:normal}
  .selected {font-weight:bold}
  .selected ul {font-weight:normal}
  </style><?php
        break;
      case 'indent_arrowbullet':
  ?><style type="text/css">
  .FoldingCategoryList li:before {content:none}
  .FoldingCategoryList li {margin:1px; padding:0; list-style-type:none;font-weight:normal;}
  .FoldingCategoryList li ul {margin-left:6px}
  .FoldingCategoryList li ul li {background-image: url('<?php echo get_option('siteurl');?>/wp-content/plugins/folding-category-widget/themes/0099_sosimple.png'); background-repeat:no-repeat; padding-left:12px; background-position:top left}
  .selected {font-weight:bold}
  .selected ul {font-weight:normal}
  .selectedparent {font-style:italic}
  .selectedparent ul {font-style:normal}
  </style><?php
        break;      
      case 'indent_circlebullet':
  ?><style type="text/css">
  .FoldingCategoryList li:before {content:none}
  .FoldingCategoryList li {margin:1px; padding:0; list-style-type:none;font-weight:normal;}
  .FoldingCategoryList li ul {margin-left:6px}
  .FoldingCategoryList li ul li {background-image: url('<?php echo get_option('siteurl');?>/wp-content/plugins/folding-category-widget/themes/bullet_green.gif'); background-repeat:no-repeat; padding-left:12px; background-position:top left}
  .selected a {font-weight:bold}
  .selectedparent {font-style:italic}
  .selectedparent ul {font-style:normal}
  </style><?php
        break;      
      case 'explorer':
  ?><style type="text/css">
  .FoldingCategoryList li:before {content:none}
  .FoldingCategoryList li {margin:1px; padding:0; padding-left:10px; list-style-type:none;font-weight:normal;}
  .FoldingCategoryList li ul {margin-left:8px}
  .selected a {font-weight:bold}
  .selectedparent, .selectedhaschildren {margin-left:-10px; padding-left:10px; background-image: url('<?php echo get_option('siteurl');?>/wp-content/plugins/folding-category-widget/themes/bullet_toggle_minus.gif'); background-repeat:no-repeat; background-position:top left}
  .haschildren {background-image: url('<?php echo get_option('siteurl');?>/wp-content/plugins/folding-category-widget/themes/bullet_toggle_plus.gif'); background-repeat:no-repeat; background-position:top left} 
  .selectedparent ul {font-style:normal}
  </style><?php
        break;  
    }  
    
    if (is_array($args))
      extract($args);
      
    echo $before_widget;
    if ($this->widgetOptions[$number]['Title'])
      print($before_title . $this->widgetOptions[$number]['Title'] . $after_title);
    echo $this->DrawTheTree($number);
    echo $after_widget;
  }
  
  
  /**** Process any options submitted to a form ****/
  function ProcessPostSubmit()
  {
    if(!empty($_POST['focalNumberSubmit']))
    {
      $widgetCount = $_POST['focalNumber'];
      if (is_numeric($widgetCount))
      {
        $this->UpdateNumberOfWidgets($widgetCount);
        $this->GetOptions();
      }
    } 
    else if(!empty($_POST['focalRebuildCache']))
    {
      $this->RebuildCache();
      $this->GetOptions();
    }
    else if(!empty($_POST['focalDeleteCache']))
    {
      $this->DeleteCache();
      $this->GetOptions();
    }
    else if(!empty($_POST['focalUninstall']))
    {
      $this->Uninstall();
      $this->GetOptions();
    }
    else if(!empty($_POST['focalSubmitOptions']))
    {
      $this->UpdateOptions();
      $this->GetOptions();
    }  
  }
  function RegisterWidgets()
  {
    $number = $this->defaultOptions['NumberOfWidgets'];
		if ( $number < 1 ) $number = 1;
		if ( $number > 9 ) $number = 9;
		for ($i = 1; $i <= 9; $i++) 
    {
	 	  $name = array('FoCaL %s', null, $i);
 		  register_sidebar_widget($name, $i <= $number ? array(&$this, 'widget_FocalWidget') : /* unregister */ '', $i);
 		  register_widget_control($name, $i <= $number ? array(&$this, 'widget_FocalWidgetControl') : /* unregister */ '', 200, 200, $i);
 		}
  }
  function UpdateNumberOfWidgets($howMany)
  {
    $this->defaultOptions['NumberOfWidgets'] = $howMany;
    $this->SetDefaultOptions();
    $this->GetDefaultOptions();
  }
  function RebuildCache()
  {
    $this->DeleteATree(1);
    $this->BuildTheTree(1);
    $this->hasAdminMessage = true;
    $this->adminMessage = "Cache Rebuilt";
  } 
  function DeleteCache()
  {
    $this->DeleteATree(1);
    $this->hasAdminMessage = true;
    $this->adminMessage = "Cache Deleted";
  }
  function Uninstall()
  {
    $this->DeleteATree(1);
    delete_option($this->dbOptions);
    delete_option($this->wdOptions);
    $this->hasAdminMessage = true;
    $this->adminMessage = "Plugin options deleted from database. You can now deactivate the plugin.";
  }  
  function UpdateOptions()
  {
    $selectedWidget =   $_POST['focal_selectedWidget'];

    if (is_numeric($selectedWidget))
    {
      $this->defaultOptions['AutoBuildCache'] = ($_POST['focal_AutoRebuildCache'] == 'on');
      $this->defaultOptions['UseCache'] = ($_POST['focal_UseCache'] == 'on');
      $this->defaultOptions['DontUseCategoryDescription'] = !($_POST['focal_UseDescriptions'] == 'on');
  
      $this->widgetOptions[$selectedWidget]['Title'] = $_POST['focal_widegtTitle'];
      $this->widgetOptions[$selectedWidget]['CSSTheme'] = $_POST['focal_widgetCSSTheme'];

      if (is_numeric($_POST['focal_widgetParent']))
        $this->widgetOptions[$selectedWidget]['RootCategory'] = $_POST['focal_widgetParent'];
      else if ($_POST['focal_widgetParent'] == "showAll")
        $this->widgetOptions[$selectedWidget]['RootCategory'] = 0;
      else if ($_POST['focal_widgetParent'] == "showChild")
        $this->widgetOptions[$selectedWidget]['RootCategory'] = -1;
      
      if ( is_array($_POST['post_category']) )
      {
        $this->widgetOptions[$selectedWidget]['ExcludeCats'] = $_POST['post_category'];
      }
      else
      {
        if (isset($_POST['focal_widgetExcluded']))
          $this->widgetOptions[$selectedWidget]['ExcludeCats'] = explode(',', $_POST['focal_widgetExcluded']);
      }
      
      $this->widgetOptions[$selectedWidget]['HightlightPriority'] = str_replace(' ', '', $_POST['focal_widegtPriority']);

      if ($_POST['focal_widgetShowMethod'] == 'normal')
      {
        $this->widgetOptions[$selectedWidget]['ExpandAllCats'] = false;
        $this->widgetOptions[$selectedWidget]['ExpandAllMember'] = false;
      }
      else if ($_POST['focal_widgetShowMethod'] == 'members')
      {
        $this->widgetOptions[$selectedWidget]['ExpandAllCats'] = false;
        $this->widgetOptions[$selectedWidget]['ExpandAllMember'] = true;
      }
      else if ($_POST['focal_widgetShowMethod'] == 'all')
      {
        $this->widgetOptions[$selectedWidget]['ExpandAllCats'] = true;
        $this->widgetOptions[$selectedWidget]['ExpandAllMember'] = false;
      }
      
      if ($_POST['focal_applyCSSTo'] == 'li')
      {
        $this->widgetOptions[$selectedWidget]['ApplyCSSTo'] = 'li';
      }
      else if ($_POST['focal_applyCSSTo'] == 'a')
      {
        $this->widgetOptions[$selectedWidget]['ApplyCSSTo'] = 'a';
      }
      else if ($_POST['focal_applyCSSTo'] == 'span')
      {
        $this->widgetOptions[$selectedWidget]['ApplyCSSTo'] = 'span';
      }


      if ($_POST['focal_widgetSortBy'] == 'name')
        $this->widgetOptions[$selectedWidget]['OrderBy'] = 'name';
      else if ($_POST['focal_widgetSortBy'] == 'id')
        $this->widgetOptions[$selectedWidget]['OrderBy'] = 'id';
      else if ($_POST['focal_widgetSortBy'] == 'description')
        $this->widgetOptions[$selectedWidget]['OrderBy'] = 'description';
      else if ($_POST['focal_widgetSortBy'] == 'slug')
        $this->widgetOptions[$selectedWidget]['OrderBy'] = 'slug';
      else if ($_POST['focal_widgetSortBy'] == 'postcount')
        $this->widgetOptions[$selectedWidget]['OrderBy'] = 'postcount';
      else if ($_POST['focal_widgetSortBy'] == 'mycategoryorder')
        $this->widgetOptions[$selectedWidget]['OrderBy'] = 'mycategoryorder';

      if ($_POST['focal_widgetSortBySeq'] == 'asc')
        $this->widgetOptions[$selectedWidget]['OrderByDirection'] = 'asc';
      else if ($_POST['focal_widgetSortBySeq'] == 'desc')
        $this->widgetOptions[$selectedWidget]['OrderByDirection'] = 'desc';

      $this->widgetOptions[$selectedWidget]['UseDescriptionAsCategoryText'] = ($_POST['focal_UseDescriptionAsCategoryText'] == 'on');
      $this->widgetOptions[$selectedWidget]['UseDescriptionAsCategoryTitle'] = ($_POST['focal_UseDescriptionAsCategoryTitle'] == 'on');
      $this->widgetOptions[$selectedWidget]['LinksBefore'] = $_POST['focal_widgetLinksBefore']; 
      $this->widgetOptions[$selectedWidget]['LinksAfter'] = $_POST['focal_widgetLinksAfter']; 
      $this->widgetOptions[$selectedWidget]['ShowPostCount'] = ($_POST['focal_ShowPostCount'] == 'on');
      $this->widgetOptions[$selectedWidget]['ShowPostCountUnique'] = ($_POST['focal_ShowPostCountUnique'] == 'on');
      $this->widgetOptions[$selectedWidget]['OutputCatIDs'] = ($_POST['focal_OutputCatIDs'] == 'on');
      $this->widgetOptions[$selectedWidget]['ShowEmptyCats'] = ($_POST['focal_ShowEmptyCats'] == 'on');

      $this->widgetOptions[$selectedWidget]['DontLinkCurrentCategory'] = ($_POST['focal_DontLinkCurrentCategory'] == 'on');
       
      $this->widgetOptions[$selectedWidget]['CountBefore'] = $_POST['focal_widegtCountBefore'];
      $this->widgetOptions[$selectedWidget]['CountAfter'] = $_POST['focal_widegtCountAfter'];

      $this->SetDefaultOptions();
      $this->SetDefaultWidgets();                                       
      $this->hasAdminMessage = true;
      $this->adminMessage = "Settings updated.";
    }
  } 
  
  



  /**** Wordpress actions and hooks ****/
  
  /* The options for the widget */
  function widget_FocalWidgetControl()
  {
    _e('Folding Category List options can now be found in the Settings menu under "FoCaL"', 'focal'); 
  }
  
  /* widget administration */
  function widget_FocalAdminPage() 
  {
    $selectedWidget = 1;
    
    if(!empty($_POST['focalChangeWidget']))
    {
      if (is_numeric($_POST['focalNumber']))
      {
        $selectedWidget = $_POST['focalNumber'];
      }
    }  
    
    
  ?>
    <div><div class="wrap">
      <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <h2><?php _e('Folding Category List: Options', 'focal'); ?></h2>
        <p>The Folding Category List provides a fast and efficient replacement for the category list which which allows categories to be folded, or collapsed, allowing more room on your sidebar. These options allow you to customise each widget indipendantly.</p> 
<?php if ($this->hasAdminMessage) { ?>        
        <div id="message" class="updated fade"><p><?php echo $this->adminMessage; ?></p></div>
<?php } ?>        
        <h3><?php _e('Global Settings', 'focal'); ?></h3>
      	
        <table class="form-table">
      		<tr>
      			<th scope="row" valign="top"><?php _e('Enable Cache', 'focal'); ?></th>
      			<td>
      				<input type="checkbox" id="focal_UseCache" name="focal_UseCache" <?php echo ($this->defaultOptions['UseCache']) ? "checked" : "" ;?>/><br />
      				<span>This widget will build a category list from all the categories and store it in the database. All future requests use the cached version. Building the data can take upto 20 seconds, whereas retrieving the cached data only takes on average 0.025 seconds.</span><br/>
      				<input type="submit" name="focalRebuildCache" value="<?php _e('Rebuild Cache', 'focal'); ?>" class="button"/> &nbsp; <input type="submit" name="focalDeleteCache" value="<?php _e('Delete Cache', 'focal'); ?>" class="button"/> &nbsp; <input type="submit" name="focalUninstall" value="<?php _e('Uninstall/Reset', 'focal'); ?>" class="button"/>
      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('Auto Rebuild Cache', 'focal'); ?></th>
      			<td>
      				<input type="checkbox" id="focal_AutoRebuildCache" name="focal_AutoRebuildCache" <?php echo ($this->defaultOptions['AutoBuildCache']) ? "checked" : "" ;?>/><br />
      				<span>Rebuild the cache when posting, deleting or modifing posts. Can slow down posting, so you may wish to disable if posting multiple entries. Remember to rebuild cache when done.</span>
      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('Use Descriptions', 'focal'); ?></th>
      			<td>
      				<input type="checkbox" id="focal_UseDescriptions" name="focal_UseDescriptions" <?php echo ($this->defaultOptions['DontUseCategoryDescription']) ? "" : "checked" ;?>/><br />
      				<span>By default category descriptions are used for the title attribute on links, but if you have long descriptions or hundreds of categories this can lead to a large database. If so, disable this option and category descriptions will be removed. Changing this value will cause the cache to be rebuilt, if it's enabled.</span>
      			</td>
      		</tr>
        </table> 
        
        <br/>
        <span style="float:right"><input type="submit" name="focalSubmitOptions" value="<?php _e('Save Changes', 'focal'); ?>" class="button"/></span>
        <div style="clear:both"></div>
        <br/>
        <h3><?php _e('Individual Widgets', 'focal'); ?></h3> 
        <span style="font-style:italic">You have <?php echo $this->defaultOptions['NumberOfWidgets']; ?> widget(s) enabled for use on the sidebar. To add more please visit the Widgets admin page.</span>
        <br/>
        
        <table class="form-table">
      		<tr>
      			<th scope="row" valign="top"><?php _e('Widget Number', 'focal'); ?></th>
      			<td>
    				  <input type="hidden" name="focal_selectedWidget" value="<?php echo $selectedWidget; ?>"/>
              <select id="focalNumber" name="focalNumber" value="<?php echo $selectedWidget; ?>">
                <?php for ( $i = 1; $i < 10; ++$i ) echo "  				    <option value='$i' ".($selectedWidget == $i ? "selected='selected'" : '').">$i</option>\n"; ?>
  				    </select> &nbsp; <input type="submit" name="focalChangeWidget" value="<?php _e('Set', 'focal'); ?>" class="button"/><br/>
      				<span>Which widget would you like to change?</span>   				
      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('Widget Title', 'focal'); ?></th>
      			<td>
      				<input type="text" name="focal_widegtTitle" size="20" value="<?php echo $this->widgetOptions[$selectedWidget]['Title'] ?>"/><br />
      				<span>Heading for the widget, e.g. Navigation, Browse by Category etc...</span>
      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('Root Category', 'focal'); ?></th>
      			<td>
<?php
    
      if ($this->widgetOptions[$selectedWidget]['RootCategory'] > 0)
      {
        $list = wp_dropdown_categories('hierarchical=1&echo=0&name=focal_widgetParent&selected=' . $this->widgetOptions[$selectedWidget]['RootCategory']);
        $pos = strpos($list, '<option');
        $list = substr($list,0,$pos) . '<option value="showAll">show all categories</option><option value="showChild">show child categories only</option>' . substr($list,$pos);
      }
      else if ($this->widgetOptions[$selectedWidget]['RootCategory'] == 0)
      {
        $list = wp_dropdown_categories('hierarchical=1&echo=0&name=focal_widgetParent');
        $pos = strpos($list, '<option');
        $list = substr($list,0,$pos) . '<option selected value="showAll">show all categories</option><option value="showChild">show child categories only</option>' . substr($list,$pos);
      }
      else if ($this->widgetOptions[$selectedWidget]['RootCategory'] == -1)
      {
        $list = wp_dropdown_categories('hierarchical=1&echo=0&name=focal_widgetParent');
        $pos = strpos($list, '<option');
        $list = substr($list,0,$pos) . '<option value="showAll">show all categories</option><option selected value="showChild">show child categories only</option>' . substr($list,$pos);
      }
      
      echo $list;
?><br/>
      				<span>Select the root (parent) category to show, show all categories (default), or only show child categories of the current category.</span>
      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('Categories to Exclude', 'focal'); ?></th>
      			<td>
                <?php 
                  if ( function_exists('wp_category_checklist') )
                  { ?>
              <div id="categorydiv" style="height:170px; width:220px; overflow:auto; background-color:white;padding:8px;"><ul id="categorychecklist" class="list:category categorychecklist form-no-clear">

              <?php wp_category_checklist(0,0,$this->widgetOptions[$selectedWidget]['ExcludeCats']); ?>
              </ul></div>
              <span>The selected categories will not be shown on the Folding Category List, nor will any child categories of the selected. Uncategorized is excluded by default.</span>
               
               <?php }
                  else
                  {
?>
              <input type="text" name="focal_widgetExcluded" size="20" value="<?php echo implode(",",$this->widgetOptions[$selectedWidget]['ExcludeCats']) ?>"/><br />
      				<span>Comma seperated list for category ID to exclude from the list. Child categories will not be shown either.</span>

<?php                  }               
                  ?>
      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('Category Expansion', 'focal'); ?></th>
      			<td>
                <?php if ($this->widgetOptions[$selectedWidget]['ExpandAllCats'])
                      {
                        $normal = "";
                        $member = "";
                        $all = "selected";
                        $value = "all";
                      }
                      else if ($this->widgetOptions[$selectedWidget]['ExpandAllMember'])
                      {
                        $normal = "";
                        $member = "selected";
                        $all = "";
                        $value = "members";
                      }
                      else
                      {
                        $normal = "selected";
                        $member = "";
                        $all = "";
                        $value = "normal";
                      }?>
              <select name="focal_widgetShowMethod" id="focal_widgetShowMethod" value="<?php echo $value;?>">
                <option value="normal" <?php echo $normal ?>>Normal</option>
                <option value="members" <?php echo $member ?>>Expand All Members</option>
                <option value="all" <?php echo $all ?>>Expand All</option>
              </select>
              <br />
      				<span>This option defines how the Folding Category List will expand. Setting this option to 'Normal' will only expand the the current category, or the category with the highest priority (see below). Setting to 'Expand All Members' will cause all categories that the current post is a member of to be selected/highlighted. Setting to 'Expand All' will show a fully expanded list with all categories shown.</span>
      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('Category Priority', 'focal'); ?></th>
      			<td>
      				<input type="text" name="focal_widegtPriority" id="focal_widegtPriority" size="40" value="<?php echo $this->widgetOptions[$selectedWidget]['HightlightPriority'] ?>"/><br />
      				<span>This option is a comma seperated list of category ID's that is used to determine which category to highlight in the event that a post is assigned to multiple categories. If a post category is a member of, or a child of, a category in this list then it will be expanded. If a category is not on the priority list then Wordpress will select the lowest category id. Category ID's can be found on the Manage Categories screen.</span>
      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('Order By', 'focal'); ?></th>
      			<td><?php 
      			
if ($this->widgetOptions[$selectedWidget]['OrderBy'] == "name") { $name = " selected"; $value= "name"; }
if ($this->widgetOptions[$selectedWidget]['OrderBy'] == "id") { $id = " selected"; $value= "id"; }
if ($this->widgetOptions[$selectedWidget]['OrderBy'] == "description") { $description = " selected"; $value= "description"; }
if ($this->widgetOptions[$selectedWidget]['OrderBy'] == "slug") { $slug = " selected"; $value= ""; }
if ($this->widgetOptions[$selectedWidget]['OrderBy'] == "postcount") { $postcount = " selected"; $value= "postcount"; }
if ($this->widgetOptions[$selectedWidget]['OrderBy'] == "mycategoryorder") { $mycategoryorder = " selected"; $value= "mycategoryorder"; }

if ($this->widgetOptions[$selectedWidget]['OrderByDirection'] == "asc") { $asc = " selected"; $valuea= "asc"; }
if ($this->widgetOptions[$selectedWidget]['OrderByDirection'] == "desc") { $desc = " selected"; $valuea= "desc"; }

      			?>
      				<select name="focal_widgetSortBy" id="focal_widgetSortBy" value="<?php echo $value; ?>">
      				  <option<?php echo $name; ?> value="name">Category Name (default)</option>
      				  <option<?php echo $id; ?> value="id">Category ID</option>
      				  <option<?php echo $description; ?> value="description">Category Description</option>
      				  <option<?php echo $slug; ?> value="slug">Category Slug</option>
      				  <option<?php echo $postcount; ?> value="postcount">Category Post Count</option>
<?php if (function_exists("mycategoryorder_init")) { ?>      				  <option<?php echo $mycategoryorder; ?> value="mycategoryorder">My Category Order</option><?php } ?>
              </select> &nbsp; <select name="focal_widgetSortBySeq" id="focal_widgetSortBySeq" value="<?php echo $valuea; ?>">
                <option<?php echo $asc; ?> value="asc">Ascending (default)</option>
                <option<?php echo $desc; ?> value="desc">Descending</option>
              </select><br/>                
      				<span>How should the list be sorted? <?php if (function_exists("mycategoryorder_init")) { ?><em>nb: if selecting My Category Order you must manually rebuild the cache after changing the order.</em><?php } ?></span>
      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('Post Count', 'focal'); ?></th>
      			<td>
      				<input type="checkbox" id="focal_ShowPostCount" name="focal_ShowPostCount" <?php echo ($this->widgetOptions[$selectedWidget]['ShowPostCount']) ? "checked" : "" ;?>/><label for="focal_ShowPostCount">Show post count</label><br />
      				<input type="checkbox" id="focal_ShowPostCountUnique" name="focal_ShowPostCountUnique" <?php echo ($this->widgetOptions[$selectedWidget]['ShowPostCountUnique']) ? "checked" : "" ;?>/><label for="focal_ShowPostCountUnique">Show unique posts only</label><br />
      				<span>Shows the number of posts contained within a category. Unique posts counts removes duplicates, for example if a post is a member of two child categories of the one being counted.</span>
      				<br/>
         			<label for="focal_widegtCountBefore">Before post count: </label><input type="text" name="focal_widegtCountBefore" id="focal_widegtCountBefore" size="20" value="<?php echo $this->widgetOptions[$selectedWidget]['CountBefore'] ?>"/><br/>
         			<label for="focal_widegtCountAfter">After post count: </label><input type="text" name="focal_widegtCountAfter" id="focal_widegtAfterBefore" size="20" value="<?php echo $this->widgetOptions[$selectedWidget]['CountAfter'] ?>"/><br/>
      				<span>Text to show before and after the post count. By default this is a pair of parenthesis, but you can use angle brackets, arrows, or any html.</span>
      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('SEO', 'focal'); ?></th>
      			<td>
      				<input type="checkbox" id="focal_UseDescriptionAsCategoryText" name="focal_UseDescriptionAsCategoryText" <?php echo ($this->widgetOptions[$selectedWidget]['UseDescriptionAsCategoryText']) ? "checked" : "" ;?>/><label for="focal_UseDescriptionAsCategoryText">Use category description as the link text.</label><br />
      				<input type="checkbox" id="focal_UseDescriptionAsCategoryTitle" name="focal_UseDescriptionAsCategoryTitle" <?php echo ($this->widgetOptions[$selectedWidget]['UseDescriptionAsCategoryTitle']) ? "checked" : "" ;?>/><label for="focal_UseDescriptionAsCategoryTitle">Use category description as the popup (title) text.</label><br />
      				<input type="checkbox" id="focal_DontLinkCurrentCategory" name="focal_DontLinkCurrentCategory" <?php echo ($this->widgetOptions[$selectedWidget]['DontLinkCurrentCategory']) ? "checked" : "" ;?>/><label for="focal_DontLinkCurrentCategory">Don't add link for current (selected) category</label><br />
      				<span>How should the category description be used? Note: you must have the description enabled on the global settings for these to take effect.</span>
      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('Additional Links', 'focal'); ?></th>
      			<td>
      				<span>This section allows additional static links to be placed in the list, before or after the category list. You must enter a pipe delimited ("|") string in the following format, with each link on a new line.</span></br>
      				<span><pre>link title|http://www.link.url|popup title description|some text before|some text after</pre></span></br>
      				<span>Text before and after the link are not included as the anchor tag, so you can have "Visit <span style="text-decoration:underline">my photos</span> page".</span></br>
      				</br></br>
              <p><span>Links before the category list:</span><br/></br>
      				<textarea name="focal_widgetLinksBefore" id="focal_widgetLinksBefore" style="width:80%; height:80px"><?php echo ($this->widgetOptions[$selectedWidget]['LinksBefore']); ?></textarea></p>
              <p><span>Links after the category list:</span><br/></br>
      				<textarea name="focal_widgetLinksAfter" id="focal_widgetLinksAfter" style="width:80%; height:80px"><?php echo ($this->widgetOptions[$selectedWidget]['LinksAfter']); ?></textarea></p>
      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('CSS', 'focal'); ?></th>
      			<td>
      				<input type="checkbox" id="focal_OutputCatIDs" name="focal_OutputCatIDs" <?php echo ($this->widgetOptions[$selectedWidget]['OutputCatIDs']) ? "checked" : "" ;?>/><label for="focal_OutputCatIDs">Use category ID as CSS ID on &lt;li&gt; tags.</label><br/>
<?php 
if ($this->widgetOptions[$selectedWidget]['ApplyCSSTo'] == "li") { $li = " selected"; $value= "li"; }
if ($this->widgetOptions[$selectedWidget]['ApplyCSSTo'] == "a") { $a = " selected"; $value= "a"; }
if ($this->widgetOptions[$selectedWidget]['ApplyCSSTo'] == "span") { $span = " selected"; $value= "span"; }
?>      				
              Apply CSS classes to: 
              <select name="focal_applyCSSTo" id="focal_applyCSSTo" value="<?php echo $value; ?>">
      				  <option<?php echo $li; ?> value="li">List item tags (li) (default)</option>
      				  <option<?php echo $a; ?> value="a">Links/Anchor tags (a)</option>
      				  <option<?php echo $span; ?> value="span">Wrap contents in a span tag</option>
              </select><br/>


<?php 
if ($this->widgetOptions[$selectedWidget]['CSSTheme'] == "none") { $none = " selected"; $value= "none"; }
if ($this->widgetOptions[$selectedWidget]['CSSTheme'] == "indent") { $indent = " selected"; $value= "indent"; }
if ($this->widgetOptions[$selectedWidget]['CSSTheme'] == "indent_bullet") { $indent_bullet = " selected"; $value= "indent_bullet"; }
if ($this->widgetOptions[$selectedWidget]['CSSTheme'] == "indent_arrowbullet") { $indent_arrow = " selected"; $value= "indent_arrowbullet"; }
if ($this->widgetOptions[$selectedWidget]['CSSTheme'] == "explorer") { $explorer = " selected"; $value= "explorer"; }
?>      			CSS Theme: 	
              <select name="focal_widgetCSSTheme" id="focal_widgetCSSTheme" value="<?php echo $value; ?>">
      				  <option<?php echo $none; ?> value="none">None (default)</option>
      				  <option<?php echo $indent; ?> value="indent">Indented</option>
      				  <option<?php echo $indent_bullet; ?> value="indent_bullet">Indented with bullet points</option>
      				  <option<?php echo $indent_arrow; ?> value="indent_arrowbullet">Indented with arrows</option>
      				  <option<?php echo $explorer; ?> value="explorer">Explorer style + and - symbols</option>
              </select><br/>
              <span>A selection of pre-defined CSS templates to style the folding category list. These CSS styles are only given as a quick referenec, they may or may not work on your theme. For detailed CSS information please see the plugin homepage.</span>

      			</td>
      		</tr>
      		<tr>
      			<th scope="row" valign="top"><?php _e('Miscellaneous', 'focal'); ?></th>
      			<td>
      				<input type="checkbox" id="focal_ShowEmptyCats" name="focal_ShowEmptyCats" <?php echo ($this->widgetOptions[$selectedWidget]['ShowEmptyCats']) ? "checked" : "" ;?>/><label for="focal_ShowEmptyCats">Show empty categories.</label><br/>
      			</td>
      		</tr>   		
                		
        </table>      
        <br/>
        <span style="float:right"><input type="submit" name="focalSubmitOptions" value="<?php _e('Save Changes', 'focal'); ?>" class="button"/></span>
        <div style="clear:both"></div>
        <br/>
  
      </form>
    </div></div>
  <?php 
  }
  
  /* number of widgets */
  function widget_FocalHowMany() 
  {
    ?>
  		<div class="wrap">
  			<form method="POST">
  				<h2><?php _e('Folding Category Lists', 'focal'); ?></h2>
  				<p style="line-height: 30px;"><?php _e('How many Folding Category widgets would you like?', 'focal'); ?>
  				<select id="focalNumber" name="focalNumber" value="<?php echo $this->defaultOptions['NumberOfWidgets']; ?>">
  	<?php for ( $i = 1; $i < 10; ++$i ) echo "<option value='$i' ".($this->defaultOptions['NumberOfWidgets'] == $i ? "selected='selected'" : '').">$i</option>"; ?>
  				</select>
  				<span class="submit"><input type="submit" name="focalNumberSubmit" id="focalNumberSubmit" value="Save" /></span></p>
  			</form>
  		</div>
  	<?php
  }  
  
  /* the admin menu */
  function widget_FocalAdminMenu() 
  {
    add_options_page('Folding Category List', 'FoCaL', 8, __FILE__, array(&$this, 'widget_FocalAdminPage'));
  }
}
  
  
if (function_exists('add_action'))
{
  function widget_FocalRegister() 
  {
    $c = new FoldingCategoryList(); 
  }
  add_action('plugins_loaded', 'widget_FocalRegister');
}
else
{
  echo "Please do not load this page directly.";
}

?>