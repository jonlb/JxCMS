<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Loads a default set of filters and extensions for
 * Twig based on Kohana helpers
 *
 * @package kohana-twig
 * @author Jonathan Geiger
 */
class Jx_Twig_Extensions extends Twig_Extension
{
	/**
	 * Returns the added token parsers
	 *
	 * @return array
	 * @author Jonathan Geiger
	 */
	public function getTokenParsers()
	{
		return array(
			new Jx_Twig_Settings_TokenParser(),
            new Jx_Twig_Assets_TokenParser()

		);
	}


	/**
	 * @return string
	 * @author Jonathan Geiger
	 */
	public function getName()
	{
		return 'jx_twig';
	}
}
