<?php
/**
 * This class creates and maintains progress bars to be printed to the console.
 * This file is a replica of the ezComponents console progress bar class (@link http://ezcomponents.org/docs/api/latest/ConsoleTools/ezcConsoleProgressbar.html)
 * allows a developer to just use the console progress bar features without the rest of the classes saving all the extra files
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Creating and maintaining progress-bars to be printed to the console.
 * <code>
 * // Create progress bar itself
 * $progress = new ConsoleProgressBar(100);
 * // Perform actions
 * $i = 0;
 * while ( $i++ < 100 )
 * {
 *     // Do whatever you want to indicate progress for
 *     // Advance the progressbar by one step
 *     $progress->advance();
 * }
 * // Finish progress bar and jump to next line.
 * $progress->finish();
 * </code>
 */
class ConsoleProgressBar
{

  /**
   * The current step the progress bar should show.
   * @var int
   */
  protected $currentStep = 0;

  /**
   * The maximum number of steps to go.
   * Calculated once from the settings.
   * @var int
   */
  protected $numSteps = 0;

  /**
   * Indicates if the starting point for the bar has been stored.
   * Per default this is false to indicate that no start position has been
   * stored, yet.
   * @var bool
   */
  protected $started = false;

  /**
   * Whether a position has been stored before, using the storePos() method.
   * @var bool
   */
  protected $positionStored = false;

  /**
   * Container to hold the options
   * @var array(string=>mixed)
   */
  protected $options = array(
    'barChar' => "=",
    'emptyChar' => "-",
    'formatString' => "%act% / %max% [%bar%] %fraction%% %memory%",
    'fractionFormat' => "%01.2f",
    'progressChar' => ">",
    'redrawFrequency' => 1,
    'step' => 1,
    'width' => 100,
    'actFormat' => '%.0f',
    'maxFormat' => '%.0f',
    'max' => 100,
  );

  /**
   * Storage for actual values to be replaced in the format string.
   * Actual values are stored here and will be inserted into the bar
   * before printing it.
   * @var array(string=>string)
   */
  protected $valueMap = array(
    'bar' => '',
    'fraction' => '',
    'act' => '',
    'max' => '',
    'memory' => ''
  );

  /**
   * Stores the bar utilization.
   * This array saves how much space a specific part of the bar utilizes to not
   * recalculate those on every step.
   * @var array(string=>int)
   */
  protected $measures = array(
    'barSpace' => 0,
    'fractionSpace' => 0,
    'actSpace' => 0,
    'maxSpace' => 0,
    'fixedCharSpace' => 0,
  );

  /**
   * Creates a new progress bar.
   *
   * @param int $max Maximum value, where progressbar
   *                                       reaches 100%.
   * @param array(string=>string) $options Options
   */
  public function __construct($max = null, $options = array())
  {
    if( $max )
    {
      $this->options['max'] = $max;
    }

    if( $options && count($options) )
    {
      $this->options = array_merge($this->options, $options);
    }
  }

  /**
   * Set new options.
   * This method allows you to change the options of progressbar.
   *
   * @param $key either an array of key=>value pairs or a string of the option
   * @param $value only needed if the key is a string
   */
  public function setOptions($key, $value = null)
  {
    if( is_array($key) )
    {
      $this->options = array_merge($this->options, $key);
    }
    else
    {
      $this->__set($key, $value);
    }
  }

  /**
   * Returns the current options.
   * Returns the options currently set for this progressbar.
   * @return The current options.
   */
  public function getOptions()
  {
    return $this->options;
  }

  /**
   * Property read access.
   *
   * @param string $key Name of the property.
   *
   * @return mixed Value of the property or null.
   */
  public function __get($key)
  {
    switch($key)
    {
      case 'options':
        return $this->options;
      case 'step':
        // Step is now an option
        return $this->options['step'];
      case 'max':
        return $this->options['max'];
      default:
        if( isset($this->options[$key]) )
        {
          return $this->options[$key];
        }
        break;
    }
    throw new Exception(sprintf("%s does not exists", $key));
  }

