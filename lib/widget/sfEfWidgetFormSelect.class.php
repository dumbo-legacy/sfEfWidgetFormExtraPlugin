<?php
/**
 * sfEfWidgetFormSelect represents a custom select widget
 *
 * @package    sfEfWidgetFormExtraPlugin
 * @subpackage widget
 * @author     Yaismel Miranda Pons <ympons@estudiantes.uci.cu>
 *             Edier García Gutierrez <egarciagu@uci.cu>
 * @version    SVN: $Id$
 */
class sfEfWidgetFormSelect extends sfWidgetForm
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * choices:  An array of possible choices (required)
   *  * multiple: true if the select tag must allow multiple selections
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('choices');
    $this->addOption('theme', 'default');
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The value selected in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $choices = $this->getOption('choices');
    if ($choices instanceof sfCallable)
    {
      $choices = $choices->call();
    }
    
    if (!isset($attributes['class']))
    {
      $attributes['class'] = 'mcdropdown_menu';
    }
    if (!isset($attributes['id']))
    {
      $attributes['id'] = $this->generateId($name);
    } 
       
    return 
    sprintf(<<<EOF
<div class="columnaAI" align="right">
  <input type="text" name="%s" id="select" />
</div>    
EOF
   , $name)
    . $this->renderContentTag('ul', "\n" . implode("\n", $this->getOptionsForSelect($choices)) . "\n", $attributes)
    . sprintf(<<<EOF
<script type="text/javascript">
  jQuery(document).ready(function() {
    jQuery("#select").mcDropdown("#%s");
  });
</script>

EOF
   , $attributes['id']);
  }

  /**
   * Returns an array of option tags for the given choices
   *
   * @param  string $value    The selected value
   * @param  array  $choices  An array of choices
   *
   * @return array  An array of option tags
   */
  protected function getOptionsForSelect($choices)
  {          
    $options = array();
    foreach ($choices as $key => $option)
    {
      if (is_array($option))
      {
        $ul = $this->renderContentTag('ul', implode("\n", $this->getOptionsForSelect($option)));
        $content = $key . $ul;
        $rel = $key;
      }
      else
      {
        $content = self::escapeOnce($option);  
        $rel = $option;    
      }
      $options[] = $this->renderContentTag('li', $content, array('rel' => $rel));
    }
    
    return $options;
  }

  public function __clone()
  {
    if ($this->getOption('choices') instanceof sfCallable)
    {
      $callable = $this->getOption('choices')->getCallable();
      $class = __CLASS__;
      if (is_array($callable) && $callable[0] instanceof $class)
      {
        $callable[0] = $this;
        $this->setOption('choices', new sfCallable($callable));
      }
    }
  }
    
  /**
   * Gets the stylesheet paths associated with the widget.
   *
   * @return array An array of stylesheet paths
   */
  public function getStylesheets()
  {
    return array('/sfEfWidgetFormExtraPlugin/css/jquery.mcdropdown.'. $this->getOption('theme') .'.css' => 'all');
  }

  /**
   * Gets the JavaScript paths associated with the widget.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavascripts()
  {
    return array(
      '/sfEfWidgetFormExtraPlugin/js/jquery.mcdropdown.js' => '/sfEfWidgetFormExtraPlugin/js/jquery.mcdropdown.js',
      '/sfEfWidgetFormExtraPlugin/js/jquery.bgiframe.min.js' => '/sfEfWidgetFormExtraPlugin/js/jquery.bgiframe.min.js'
    );
  }
  
}
