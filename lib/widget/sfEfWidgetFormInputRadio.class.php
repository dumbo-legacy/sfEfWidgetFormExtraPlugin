<?php
/**
 * sfEfWidgetFormInputCheckbox represents a custom radio widget
 *
 * @package    sfEfWidgetFormExtraPlugin
 * @subpackage widget
 * @author     Yaismel Miranda Pons <yaismelmp@googlemail.com>
 *             Edier García Gutierrez
 * @version    SVN: $Id$
 */
class sfEfWidgetFormInputRadio extends sfWidgetFormInput
{
  protected static $radio_count = 0;
  /**
   * Constructor.
   *
   * Available options:
   *
   *  - value_attribute_value: The "value" attribute value to set for the checkbox
   *  - group_name: Group name
   *  
   * @param array  $options     An array of options
   * @param array  $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormInput
   */
  public function __construct($options = array(), $attributes = array())
  {
    $this->addOption('value_attribute_value');
    $this->addOption('group_name');
    $this->addOption('theme');
    $this->addOption('javascript');
    
    parent::__construct($options, $attributes);
  }

  /**
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormInput
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    
    $this->setOption('type', 'radio');
    $this->setOption('group_name', 'rbtn');
    $this->setOption('theme', 'default');
    $this->setOption('javascript', '');
    
    if (isset($attributes['value']))
    {
      $this->setOption('value_attribute_value', $attributes['value']);
    }
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The this widget is checked if value is not null
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $orig_name = $name;
    $name = preg_replace('/(.+?)\[(.+)\]$/', '$1['. $this->getOption('group_name') .'][]', $name);
    $attributes['id'] = $this->generateId($orig_name.self::$radio_count++, $value);
    
    if (!is_null($value) && $value != false)
    {
      $attributes['checked'] = 'checked';
    }
    
    if (!isset($attributes['value']) && !is_null($this->getOption('value_attribute_value')))
    {
      $attributes['value'] = $this->getOption('value_attribute_value');
    } 
    
    return parent::render($name, $value, $attributes, $errors) .
    sprintf(<<<EOF
<script type="text/javascript">
  jQuery(document).ready(function() {
    jQuery("input:radio#%s").checkbox(jQuery.extend({}, {
      imagesPath: '/sfEfWidgetFormExtraPlugin/images/',
      cls: 'jquery-radio'
    })).click(function(e){
      %s
    });
  });
</script>
EOF
   , $attributes['id']
   , $this->getOption('javascript'));
  }

  /**
   * Gets the stylesheet paths associated with the widget.
   *
   * @return array An array of stylesheet paths
   */
  public function getStylesheets()
  {
    return array('/sfEfWidgetFormExtraPlugin/css/jquery.radio.'. $this->getOption('theme') .'.css' => 'all');
  }

  /**
   * Gets the JavaScript paths associated with the widget.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavascripts()
  {  
    return array('/sfEfWidgetFormExtraPlugin/js/jquery.checkbox.js' => '/sfEfWidgetFormExtraPlugin/js/jquery.checkbox.js');
  }
}
