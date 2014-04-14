<?php
/**
 * sfEfWidgetFormChoice represents a custom choice widget
 *
 * @package    sfEfWidgetFormExtraPlugin
 * @subpackage widget
 * @author     Yaismel Miranda <yaismelmp@googlemail.com>
 * @version    SVN: $Id$
 * 
 */
class sfEfWidgetFormChoice extends sfWidgetFormChoice
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * choices:          An array of possible choices (required)
   *  * multiple:         true if the select tag must allow multiple selections
   *  * expanded:         true to display an expanded widget
   *                        if expanded is false, then the widget will be a select
   *                        if expanded is true and multiple is false, then the widget will be a list of radio
   *                        if expanded is true and multiple is true, then the widget will be a list of checkbox
   *  * renderer_options: The options to pass to the renderer constructor
   *   
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormChoice
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addOption('theme', 'default');
    parent::configure($options, $attributes);
  }

  public function getRenderer()
  {
    $type = !$this->getOption('expanded') ? '' : ($this->getOption('multiple') ? 'checkbox' : 'radio');
    $class = sprintf('sfEfWidgetFormSelect%s', ucfirst($type));
    
    return new $class(array_merge(array('theme' => $this->getOption('theme'), 'choices' => new sfCallable(array($this, 'getChoices'))), $this->getOption('renderer_options')), $this->getAttributes());
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
}
