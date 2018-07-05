<?php

/**
 * @file
 *
 * BrowserSizeContext Context for Behat.
 *
 */

namespace lexsoft\DrupalBehatContexts;

use Behat\Behat\Context\SnippetAcceptingContext;
use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Context class to modify browser window size.
 *
 * This context allows easy browser window size change to a predefined sizes.
 * This context needs a browser driver (Selenium2 for example).
 *
 * Context params:
 *   'sizes': An array of predefined sizes. Each entry is a key-value pair with
 *     the key eing the size name and the value and array with the width and
 *     the height for taht size. @see __construct().
 */
class BrowserSizeContext extends RawDrupalContext implements SnippetAcceptingContext {

  /**
   * Context parameters.
   *
   * @var array
   */
  protected $customParameters;

  /**
   * Constructor.
   *
   * Save class params, if any.
   *
   * @param array $parameters
   */
  public function __construct($parameters) {
    // Default values.
    $this->customParameters = array(
      'sizes' => array(
        'Default'  => array('width' => 1200, 'height' => 800),
        'Full'     => array('width' => 1200, 'height' => 800),
        'Tablet H' => array('width' => 1024, 'height' => 768),
        'Tablet V' => array('width' => 800, 'height' => 1024),
        'Mobile H' => array('width' => 650, 'height' => 370),
        'Mobile V' => array('width' => 350, 'height' => 650),
      ),
    );

    // Collect received parameters.
    if (!empty($parameters)) {
      // Filter any invalid parameters.
      $parameters_filtered = array_intersect_key($parameters, $this->customParameters);

      // Apply parameters.
      $this->customParameters = array_replace_recursive($this->customParameters, $parameters_filtered);
    }
  }

  /**
   * Step to resize the window to a given size.
   *
   * This step is executed only if the stage has the tag @javascript.
   *
   * @See $defaults
   *
   * @Given (that ) browser window size is :size size
   */
  public function browserWindowSizeIs($size) {
    if (array_key_exists($size, $this->customParameters['sizes'])) {
      $size = $this->customParameters['sizes'][$size];
      $this->getSession()->resizeWindow($size['width'], $size['height'], 'current');
      print_r("Browser Window Size: " . $size['width'] . "x" . $size['height'] . " px");
    }
    else {
      $sizes = array();
      foreach ($this->customParameters['sizes'] as $size_name => $size_dimensions) {
        $sizes[] = "\"$size_name\" (" . $size_dimensions['width'] . "x" . $size_dimensions['height'] . ')';
      }
      throw new \InvalidArgumentException("Unknown size $size. It should be one of: " . implode(", " , $sizes));
    }
  }
}
