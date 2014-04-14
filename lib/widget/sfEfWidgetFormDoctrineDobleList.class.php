<?php
/**
 * sfEfWidgetFormDoctrineDobleList represents a multiple select displayed as a double list for a model.
 *
 * @package    sfEfWidgetFormExtraPlugin
 * @subpackage widget
 * @author     Yaismel Miranda <yaismelmp@googlemail.com>
 * @version    SVN: $Id$
 */
class sfEfWidgetFormDoctrineDobleList extends sfWidgetFormDoctrineChoice
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * choices:            An array of possible choices (required)
   *  * class:              The main class of the widget
   *  * class_select:       The class for the two select tags
   *  * label_unassociated: The label for unassociated
   *  * label_associated:   The label for associated
   *  * unassociate:        The HTML for the unassociate link
   *  * associate:          The HTML for the associate link
   *  * associated_first:   Whether the associated list if first (true by default)
   *  * template:           The HTML template to use to render this widget
   *                        The available placeholders are:
   *                          * label_associated
   *                          * label_unassociated
   *                          * associate
   *                          * unassociate
   *                          * associated
   *                          * unassociated
   *                          * class
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormDoctrineChoice
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('choices');

    $this->addOption('class', 'double_list');
    $this->addOption('class_select', 'double_list_select');
    $this->addOption('associated_first', true);
    $this->addOption('label_unassociated', __('Unassociated', null, 'global'));
    $this->addOption('label_associated', __('Associated', null, 'global'));
    $this->addOption('myfunction', false);
    $this->addOption('associated_choices', array());

    parent::configure($options, $attributes);

    $associated_first = isset($options['associated_first']) ? $options['associated_first'] : true;

    if ($associated_first)
    {
      $associate_image = 'previous.png';
      $unassociate_image = 'next.png';
      $float = 'left';
    }
    else
    {
      $associate_image = 'next.png';
      $unassociate_image = 'previous.png';
      $float = 'right';
    }

    $this->addOption('unassociate', '<img src="/sfEfWidgetFormExtraPlugin/images/'.$unassociate_image.'" alt="unassociate" />');
    $this->addOption('associate', '<img src="/sfEfWidgetFormExtraPlugin/images/'.$associate_image.'" alt="associate" />');
    $this->addOption('template', <<<EOF
<div class="%class%">
  <div style="float: left">
    <div style="float: $float">
      <div class="double_list_label">%label_associated%</div>
      %associated%
    </div>
    <div style="float: $float; margin-top: 2em">
      %associate%
      <br />
      %unassociate%
    </div>
    <div style="float: $float">
      <div class="double_list_label">%label_unassociated%</div>
      %unassociated%
    </div>
  </div>
  <br style="clear: both" />
  <script type="text/javascript">
    sfDoubleList.init(document.getElementById('%id%'), '%class_select%');
  </script>
</div>
EOF
);
  }

  /**
   * Renders the widget.
   *
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
    if (is_null($value))
    {
      $value = $this->getOption('associated_choices');
    }

    $choices = $this->getChoices();
    if ($choices instanceof sfCallable)
    {
      $choices = $choices->call();
    }
	
	$associated = array();
    $unassociated = array();
    foreach ($choices as $key => $option)
    {
      if (in_array(strval($key), $value))
      {
        $associated[$key] = __(str_replace('_', ' ', $option), null, 'global');
      }
      else
      {
        $unassociated[$key] = __(str_replace('_', ' ', $option), null, 'global');
      }
    }

    $size = isset($attributes['size']) ? $attributes['size'] : (isset($this->attributes['size']) ? $this->attributes['size'] : 10);

    $associatedWidget = new sfWidgetFormSelect(array('multiple' => true, 'choices' => $associated), array('size' => $size, 'class' => $this->getOption('class_select').'-selected'));
    $unassociatedWidget = new sfWidgetFormSelect(array('multiple' => true, 'choices' => $unassociated), array('size' => $size, 'class' => $this->getOption('class_select')));

    $function = ($this->getOption('myfunction')) ? $this->getOption('myfunction').';' : '';
    
    return strtr($this->getOption('template'), array(
      '%class%'              => $this->getOption('class'),
      '%class_select%'       => $this->getOption('class_select'),
      '%id%'                 => $this->generateId($name),
      '%label_associated%'   => $this->getOption('label_associated'),
      '%label_unassociated%' => $this->getOption('label_unassociated'),
      '%associate%'          => sprintf('<a href="#" onclick="%s">%s</a>', 'sfDoubleList.move(\'unassociated_'.$this->generateId($name).'\', \''.$this->generateId($name).'\'); '.$function.' return false;', $this->getOption('associate')),
      '%unassociate%'        => sprintf('<a href="#" onclick="%s">%s</a>', 'sfDoubleList.move(\''.$this->generateId($name).'\', \'unassociated_'.$this->generateId($name).'\'); '.$function.' return false;', $this->getOption('unassociate')),
      '%associated%'         => $associatedWidget->render($name),
      '%unassociated%'       => $unassociatedWidget->render('unassociated_'.$name),
    )).(($function=='') ? '' :
    sprintf(<<<EOF
<script type="text/javascript">
  %s
</script>
EOF
   , $function));
  }

  /**
   * Gets the JavaScript paths associated with the widget.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavascripts()
  {
    return array('/sfEfWidgetFormExtraPlugin/js/double_list.js');
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
