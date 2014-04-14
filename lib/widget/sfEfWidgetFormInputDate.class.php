<?php

/**
 * sfEfWidgetFormInputDate represents a date widget rendered by JQuery UI.
 *
 * This widget needs JQuery and JQuery UI to work.
 *
 * @package    sfEfWidgetFormExtraPlugin
 * @subpackage widget
 * @author     Yaismel Miranda Pons <yaismelmp@googlemail.com>
 * @version    SVN: $Id$
 */
class sfEfWidgetFormInputDate extends sfWidgetFormInput
{
  /**
   * Configures the current widget.
   *
   * Available options:
   *
   */
  protected function configure($options = array(), $attributes = array())
  {
    $culture = sfContext::getInstance()->getUser()->getCulture();
    $this->addOption('config', '{}');
    $this->addOption('culture', $culture);

    parent::configure($options, $attributes);
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The date displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetFormInput
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $dirButton = '/sfEfWidgetFormExtraPlugin/images/calendar.png';
    return parent::render($name, $value, $attributes, $errors).
           sprintf(<<<EOF
<script type="text/javascript">
  jQuery(document).ready(function() {
    jQuery("#%s").datepicker({
      showAnim: 'show',
      showOn: 'button',
      buttonImage: '%s',
      buttonImageOnly: true,
      dateFormat: "yy-mm-dd",
      yearRange: '-80:+3',
      changeMonth: true,
      changeYear: true
    })
    $.datepicker.setDefaults($.datepicker.regional['%s']);
  });
</script>
EOF
      ,
      $this->generateId($name),
      $dirButton,
      $this->getOption('culture')
    );
  }

  /**
   * Gets the JavaScript paths associated with the widget.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavascripts()
  {
    return array(sfConfig::get('sf_jquery_ui_jspath').'/i18n/jquery.ui.datepicker-'.$this->getOption('culture').'.js' => sfConfig::get('sf_jquery_ui_jspath').'/i18n/jquery.ui.datepicker-'.$this->getOption('culture').'.js');
  }
}
