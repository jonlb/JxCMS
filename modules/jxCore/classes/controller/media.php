<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Loads css, js, png, jpg or gif files located in
 * application/views/media
 *
 * Examples:
 *
 * URL :: http://example.com/index.php/media/js/mootools
 * Loads :: application/views/media/js/mootools.js
 *
 * URL :: http://example.com/index.php/media/css/style
 * Loads :: application/views/media/css/style.css
 *
 * URL :: http://example.com/index.php/media/image/loading
 * Loads :: application/views/media/image/loading.gif
 *
 */
class Media_Controller extends Controller
{
	// boolean flag for triggering an exception for missing files
	protected $error_on_missing;
	// set allowed image extensions
	protected $img_extensions = array('jpg','png','gif');

	public function __construct()
	{
		// if IN_PRODUCTION then don't display errors for missing files
		$this->error_on_missing = ( IN_PRODUCTION ) ? FALSE : TRUE ;
	}

	/**
	 * Public method for loading css files
	 * Usage: /media/css/style  or /media/css/style.css
	 */
	public function css()
	{
		// grab requested css file while stripping extension
		$css = $this->strip_extension(url::current(), 'css');
		// if file is present then display
		if( $this->find_file( $css, 'css' ) ){
			$this->display_file( $css, 'css' );
		}
	}
	/**
	 * Public method for loading js files
	 * Usage: /media/js/myscript or /media/js/myscript.js
	 */
	public function js()
	{
		// grab requested js file while stripping extension
		$js = $this->strip_extension(url::current(), 'js');
		// if file is present then display
		if( $this->find_file( $js, 'js' ) ){
			$this->display_file( $js, 'js' );
		}
	}
	/**
	 * Public method for loading image files
	 * Usage: /media/image/myimg  or /media/image/myimg.jpg
	 */
	public function image()
	{
		// grab requested image while stripping allowed image extensions
		$img = $this->strip_extensions(url::current(), $this->img_extensions );
		// returns false if image file is not found
		// returns the extension of valid image file if found
		$img_ext = $this->find_image_file( $img );
		// if image file was found with valid extension then display
		if( $img_ext != FALSE ){
			$this->display_file( $img, $img_ext );
		}

	}
	/**
	 * protected method for stripping extension off a string
	 *
	 * @param string $string	string to be processed
	 * @param string $ext		extension to strip off string
	 * @return string		 	returns string with extension stripped
	 */
	protected function strip_extension($string, $ext)
	{
		$ext_pos = 0 - strlen( '.'.$ext );
		$has_ext = ( substr($string,$ext_pos) == '.'.$ext ) ? TRUE : FALSE ;
		return ( $has_ext ) ? substr($string,0,$ext_pos) : $string;
	}
	/**
	 * protected method for stripping an array of extensions from a string
	 *
	 * @param string $string 	string to be processed
	 * @param array $exts		array of extensions to strip from string
	 * @return string			returns string with extensions stripped
	 */
	protected function strip_extensions( $string, $exts )
	{
		foreach( $exts as $ext ){
			$string = $this->strip_extension( $string, $ext );
		}
		return $string;
	}
	/**
	 * Protected method. Uses the Kohana::find_file method to located
	 * file. Cascading should be supported
	 *
	 * @param string $file		file you want to find without extension
	 * @param string $ext		file extension to look for
	 * @return full path to file if found. returns FALSE if not found
	 */
	protected function find_file( $file, $ext ){
		return Kohana::find_file( 'views', $file, $this->error_on_missing, $ext );
	}
	/**
	 * Find image file. Images could have multiple extensions.
	 * Method loops over allowed image extensions and uses the Kohana::find_file
	 * feature to locate the image
	 *
	 * @param string $file
	 * @return string returns the extension of the found image or false if not found
	 */
	protected function find_image_file( $file ){
		$img_ext = FALSE;
		// loop over image extensions
		foreach( $this->img_extensions as $ext ){
			// use kohana's cascading to find file. errors are supressed here.
			if( Kohana::find_file( 'views', $file, FALSE, $ext ) != FALSE ){
				// if found capture the ext which worked
				$img_ext = $ext;
			}
		}
		// if no image was found recall kohana find file to trigger error
		if( $img_ext == FALSE ) {
			Kohana::find_file( 'views', $file, $this->error_on_missing, $ext );
		}
		return $img_ext;
	}
	/**
	 * Method sets appropriate header and displays the file
	 *
	 * @param string $file	file to be displayed without extension
	 * @param string $ext	extension of the file to be displayed
	 */
	protected function display_file( $file, $ext ){
		// set header
		header('Content-Type: ' . reset(Kohana::config("mimes.$ext")) );
		// include file
		include Kohana::find_file('views', $file, FALSE, $ext );
	}
}
