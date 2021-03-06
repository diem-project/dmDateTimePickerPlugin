<?php

/**
 * sfWidgetFormDate represents a date widget.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormDate.class.php 16259 2009-03-12 11:42:00Z fabien $
 */
class sfWidgetFormDmDateTime extends sfWidgetFormI18nDate
{

  /**
   * @param  string $name        The element name
   * @param  string $value       The date displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */

  protected function configure($options = array(), $attributes = array())
  {
    $options['culture'] = isset($options['culture']) ? $options['culture'] : sfDoctrineRecord::getDefaultCulture();

    parent::configure($options, $attributes);

    $this->setOption('culture', $options['culture']);
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    if($value && strtotime($value))
    {
      // convert value to an array
      $default = array('year' => null, 'month' => null, 'day' => null, 'hour' => null, 'minute' => null);

      $value = (string) $value == (string) (integer) $value ? (integer) $value : strtotime($value);
      if (false === $value)
      {
        $value = $default;
      }
      else
      {
        $value = array('year' => date('Y', $value), 'month' => date('n', $value), 'day' => date('j', $value), 'hour' => date('H', $value), 'minute' => date('i', $value));
      }

      $formattedValue = strtr(
        $this->getOption('format'),
        array(
          '%year%' => sprintf('%02d', $value['year']),
          '%month%' => sprintf('%02d', $value['month']),
          '%day%' => sprintf('%02d', $value['day']),
          '%hour%' => sprintf('%02d', $value['hour']),
          '%minute%' => sprintf('%02d', $value['minute']),
        )
      );
    }
    else
    {
      $formattedValue = $value;
    }

    return $this->renderTag(
      'input',
      array(
        'type' => 'text',
        'name' => $name,
        'size' => isset($attributes['size']) ? $attributes['size'] : 16,
        'id' => $this->generateId($name),
        'class' => 'datetimepicker_me' . (isset($attributes['class']) ? ' ' . $attributes['class'] : ''),
        'value' => $formattedValue
      )
    );
  }

  protected function getDateFormat($culture)
  {
    $dateFormat = sfDateTimeFormatInfo::getInstance($culture)->getShortDatePattern() . ' ' . sfDateTimeFormatInfo::getInstance($culture)->getShortTimePattern();

    if (false === ($dayPos = stripos($dateFormat, 'd')) || false === ($monthPos = strpos($dateFormat, 'M')) || false === ($yearPos = stripos($dateFormat, 'y'))
      || false === ($hourPos = stripos($dateFormat, 'H')) || false === ($minutePos = strpos($dateFormat, 'm')))
    {
      return $this->getOption('format');
    }

    return strtr($dateFormat, array(
        substr($dateFormat, $dayPos,   strripos($dateFormat, 'd') - $dayPos + 1)   => '%day%',
        substr($dateFormat, $monthPos, strrpos($dateFormat, 'M') - $monthPos + 1) => '%month%',
        substr($dateFormat, $yearPos,  strripos($dateFormat, 'y') - $yearPos + 1)  => '%year%',
        substr($dateFormat, $hourPos,  strripos($dateFormat, 'H') - $hourPos + 1)  => '%hour%',
        substr($dateFormat, $minutePos,  strrpos($dateFormat, 'm') - $minutePos + 1)  => '%minute%',
    ));
  }


  public function getJavascripts()
  {
    $javascripts = array('dmDateTimePickerPlugin.datetimepicker');

//    if('en' !== $this->getOption('culture'))
//    {
//      $javascripts[] = 'lib.ui-i18n';
//    }

    return array_merge(parent::getJavascripts(), $javascripts);
  }

  public function getStylesheets()
  {
    return array_merge(parent::getStylesheets(), array(
      'dmDateTimePickerPlugin.datetimepicker' => null
    ));
  }
}

