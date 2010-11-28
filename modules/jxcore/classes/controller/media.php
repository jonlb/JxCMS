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
class Controller_Media extends Controller
{
	// boolean flag for triggering an exception for missing files
	protected $error_on_missing;
	// set allowed image extensions
	protected $img_extensions = array('jpg','png','gif');

    protected $file;
    protected $extension;

    public function before() {
        // if IN_PRODUCTION then don't display errors for missing files
		$this->error_on_missing = ( IN_PRODUCTION ) ? FALSE : TRUE ;
        $this->file = $this->request->param('file');
        $this->extension = $this->request->param('ext', null);
        return parent::before();
    }
	/**
	 * Public method for loading css files
	 * Usage: /media/css/style  or /media/css/style.css
	 */
	public function action_css()
	{
		// grab requested css file while stripping extension
		$css = $this->strip_extension($this->file, 'css');
		// if file is present then display
        $path = $this->find_file( 'css'.DS.$css, 'css' );
		if($path){
			$this->display_file( $path, 'css' );
		}
	}
	/**
	 * Public method for loading js files
	 * Usage: /media/js/myscript or /media/js/myscript.js
	 */
	public function action_js()
	{
		// grab requested js file while stripping extension
		$js = $this->strip_extension($this->file, 'js');
		// if file is present then display
		$path = $this->find_file( 'js'.DS.$js, 'js' );
        if ($path) {
			$this->display_file( $path, 'js' );
		}
	}
	/**
	 * Public method for loading image files
	 * Usage: /media/image/myimg  or /media/image/myimg.jpg
	 */
	public function action_image()
	{
		// grab requested image while stripping allowed image extensions
		$img = $this->strip_extensions($this->file, $this->img_extensions );
		// returns false if image file is not found
		// returns the extension of valid image file if found
        if (is_null($this->extension)) {
		    $img_ext = $this->find_image_file( $img );
        } else {
            $img_ext = $this->extension;
        }

        $img = $this->find_file('images'.DS.$img, $img_ext);
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
	 * Protected method. finds the needed file. It will search the
     * theme directories first, then the loader configs, then finally
     * uses Kohana::find_file in the media and views directories.
	 *
	 * @param string $file		file you want to find without extension
	 * @param string $ext		file extension to look for
	 * @return full path to file if found. returns FALSE if not found
	 */
	protected function find_file( $file, $ext ){
		$theme = Jx_Theme::get_current_theme_dir();
        $path = Kohana::find_file('views',$theme.DS.$file,$ext);

        if (!$path) {
            $path = Kohana::find_file('media',$file,$ext);
            if (!$path) {
                $path = Kohana::find_file('views',$file,$ext);
                if (!$path) {
                    $path = Kohana::find_file('views','media'.DS.$file,$ext);
                }
            }
        }

        return $path;
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
			if( Kohana::find_file( 'media', $file, FALSE, $ext ) != FALSE ){
				// if found capture the ext which worked
				$img_ext = $ext;
			}
		}
		// if no image was found recall kohana find file to trigger error
		if( $img_ext == FALSE ) {
			Kohana::find_file( 'media', $file, $this->error_on_missing, $ext );
		}
		return $img_ext;
	}
	/**
	 * Method sets appropriate header and displays the file
	 *
	 * @param string $file	the full path to the file to be displayed
     * @param string $ext   the extension/type of the file
	 */
	protected function display_file( $file, $ext){
		// set header
        //Jx_Debug::dump($ext, 'extension');
        $mime = "mimes.$ext";
        $config = Kohana::config($mime);
        $content_type = reset($config);
        //Jx_Debug::dump($content_type, 'content type');
		header('Content-type: ' . $content_type );
		// include file
		include $file;
        exit(); //stop so that we don't get the wrong content-type.
	}
}