  /**
   * Property write access.
   *
   * @param string $key Name of the property.
   * @param mixed $val The value for the property.
   *
   * @throws Exception
   *         If a desired property could not be found.
   */
  public function __set($key, $val)
  {
    switch($key)
    {
      case 'options':
        $this->setOptions($val);
        break;
      case 'max':
        if( (!is_int($val) && !is_float($val)) || $val < 0 )
        {
          throw new Exception(sprintf("%s must be a number greater then 0. Value set: %s", $key, $val));
        }
        break;
      case 'step':
        if( (!is_int($val) && !is_float($val)) || $val < 0 )
        {
          throw new Exception(sprintf("%s must be a number greater then 0. Value set: %s", $key, $val));
        }
        // Step is now an option.
        $this->options['step'] = $val;

        return;
      default:
        throw new Exception(sprintf("%s does not exists", $key));
        break;
    }
    // Changes settings or options, need for recalculating measures
    $this->started = false;
    $this->options[$key] = $val;
  }

  /**
   * Property isset access.
   *
   * @param string $key Name of the property.
   *
   * @return bool True is the property is set, otherwise false.
   */
  public function __isset($key)
  {
    switch($key)
    {
      case 'options':
      case 'max':
      case 'step':
        return true;
    }

    return false;
  }

  /**
   * Start the progress bar
   * Starts the progress bar and sticks it to the current line.
   * No output will be done yet.
   * to print the bar.
   * @return void
   */
  public function start()
  {
    $this->calculateMeasures();
    $this->storePos();
    $this->started = true;
  }

  /**
   * Advance the progress bar.
   * Advances the progress bar by $step steps. Redraws the bar by default,
   * using the output method.
   *
   * @param bool $redraw Whether to redraw the bar immediately.
   * @param int $step How many steps to advance.
   *
   * @return void
   */
  public function advance($redraw = true, $step = 1)
  {
    $this->currentStep += $step;
    if( $redraw === true && $this->currentStep % $this->options['redrawFrequency'] === 0 )
    {
      $this->output();
    }
  }

  public function setValueMap($key, $value)
  {
    $this->valueMap[$key] = $value;
  }

  /**
   * Finish the progress bar.
   * Finishes the bar (jump to 100% if not happened yet,...) and jumps
   * to the next line to allow new output. Also resets the values of the
   * @return void
   */
  public function finish()
  {
    $this->currentStep = $this->numSteps;
    $this->output();
    echo PHP_EOL;
  }

  /**
   * Draw the progress bar.
   * Prints the progress-bar to the screen. If start() has not been called
   * yet, the current line is used for start
   * @return void
   */
  protected function output()
  {
    if( $this->started === false )
    {
      $this->start();
    }

    $this->restorePos();
    if( $this->isWindows() )
    {
      echo str_repeat("\x8", $this->options['width']);
    }

    $this->generateValues();
    echo $this->insertValues();
  }

  /**
   * Stores the current cursor position.
   * Saves the current cursor position to return to it using
   * restorePos. Multiple calls
   * to this method will override each other. Only the last
   * position is saved.
   * @return void
   */
  protected function storePos()
  {
    if( !$this->isWindows() )
    {
      echo "\0337";
      $this->positionStored = true;
    }
  }

  /**
   * Restores a cursor position.
   * Restores the cursor position last saved using storePos.
   * @throws Exception
   *         If no position is saved.
   * @return void
   */

  protected function restorePos()
  {
    if( !$this->isWindows() )
    {
      if( $this->positionStored === false )
      {
        throw new Exception("Progress Bar position was not stored.");
      }
      echo "\0338";
    }
  }

  /**
   * Generate all values to be replaced in the format string.
   * @return void
   */
  protected function generateValues()
  {
    // Bar
    $barFilledSpace = ceil($this->measures['barSpace'] / $this->numSteps * $this->currentStep);
    // Sanitize value if it gets to large by rounding
    $barFilledSpace = $barFilledSpace > $this->measures['barSpace'] ? $this->measures['barSpace'] : $barFilledSpace;
    $bar = $this->strPad(
      $this->strPad(
        $this->options['progressChar'],
        $barFilledSpace,
        $this->options['barChar'],
        STR_PAD_LEFT
      ),
      $this->measures['barSpace'],
      $this->options['emptyChar'],
      STR_PAD_RIGHT
    );
    $this->valueMap['bar'] = $bar;

    // Fraction
    $fractionVal = sprintf(
      $this->options['fractionFormat'],
      ($fractionVal = ($this->options['step'] * $this->currentStep) / $this->options['max'] * 100) > 100 ? 100 : $fractionVal
    );
    $this->valueMap['fraction'] = $this->strPad(
      $fractionVal,
      iconv_strlen(sprintf($this->options['fractionFormat'], 100), 'UTF-8'),
      ' ',
      STR_PAD_LEFT
    );

    // Act / max
    $actVal = sprintf(
      $this->options['actFormat'],
      ($actVal = $this->currentStep * $this->options['step']) > $this->options['max'] ? $this->options['max'] : $actVal
    );
    $this->valueMap['act'] = $this->strPad(
      $actVal,
      iconv_strlen(sprintf($this->options['actFormat'], $this->options['max']), 'UTF-8'),
      ' ',
      STR_PAD_LEFT
    );
    $this->valueMap['max'] = sprintf($this->options['maxFormat'], $this->options['max']);
  }

