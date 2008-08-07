<?php
/* -----------------------------------------------------------------------------
  Plugin     : Advanced Menu Manager
  Author     : Grum
    email    : grum@grum.dnsalias.com
    website  : http://photos.grum.dnsalias.com
    PWG user : http://forum.phpwebgallery.net/profile.php?id=3706

    << May the Little SpaceFrog be with you ! >>
  ------------------------------------------------------------------------------
  See main.inc.php for release information

  AMM_root : root classe for plugin 

  --------------------------------------------------------------------------- */

if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

include_once(PHPWG_PLUGINS_PATH.'grum_plugins_classes-2/common_plugin.class.inc.php');

class AMM_root extends common_plugin
{ 
  function AMM_root($prefixeTable, $filelocation)
  {
    $this->plugin_name="Advanced Menu Manager";
    $this->plugin_name_files="amm";
    parent::__construct($prefixeTable, $filelocation);

    $list=array('urls', 'personalised');
    $this->set_tables_list($list);
  }

  /* ---------------------------------------------------------------------------
  common AIP & PIP functions
  --------------------------------------------------------------------------- */

  /* this function initialize var $my_config with default values */
  public function init_config()
  {
    global $menu;

    $this->my_config=array(
      'amm_links_show_icons' => 'y',
      'amm_links_active' => 'y',
      'amm_links_title' => array(),
      'amm_sections_visible' => array(),
      'amm_randompicture_active' => 'n',
      'amm_randompicture_showname' => 'n',     //n:no, o:over, u:under
      'amm_randompicture_showcomment' => 'n',   //n:no, o:over, u:under
      'amm_randompicture_title' => array(),
      'amm_sections_modspecial' => array(
        'favorite_cat' => 'y',
        'most_visited_cat' => 'y',
        'best_rated_cat' => 'y',
        'random_cat' => 'y',
        'recent_pics_cat' => 'y',
        'recent_cats_cat' => 'y',
        'calendar' => 'y'
      ),
      'amm_sections_modmenu' => array(
        'qsearch' => 'y',
        'Tags' => 'y',
        'Search' => 'y',
        'comments' => 'y',
        'About' => 'y',
        'Notification' => 'y'
      )
    );

    $languages=get_languages();
    foreach($languages as $key => $val)
    {
      if($key=='fr_FR')
      {
        $this->my_config['amm_links_title'][$key]=base64_encode('Liens');
        $this->my_config['amm_randompicture_title'][$key]=base64_encode('Une image au hasard');
      }
      else
      {
        $this->my_config['amm_links_title'][$key]=base64_encode('Links');
        $this->my_config['amm_randompicture_title'][$key]=base64_encode('A random picture');
      }
    }

    $sections=$menu->registered();
    foreach($sections as $key => $val)
    {
      $this->my_config['amm_sections_visible'][$key]='y';
    }
  }

  public function load_config()
  {
    global $menu;

    parent::load_config();

    $sections=$menu->registered();
    foreach($sections as $key => $val)
    {
      if(!isset($this->my_config['amm_sections_visible'][$key]))
      {
        $this->my_config['amm_sections_visible'][$key]='y';
      }
    }
    
  }

  // return an array of urls (each url is an array)
  protected function get_urls($only_visible=false)
  {
    $returned=array();
    $sql="SELECT * FROM ".$this->tables['urls'];
    if($only_visible)
    {
      $sql.=" WHERE visible = 'y' ";
    }
    $sql.=" ORDER BY position";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=mysql_fetch_array($result))
      {
        $row['label']=stripslashes($row['label']);
        $returned[]=$row;
      }
    }
    return($returned);
  }

  //return number of url
  protected function get_count_url($only_visible=false)
  {
    $returned=0;
    $sql="SELECT count(id) FROM ".$this->tables['urls'];
    if($only_visible)
    {
      $sql.=" WHERE visible = 'y' ";
    }
    $result=pwg_query($sql);
    if($result)
    {
      $tmp=mysql_fetch_row($result);
      $returned=$tmp[0];
    }
    return($returned);
  }

  // return an array of sections (each section is an array)
  protected function get_sections($only_visible=false, $lang="", $only_with_content=true)
  {
    global $user;

    if($lang=="")
    {
      $lang=$user['language'];
    }

    $returned=array();
    $sql="SELECT * FROM ".$this->tables['personalised']."
WHERE (lang = '*' OR lang = '".$lang."') ";
    if($only_visible)
    {
      $sql.=" AND visible = 'y' ";
    }
    if($only_with_content)
    {
      $sql.=" AND content != '' ";
    }
    $sql.=" ORDER BY id, lang DESC ";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=mysql_fetch_array($result))
      {
        $returned[]=$row;
      }
    }
    return($returned);
  }



} // amm_root  class


?>
