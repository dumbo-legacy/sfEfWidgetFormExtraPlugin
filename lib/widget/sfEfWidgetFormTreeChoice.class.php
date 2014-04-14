<?php
/**
 * sfEfWidgetFormTreeChoice represents a choice widget
 *
 * @package    sfEfWidgetFormExtraPlugin
 * @subpackage widget
 * @author     Yaismel Miranda Pons <yaismelmp@googlemail.com>
 * @version    SVN: $Id$
 * 
 */
class sfEfWidgetFormTreeChoice extends sfWidgetFormDoctrineChoice
{
 
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('choices');

    parent::configure($options, $attributes);
  }
  
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {    
    if (is_null($value))
    {
      $value = array();
    }
    
    $choices = $this->getChoices();
    if ($choices instanceof sfCallable)
    {
      $choices = $choices->call();
    }
    $tree = array();
    foreach ($choices as $key => $option)
    {
      $action = @explode('.', $option);
      if (in_array(strval($key), $value))
      {
        $tree[$action[0]][$action[1]][] = '<li id="'.$key.'" selected="true"><a href="#"><ins>&nbsp;</ins>'.$option.'</a></li>';
      }
      else
      {
        $tree[$action[0]][$action[1]][] = '<li id="'.$key.'"><a href="#"><ins>&nbsp;</ins>'.$option.'</a></li>';
      }
    }
    $idcomponent = $this->generateId($name);
    $treeWidget  = '<div id="'.$idcomponent.'" style="display:none;"><ul>';
    foreach ($tree as $app => $mods)
    {
       $treeWidget .= '<li><a href="#"><ins>&nbsp;</ins>'.$app.'</a><ul>';
       foreach ($mods as $mod => $action)
       {
          $treeWidget .= '<li><a href="#"><ins>&nbsp;</ins>'.$mod.'</a><ul>';
          $treeWidget .= implode('',$action);
          $treeWidget .= '</ul></li>';
       }
       $treeWidget .= '</ul></li>';
    }
    $treeWidget.="</ul></div>";
    jq_add_plugins_by_name(array('tree'));
    return sprintf(<<<EOF
<script type="text/javascript">
  $(function(){
    $("#%s").tree({
      ui: { theme_name : "checkbox" },
      plugins: { checkbox : {} },
      callback: {
          onload: function(TREE_OBJ){
              $('#%s li[selected=true]').each(function(){
                 $.tree.plugins.checkbox.check(this);
              })
          }      
      }
    }).show("slow");
    $("form").bind("submit", function(){
        var treeId = "%s";
        $.tree.plugins.checkbox.get_checked($.tree.reference("#" + treeId)).each(function () {
          var checkedId = this.id;
          if(checkedId){
            $("<input>").attr("type", "hidden").attr("name", "%s[]").val(checkedId).appendTo("#" + treeId);            
          }
        });
    })
    
  })
</script>
EOF
   , $idcomponent, $idcomponent, $idcomponent, $name).$treeWidget;
  }

  public function __clone()
  {
    if ($this->getOption('choices') instanceof sfCallable)
    {
      $callable = $this->getOption('choices')->getCallable();
      if (is_array($callable))
      {
        $callable[0] = $this;
        $this->setOption('choices', new sfCallable($callable));
      }
    }
  }
  
}