  /**
   * Insert values into bar format string.
   * @return void
   */
  protected function insertValues()
  {
    $bar = $this->options['formatString'];
    foreach($this->valueMap as $name => $val)
    {
      $bar = str_replace("%{$name}%", $val, $bar);
    }

    return $bar;
  }

  /**
   * Calculate several measures necessary to generate a bar.
   * @return void
   */
  protected function calculateMeasures()
  {
    // Calc number of steps bar goes through
    $this->numSteps = ( int )round($this->options['max'] / $this->options['step']);
    // Calculate measures
    $this->measures['fixedCharSpace'] = iconv_strlen($this->stripEscapeSequences($this->insertValues()), 'UTF-8');
    if( iconv_strpos($this->options['formatString'], '%max%', 0, 'UTF-8') !== false )
    {
      $this->measures['maxSpace'] = iconv_strlen(sprintf($this->options['maxFormat'], $this->options['max']), 'UTF-8');
    }
    if( iconv_strpos($this->options['formatString'], '%act%', 0, 'UTF-8') !== false )
    {
      $this->measures['actSpace'] = iconv_strlen(sprintf($this->options['actFormat'], $this->options['max']), 'UTF-8');
    }
    if( iconv_strpos($this->options['formatString'], '%fraction%', 0, 'UTF-8') !== false )
    {
      $this->measures['fractionSpace'] = iconv_strlen(sprintf($this->options['fractionFormat'], 100), 'UTF-8');
    }
    $this->measures['barSpace'] = $this->options['width'] - array_sum($this->measures);
  }

  /**
   * Strip all escape sequences from a string to measure it's size correctly.
   *
   * @param mixed $str
   *
   * @return void
   */
  protected function stripEscapeSequences($str)
  {
    return preg_replace('/\033\[[0-9a-f;]*m/i', '', $str);
  }

  /**
   * Check if we currently running under windows
   * @return bool
   */
  protected function isWindows()
  {
    return stripos(PHP_OS, 'windows') !== false;
  }

  /**
   * Binary safe str_pad() replacement.
   * This method is a multi-byte encoding safe replacement for the PHP
   * function str_pad().  It mimics exactly the behavior of str_pad(), but
   * uses iconv_* functions with UTF-8 encoding. The parameters received by
   * this method equal the parameters of {@link http://php.net/str_pad
   * str_pad()}. Note: Make sure to hand only UTF-8 encoded content to this
   * method.
   *
   * @param string $input
   * @param int $padLength
   * @param string $padString
   * @param int $padType
   *
   * @return string
   */
  protected function strPad($input, $padLength, $padString = ' ', $padType = STR_PAD_RIGHT)
  {
    $input = (string)$input;

    $strLen = iconv_strlen($input, 'UTF-8');
    $padStrLen = iconv_strlen($padString, 'UTF-8');

    if( $strLen >= $padLength )
    {
      return $input;
    }

    if( $padType === STR_PAD_BOTH )
    {
      return $this->strPad(
        $this->strPad(
          $input,
          $strLen + ceil(($padLength - $strLen) / 2),
          $padString
        ),
        $padLength,
        $padString,
        STR_PAD_LEFT
      );
    }

    $fullStrRepeats = (int)(($padLength - $strLen) / $padStrLen);
    $partlyPad = iconv_substr(
      $padString,
      0,
      (($padLength - $strLen) % $padStrLen)
    );

    $padding = str_repeat($padString, $fullStrRepeats).$partlyPad;

    switch($padType)
    {
      case STR_PAD_LEFT:
        return $padding.$input;
      case STR_PAD_RIGHT:
      default:
        return $input.$padding;
    }
  }
}