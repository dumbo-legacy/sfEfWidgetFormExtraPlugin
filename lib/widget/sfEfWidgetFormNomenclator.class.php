<?php
/**
 * sfEfWidgetFormNomenclator represents a custom choice widget
 *
 * @package    sfEfWidgetFormExtraPlugin
 * @subpackage widget
 * @author     Yaismel Miranda Pons <yaismelmp@googlemail.com>
 * @version    SVN: $Id$
 * 
 */
class sfEfWidgetFormNomenclator extends sfWidgetFormChoice
{
  /**
   * @see sfWidget
   */
  public function __construct($options = array(), $attributes = array())
  {
    $this->addOption('choices', array());
    parent::__construct($options, $attributes);
  }
  /**
   * Constructor.
   *
   * @see sfEfWidgetFormSelect
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addOption('array', array());
    $this->addOption('add_empty', false);
    $this->addOption('theme', 'default');
    parent::configure($options, $attributes);
  }

  /**
   * Returns the choices associated to the model.
   *
   * @return array An array of choices
   */
  public function getChoices()
  {
    $choices = array();
    if (false !== $this->getOption('add_empty'))
    {
      $choices[''] = true === $this->getOption('add_empty') ? '' : $this->translate($this->getOption('add_empty'));
    }
    
    if ($this->getOption('choices'))
    {
      $array = $this->getOption('choices');
      foreach ($array as $key => $value)
      {
        $choices[$key] = __(str_replace('_', ' ', $value), null, 'global');
      }      
    }
    else
    {    
      $array = $this->getOption('array');
      foreach ($array as $key => $value)
      {
        $choices[$value['id']] = __($value['name'], null, 'global');
      }
    }
    return $choices;
  }

}
