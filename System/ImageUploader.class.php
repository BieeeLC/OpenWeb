<?php
/**
 * BULLETPROOF,  
 *
 * This is a one-file solution for a quick and safe way of
 * uploading, watermarking, cropping and resizing images
 * during and after uploads with PHP with best security.
 *
 * This class is heavily commented, to be as much friendly as possible.
 * Please help out by posting out some bugs/flaws if you encounter any. Thanks!
 *
 * @category    Image uploader
 * @package     BulletProof
 * @version     1.4.0
 * @author      samayo 
 * @link        https://github.com/samayo/BulletProof
 * @license     Luke 3:11 ( Free )
 */
 
namespace ImageUploader;

class ImageUploaderException extends \Exception {}

class BulletProof
{

    /*
    |--------------------------------------------------------------------------
    | Image Upload Properties
    \--------------------------------------------------------------------------*/

    /**
     * Set a group of default image types to upload.
     * @var array
     */
    protected $imageType = array("bmp", "jpg", "jpeg", "png", "gif");

    /**
     * Set a default file size to upload. Values are in bytes. Remember: 1kb ~ 1000 bytes.
     * @var array
     */
    protected $imageSize = array("min" => 1, "max" => 30000);

    /**
     * Set a default min & maximum height & width for image to upload.
     * @var array
     */
    protected $imageDimension = array("height"=>1000, "width"=>1000);

    /**
     * Set a default folder to upload images, if it does not exist, it will be created.
     * @var string
     */
    protected $uploadDir = "uploads";
    
    /**
     * To get the real image/mime type. i.e gif, jpeg, png, ....
     * @var string
     */
    protected $getMimeType;


    /*
    |--------------------------------------------------------------------------
    | Image Resize and Crop Properties
    \------------------------------------------------------------------------*/

    /**
     * Image dimensions for resizing or shrinking ex: array("height"=>100, "width"=>100)
     * @var array
     */
    protected $shrinkImageTo = array();

    /**
     * Whether or not to keep the ratio of the original image while resizing
     * @var boolean
     */
    protected $shrinkRatio;

    /**
     * Whether or not to allow upsizing of an image when applying the shrink command.
     * @var boolean
     */
    protected $shrinkUpsize;

    /**
     * New image dimensions for image cropping ex: array("height"=>100, "width"=>100)
     * @var array
     */
    protected $cropImageTo  = array();


    /*
    |--------------------------------------------------------------------------
    | Image Watermark and Crop Properties
    \-------------------------------------------------------------------------*/

    /**
     * Name of the image to use as a watermark. ( best to use a png  image )
     * @var string
     */
    protected $getWatermark;

    /**
     * Watermark Position. (Where to put the watermark). ex: 'center', 'top-right', 'bottom-left'....
     * @var string
     */
    protected $getWatermarkPosition;

    /**
     * Size ( Width & Height ) of the watermark ex: 'array("height"=>40, "width"=>20)'.
     * @var array
     */
    protected $getWatermarkDimensions;


    /*
    |--------------------------------------------------------------------------
    | Image Upload Methods
    \--------------------------------------------------------------------------*/

    /**
     * Stores image types to upload
     *
     * @param array $fileTypes -  ex: ['jpg', 'doc', 'txt'].
     * @return $this
     */
    public function fileTypes(array $fileTypes)
    {
        $this->imageType = $fileTypes;
        return $this;
    }

    /**
     * Minimum and Maximum allowed image size for upload (in bytes),
     *
     * @param array $fileSize - ex: ['min'=>500, 'max'=>1000]
     * @return $this
     */
    public function limitSize(array $fileSize)
    {
        $this->imageSize = $fileSize;
        return $this;
    }

    /**
     * Default & maximum allowed height and width image to download.
     *
     * @param array $dimensions
     * @return $this
     */
    public function limitDimension(array $dimensions){
        $this->imageDimension = $dimensions;
        return $this;
    }

    /**
     * Get the real image's Extension/mime type
     *
     * @param $imageName
     * @return mixed
     * @throws ImageUploaderException
     */
    protected function getMimeType($imageName)
    {   
        if(!file_exists($imageName))
            throw new ImageUploaderException("Image " . $imageName . " does not exist");

        $listOfMimeTypes = array(
        1 => "gif", "jpeg", "png",  "swf", "psd",
             "bmp", "tiff", "tiff", "jpc", "jp2",
             "jpx", "jb2",  "swc",  "iff", "wbmp",
             "xmb", "ico"
        );

        if(isset($listOfMimeTypes[ exif_imagetype($imageName) ])){
            return $listOfMimeTypes[ exif_imagetype($imageName) ];
        }
    }

    /**
     * Handy method for getting image dimensions (W & H) in pixels.
     *
     * @param $getImage - The image name
     * @return array
     */
    protected function getPixels($getImage)
    {
        list($width, $height) = getImageSize($getImage);
        return array("width"=>$width, "height"=>$height);
    }

    /**
     * Calculate the new size of the image.
     * Has the ability to keep the original ratio of the image. Can prevent upsizing of an image.
     *
     * @param array $oldImage
     * @return array
     */
    protected function getNewImageSize($oldImage)
    {

        // If the ratio needs to be kept.
        if ($this->shrinkRatio) {
            $width = $this->shrinkImageTo["width"];
            // First, calculate the height.
            $height = intval($width / $oldImage["width"] * $oldImage["height"]);

            // If the height is too large, set it to the maximum height and calculate the width.
            if ($height > $this->shrinkImageTo["height"]) {

                $height = $this->shrinkImageTo["height"];
                $width = intval($height / $oldImage["height"] * $oldImage["width"]);
            }

            // If we don't allow upsizing check if the new width or height are too big.
            if (! $this->shrinkUpsize) {
                // If the given width is larger then the image height, then resize it.
                if ($width > $oldImage["width"]) {
                    $width = $oldImage["width"];
                    $height = intval($width / $oldImage["width"] * $oldImage["height"]);
                }

                // If the given height is larger then the image height, then resize it.
                if ($height > $oldImage["height"]) {
                    $height = $oldImage["height"];
                    $width = intval($height / $oldImage["height"] * $oldImage["width"]);
                }
            }

        } else {
            $width = $this->shrinkImageTo["width"];
            $height = $this->shrinkImageTo["height"];
        }

        return array(
            "width" => $width,
            "height" => $height
        );
    }

    /**
     * Rename file either from method or by generating a random one.
     *
     * @param $isNameProvided - A new name for the file. 
     * @return string
     */
    protected function imageRename($isNameProvided)
    {
        $theMime = $this->getMimeType;
		if($theMime == "jpeg") $theMime = "jpg";
		
		if ($isNameProvided)
		{
			return $isNameProvided . "." . $theMime;
        }
        return uniqid(true)."_".str_shuffle(implode(range("E", "Q"))) . "." . $theMime;
    }

    /**
     * Get the specified upload dir, if it does not exist, create a new one.
     *
     * @param $directoryName - directory name where you want your files to be uploaded
     * @return $this
     * @throws ImageUploaderException
     */
    public function uploadDir($directoryName)
    {
        if (!file_exists($directoryName) && !is_dir($directoryName)) {
            $createFolder = mkdir("" . $directoryName, 0666, true);
            if (!$createFolder) {
                throw new ImageUploaderException("Folder " . $directoryName . " could not be created");
            }
        }
        $this->uploadDir = $directoryName;
        return $this;
    }

    /**
     * For getting common error messages from FILES[] array during upload.
     *
     * @return array
     */
    protected function commonUploadErrors($key)
    {
        $uploadErrors = array(
            UPLOAD_ERR_OK           => "...",
            UPLOAD_ERR_INI_SIZE     => "File is larger than the specified amount set by the server",
            UPLOAD_ERR_FORM_SIZE    => "File is larger than the specified amount specified by browser",
            UPLOAD_ERR_PARTIAL      => "File could not be fully uploaded. Please try again later",
            UPLOAD_ERR_NO_FILE      => "File is not found",
            UPLOAD_ERR_NO_TMP_DIR   => "Can't write to disk, due to server configuration ( No tmp dir found )",
            UPLOAD_ERR_CANT_WRITE   => "Failed to write file to disk. Please check you file permissions",
            UPLOAD_ERR_EXTENSION    => "A PHP extension has halted this file upload process"
        );

        return $uploadErrors[$key];
    }


    /*
    |--------------------------------------------------------------------------
    | Image Watermark Methods
    \--------------------------------------------------------------------------*/

    /**
     * Get the watermark image and its position.
     *
     * @param $watermark - the watermark name, ex: 'logo.png'
     * @param $watermarkPosition - position to put the watermark, ex: 'center'
     * @return $this
     * @throws ImageUploaderException
     */
    public function watermark($watermark, $watermarkPosition = null)
    {
        if (!file_exists($watermark)) {
            throw new ImageUploaderException(" Please provide valid image to use as watermark ");
        }
        $this->getWatermark = $watermark;
        $this->getWatermarkPosition = $watermarkPosition;
        return $this;
    }

    /**
     * Calculate position and apply image watermark.
     *
     * The objective is to let position of watermarking be passed in simple English words like:
     * 'center', 'right-top', 'bottom-left'.. as the second argument for the 'watermark()' method
     * then take that word and do the real offset & marginal-calculation in this method.
     *
     * @param $imageName
     * @throws ImageUploaderException
     */
    protected function applyWatermark($imageName)
    {
        if (!$this->getWatermark) {
            return ;
        }

        // Calculate the watermark position
        $image      = $this->getPixels($imageName); 
        $watermark  = $this->getPixels($this->getWatermark);

        switch ($this->getWatermarkPosition) {
            case "center":
                $marginBottom  =   round($image["height"] / 2);
                $marginRight   =   round($image["width"] / 2) - round($watermark["width"] / 2);
                break;

            case "top-left":
                $marginBottom  =   round($image["height"] - $watermark["height"]);
                $marginRight   =   round($image["width"] - $watermark["width"]);
                break;

            case "bottom-left":
                $marginBottom  =   5;
                $marginRight   =   round($image["width"] - $watermark["width"]);
                break;

            case "top-right":
                $marginBottom  =   round($image["height"] - $watermark["height"]);
                $marginRight   =   5;
                break;

            default:
                $marginBottom  =   2;
                $marginRight   =   2;
                break;
        }

        // Apply the watermark using the calculated position
        $this->getWatermarkDimensions = $this->getPixels($this->getWatermark);

        $imageType = $this->getMimeType($imageName);
        $watermark = imagecreatefrompng($this->getWatermark);


        switch ($imageType) {
            case "jpeg":
            case "jpg":
                $createImage = imagecreatefromjpeg($imageName);
                break;

            case "png":
                $createImage = imagecreatefrompng($imageName);
                break;

            case "gif":
                $createImage = imagecreatefromgif($imageName);
                break;

            default:
                $createImage = imagecreatefromjpeg($imageName);
                break;
        }

        $sx = imagesx($watermark);
        $sy = imagesy($watermark);
        imagecopy(
            $createImage,
            $watermark,
            imagesx($createImage) - $sx - $marginRight,
            imagesy($createImage) - $sy - $marginBottom,
            0,
            0,
            imagesx($watermark),
            imagesy($watermark)
        );
    

        switch ($imageType) {
            case "jpeg":
            case "jpg":
                 imagejpeg($createImage, $imageName);
                break;

            case "png":
                 imagepng($createImage, $imageName);
                break;

            case "gif":
                 imagegif($createImage, $imageName);
                break;

            default:
                throw new ImageUploaderException("A watermark can only be applied to: jpeg, jpg, gif, png images ");
                break;
        }
    }
    

    /*
    |--------------------------------------------------------------------------
    | Image Shrink/Resize Properties
    \--------------------------------------------------------------------------*/

    /**
     * Get the Width and Height of the image image to shrink (in pixels)
     *
     * @param array $setImageDimensions
     * @return $this
     */
    public function shrink(array $setImageDimensions, $ratio = false, $upsize = true)
    {
        $this->shrinkImageTo = $setImageDimensions;
        $this->shrinkRatio = $ratio;
        $this->shrinkUpsize = $upsize;
        return $this;
    }

    /**
     * Shrink the image.
     *
     * @param $fileName - the file name
     * @param $imageName - the file to upload
     * @throws ImageUploaderException
     */
    protected function applyShrink($fileName, $imageName)
    {
        if (!$this->shrinkImageTo)
            return;
			
		$oldImage = $this->getPixels($imageName);
        $newImage = $this->getNewImageSize($oldImage, false);
		
		$mimeType = $this->getMimeType($fileName);
		
		if($mimeType == "gif")
		{
			if(!is_dir($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "GifFrames"))
				mkdir($_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "GifFrames",0777);
			
			$gr = new gifresizer;
			$gr->temp_dir = $_SERVER['DOCUMENT_ROOT'] . "/" . $_SESSION['SiteFolder'] . "GifFrames";
			$gr->resize($imageName,$fileName,$newImage["width"], $newImage["height"]);
			return;
		}

        $imgString	= file_get_contents($imageName);
        $image		= imagecreatefromstring($imgString);
        $tmp		= imagecreatetruecolor($newImage["width"], $newImage["height"]);
		
        imagecopyresampled($tmp,$image,0,0,0,0,$newImage["width"],$newImage["height"],$oldImage["width"],$oldImage["height"]);

        switch ($mimeType) {
            case "jpeg":
            case "jpg":
                imagejpeg($tmp, $imageName, 100);
                break;
            case "png":
                imagepng($tmp, $imageName, 0);
                break;
            /*case "gif":
                imagegif($tmp, $imageName);
                break;*/
            default:
                throw new ImageUploaderException(" Only jpg, jpeg, png and gif files can be resized ");
                break;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Image Crop Methods. 
    \--------------------------------------------------------------------------*/

    /**
     * Get size dimensions to use for new image cropping
     *
     * @param array $imageCropValues
     * @return $this
     */
    public function crop(array $imageCropValues)
    {
        $this->cropImageTo = $imageCropValues;
        return $this;
    }

    /**
     * Apply crop image, from the given size
     *
     * @param $imageName
     * @param $tmp_name
     * @return resource
     * @throws ImageUploaderException
     */
    protected function applyCrop($imageName, $tmp_name)
    {

        if (!$this->cropImageTo) {
            return ;
        }

        $mimeType = $this->getMimeType($imageName);

        switch ($mimeType) {
            case "jpg":
            case "jpeg":
                $imageCreate = imagecreatefromjpeg($tmp_name);
                break;

            case "png":
                $imageCreate = imagecreatefrompng($tmp_name);
                break;

            case "gif":
                $imageCreate = imagecreatefromgif($tmp_name);
                break;

            default:
                throw new ImageUploaderException(" Only gif, jpg, jpeg and png files can be cropped ");
                break;
        }

        // Uploaded image pixels.
        $image = $this->getPixels($tmp_name);
        $crop = $this->cropImageTo;

        // The image offsets/coordination to crop the image.
        $widthTrim = ceil(($image["width"] - $crop["width"]) / 2);
        $heightTrim = ceil(($image["height"] - $crop["height"]) / 2);

        // Can't crop a 100X100 image, to 200X200. Image can only be cropped to smaller size.
        if ($widthTrim < 0 && $heightTrim < 0) {
            return ;
        }

        $temp = imagecreatetruecolor($crop["width"], $crop["height"]);
                imagecopyresampled(
                    $temp,
                    $imageCreate,
                    0,
                    0,
                    $widthTrim,
                    $heightTrim,
                    $crop["width"],
                    $crop["height"],
                    $crop["width"],
                    $crop["height"]
                );


        if (!$temp) {
            throw new ImageUploaderException("Failed to crop image. Please pass the right parameters");
        } else {
            imagejpeg($temp, $tmp_name);
        }

    }


    /*
    |--------------------------------------------------------------------------
    | Not Upload Related. 
    \--------------------------------------------------------------------------*/

    /**
     * Without uploading, just crop/watermark/shrink all images in your folders
     *
     * @param $action - the task.. ex: 'crop', 'watermark', 'shrink'...
     * @param $imageName - the image you want to change. Provide full path pls.
     * @throws ImageUploaderException
     */
    public function change($action, $imageName){

        if(empty($action) || !file_exists($imageName)){
            throw new ImageUploaderException(__FUNCTION__." needs two arguments. the Task and Image name");
        }

        if($action == "watermark" && 
            $this->getWatermark)
        {
            $this->applyWatermark($imageName);
            return true;
        }

        if($action == "shrink" &&
            $this->shrinkImageTo)
        {
            $this->applyShrink($imageName, $imageName);
            return true;
        }

        if($action == "crop" && 
            $this->cropImageTo)
        {
            $this->applyCrop($imageName, $imageName);
            return true;
        }
        
        throw new ImageUploaderException("Unknown directive given to function ". __FUNCTION__);
        
    }

    /**
     * Simple file check and delete wrapper.
     *
     * @param $fileToDelete
     * @return bool
     * @throws ImageUploaderException
     */
    public function deleteFile($fileToDelete){
        if (file_exists($fileToDelete) && !unlink($fileToDelete)) {
            throw new ImageUploaderException("File may have been deleted or does not exist");
        }
        return true;
    }

    /**
     * Final image uploader method, to check for errors and upload
     *
     * @param $fileToUpload
     * @param null $isNameProvided
     * @return string
     * @throws ImageUploaderException
     */
	public function upload($fileToUpload, $isNameProvided = null, $index = false)
	{
		if(!function_exists('exif_imagetype'))
			throw new ImageUploaderException("Function 'exif_imagetype' Not found.");

		// Check if any errors are thrown by the FILES[] array
		if( $index !== false )
		{
			if ($fileToUpload["error"][$index])
			{
	            throw new ImageUploaderException($this->commonUploadErrors($fileToUpload["error"][$index]));
			}
		}
		else
		{
	        if ($fileToUpload["error"])
			{
	            throw new ImageUploaderException($this->commonUploadErrors($fileToUpload["error"]));
			}
		}

        // First get the real file extension
		if( $index !== false )
        	$this->getMimeType = $this->getMimeType($fileToUpload["tmp_name"][$index]);
		else
			$this->getMimeType = $this->getMimeType($fileToUpload["tmp_name"]);

        // Check if this file type is allowed for upload
        if (!in_array($this->getMimeType, $this->imageType))
            throw new ImageUploaderException(" This is not allowed file type! Please only upload ( " . implode(", ", $this->imageType) . " ) file types");

        //Check if size (in bytes) of the image are above or below of defined in 'limitSize()' 
		if( $index !== false )
		{
	        if ($fileToUpload["size"][$index] < $this->imageSize["min"] || $fileToUpload["size"][$index] > $this->imageSize["max"])
			{
    	        throw new ImageUploaderException("File sizes must be between " . implode(" to ", $this->imageSize) . " bytes");
			}
		}
		else
		{
			if ($fileToUpload["size"] < $this->imageSize["min"] || $fileToUpload["size"] > $this->imageSize["max"])
			{
    	        throw new ImageUploaderException("File sizes must be between " . implode(" to ", $this->imageSize) . " bytes");
			}
		}
        
        // check if image is valid pixel-wise.
		if( $index !== false )
	        $pixel = $this->getPixels($fileToUpload["tmp_name"][$index]);
		else
			$pixel = $this->getPixels($fileToUpload["tmp_name"]);
        
        if($pixel["width"] < 4 || $pixel["height"] < 4)
            throw new ImageUploaderException("This file is either too small or corrupted to be an image");

        if($pixel["height"] > $this->imageDimension["height"] || $pixel["width"] > $this->imageDimension["width"])
            throw new ImageUploaderException("Image pixels/size must be below ". implode(", ", $this->imageDimension). " pixels");

        // Assign given name or generate a new one.
        $newFileName = $this->imageRename($isNameProvided);

        // create upload directory if it does not exist
        $this->uploadDir($this->uploadDir);

        // watermark, shrink and crop
		if( $index !== false )
		{
			$this->applyWatermark($fileToUpload["tmp_name"][$index]);
			$this->applyShrink($fileToUpload["tmp_name"][$index], $fileToUpload["tmp_name"][$index]);
			$this->applyCrop($fileToUpload["tmp_name"][$index], $fileToUpload["tmp_name"][$index]);
		}
		else
		{
			$this->applyWatermark($fileToUpload["tmp_name"]);
			$this->applyShrink($fileToUpload["tmp_name"], $fileToUpload["tmp_name"]);
			$this->applyCrop($fileToUpload["tmp_name"], $fileToUpload["tmp_name"]);			
		}

        // Security check, to see if file was uploaded with HTTP_POST
		if( $index !== false )
	        $checkSafeUpload = $this->isUploadedFile($fileToUpload["tmp_name"][$index]);
		else
			$checkSafeUpload = $this->isUploadedFile($fileToUpload["tmp_name"]);
			
        // Upload the file
		if( $index !== false )
	        $moveUploadedFile = $this->moveUploadedFile($fileToUpload["tmp_name"][$index], $this->uploadDir . "/" . $newFileName);
		else
			$moveUploadedFile = $this->moveUploadedFile($fileToUpload["tmp_name"], $this->uploadDir . "/" . $newFileName);
			
        if ($checkSafeUpload && $moveUploadedFile)
		{
            return $this->uploadDir . "/" . $newFileName; 
        }
		else
		{
            throw new ImageUploaderException(" File could not be uploaded. Unknown error occurred. ");
			return false;
        }
    }

    public function isUploadedFile($file)
    {
        return is_uploaded_file($file);
    }

    public function moveUploadedFile($uploaded_file, $new_file) {
        return move_uploaded_file($uploaded_file, $new_file);
    }
}

class gifresizer
{
	public $temp_dir = "frames";
	private $pointer = 0;
	private $index = 0;
	private $globaldata = array();
	private $imagedata = array();
	private $imageinfo = array();
	private $handle = 0;
	private $orgvars = array();
	private $encdata = array();
	private $parsedfiles = array();
	private $originalwidth = 0;
	private $originalheight = 0;
	private $wr,$hr;
	private $props = array();
	private $decoding = false;

	/** 
	* Public part of the class
	* 
	* @orgfile - Original file path
	* @newfile - New filename with path
	* @width   - Desired image width 
	* @height  - Desired image height
	*/ 
	function resize($orgfile,$newfile,$width,$height){
		$this->decode($orgfile);
		$this->wr=$width/$this->originalwidth;
		$this->hr=$height/$this->originalheight;
		$this->resizeframes();
		$this->encode($newfile,$width,$height);
		$this->clearframes();
	}	

	/** 
	* GIF Decoder function.
	* Parses the GIF animation into single frames.
	*/
	private function decode($filename){
		$this->decoding = true;            
		$this->clearvariables();
		$this->loadfile($filename);
		$this->get_gif_header();
		$this->get_graphics_extension(0);
		$this->get_application_data();
		$this->get_application_data();
		$this->get_image_block(0);
		$this->get_graphics_extension(1);
		$this->get_comment_data();
		$this->get_application_data();
		$this->get_image_block(1);
		while(!$this->checkbyte(0x3b) && !$this->checkEOF()){
			$this->get_comment_data(1);
			$this->get_graphics_extension(2);
			$this->get_image_block(2);
		}
		$this->writeframes(time());		
		$this->closefile();
		$this->decoding = false;
	}

	/** 
	* GIF Encoder function.
	* Combines the parsed GIF frames into one single animation.
	*/
	private function encode($new_filename,$newwidth,$newheight){
		$mystring = "";
		$this->pointer = 0;
		$this->imagedata = array();
		$this->imageinfo = array();
		$this->handle = 0;
		$this->index=0;

		$k=0;
		foreach($this->parsedfiles as $imagepart){
			$this->loadfile($imagepart);
			$this->get_gif_header();
			$this->get_application_data();
			$this->get_comment_data();
			$this->get_graphics_extension(0);
			$this->get_image_block(0);

			//get transparent color index and color
			if(isset($this->encdata[$this->index-1]))
				$gxdata = $this->encdata[$this->index-1]["graphicsextension"];
			else 
				$gxdata = null;
			$ghdata = $this->imageinfo["gifheader"];
			$trcolor = "";
			$hastransparency=($gxdata[3]&&1==1);

			if($hastransparency){
				$trcx = ord($gxdata[6]);
				$trcolor = substr($ghdata,13+$trcx*3,3);
			}

			//global color table to image data;
			$this->transfercolortable($this->imageinfo["gifheader"],$this->imagedata[$this->index-1]["imagedata"]);	

			$imageblock = &$this->imagedata[$this->index-1]["imagedata"];

			//if transparency exists transfer transparency index
			if($hastransparency){
				$haslocalcolortable = ((ord($imageblock[9])&128)==128);
				if($haslocalcolortable){
					//local table exists. determine boundaries and look for it.
					$tablesize=(pow(2,(ord($imageblock[9])&7)+1)*3)+10;
					$this->orgvars[$this->index-1]["transparent_color_index"] = 
					((strrpos(substr($this->imagedata[$this->index-1]["imagedata"],0,$tablesize),$trcolor)-10)/3);		
				}else{
					//local table doesnt exist, look at the global one.
					$tablesize=(pow(2,(ord($gxdata[10])&7)+1)*3)+10;
					$this->orgvars[$this->index-1]["transparent_color_index"] = 
					((strrpos(substr($ghdata,0,$tablesize),$trcolor)-10)/3);	
				}				
			}

			//apply original delay time,transparent index and disposal values to graphics extension

			if(!$this->imagedata[$this->index-1]["graphicsextension"]) $this->imagedata[$this->index-1]["graphicsextension"] = chr(0x21).chr(0xf9).chr(0x04).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00);

			$imagedata = &$this->imagedata[$this->index-1]["graphicsextension"];

			$imagedata[3] = chr((ord($imagedata[3]) & 0xE3) | ($this->orgvars[$this->index-1]["disposal_method"] << 2));
			$imagedata[4] = chr(($this->orgvars[$this->index-1]["delay_time"] % 256));
			$imagedata[5] = chr(floor($this->orgvars[$this->index-1]["delay_time"] / 256));
			if($hastransparency){
				$imagedata[6] = chr($this->orgvars[$this->index-1]["transparent_color_index"]);
			}
			$imagedata[3] = chr(ord($imagedata[3])|$hastransparency);

			//apply calculated left and top offset 
			$imageblock[1] = chr(round(($this->orgvars[$this->index-1]["offset_left"]*$this->wr) % 256));
			$imageblock[2] = chr(floor(($this->orgvars[$this->index-1]["offset_left"]*$this->wr) / 256));
			$imageblock[3] = chr(round(($this->orgvars[$this->index-1]["offset_top"]*$this->hr) % 256));
			$imageblock[4] = chr(floor(($this->orgvars[$this->index-1]["offset_top"]*$this->hr) / 256));			

			if($this->index==1){
				if(!isset($this->imageinfo["applicationdata"]) || !$this->imageinfo["applicationdata"]) 
					$this->imageinfo["applicationdata"]=chr(0x21).chr(0xff).chr(0x0b)."NETSCAPE2.0".chr(0x03).chr(0x01).chr(0x00).chr(0x00).chr(0x00);
				if(!isset($this->imageinfo["commentdata"]) || !$this->imageinfo["commentdata"])
					$this->imageinfo["commentdata"] = chr(0x21).chr(0xfe).chr(0x10)."PHPGIFRESIZER1.0".chr(0);
				$mystring .= $this->orgvars["gifheader"]. $this->imageinfo["applicationdata"].$this->imageinfo["commentdata"];
				if(isset($this->orgvars["hasgx_type_0"]) && $this->orgvars["hasgx_type_0"]) $mystring .= $this->globaldata["graphicsextension_0"];
				if(isset($this->orgvars["hasgx_type_1"]) && $this->orgvars["hasgx_type_1"]) $mystring .= $this->globaldata["graphicsextension"];
			}

			$mystring .= $imagedata . $imageblock;
			$k++;
			$this->closefile();
		}

		$mystring .= chr(0x3b); 

		//applying new width & height to gif header
		$mystring[6] = chr($newwidth % 256);
		$mystring[7] = chr(floor($newwidth / 256));
		$mystring[8] = chr($newheight % 256);
		$mystring[9] = chr(floor($newheight / 256));
		$mystring[11]= $this->orgvars["background_color"];
		//if(file_exists($new_filename)){unlink($new_filename);}
		file_put_contents($new_filename,$mystring);
	}

	/** 
	* Variable Reset function
	* If a instance is used multiple times, it's needed. Trust me.
	*/
	private function clearvariables(){
		$this->pointer = 0;
		$this->index = 0;
		$this->imagedata = array();
		$this->imageinfo = array();            
		$this->handle = 0;
		$this->parsedfiles = array();
	}

	/** 
	* Clear Frames function
	* For deleting the frames after encoding.
	*/
	private function clearframes(){
		foreach($this->parsedfiles as $temp_frame){
			unlink($temp_frame);
		}
	}

	/** 
	* Frame Writer
	* Writes the GIF frames into files.
	*/
	private function writeframes($prepend){
		for($i=0;$i<sizeof($this->imagedata);$i++){
			file_put_contents($this->temp_dir."/frame_".$prepend."_".str_pad($i,2,"0",STR_PAD_LEFT).".gif",$this->imageinfo["gifheader"].$this->imagedata[$i]["graphicsextension"].$this->imagedata[$i]["imagedata"].chr(0x3b));
			$this->parsedfiles[]=$this->temp_dir."/frame_".$prepend."_".str_pad($i,2,"0",STR_PAD_LEFT).".gif";
		}
	}

	/** 
	* Color Palette Transfer Device
	* Transferring Global Color Table (GCT) from frames into Local Color Tables in animation.
	*/
	private function transfercolortable($src,&$dst){
		//src is gif header,dst is image data block
		//if global color table exists,transfer it
		if((ord($src[10])&128)==128){
			//Gif Header Global Color Table Length
			$ghctl = pow(2,$this->readbits(ord($src[10]),5,3)+1)*3;
			//cut global color table from gif header
			$ghgct = substr($src,13,$ghctl);
			//check image block color table length
			if((ord($dst[9])&128)==128){
				//Image data contains color table. skip.
			}else{
				//Image data needs a color table.
				//get last color table length so we can truncate the dummy color table
				$idctl = pow(2,$this->readbits(ord($dst[9]),5,3)+1)*3;
				//set color table flag and length	
				$dst[9] = chr(ord($dst[9]) | (0x80 | (log($ghctl/3,2)-1)));
				//inject color table
				$dst = substr($dst,0,10).$ghgct.substr($dst,-1*strlen($dst)+10);
			}
		}else{
			//global color table doesn't exist. skip.
		}
	}

	/** 
	* GIF Parser Functions.
	* Below functions are the main structure parser components.
	*/
	private function get_gif_header(){
		$this->p_forward(10);
		if($this->readbits(($mybyte=$this->readbyte_int()),0,1)==1){
			$this->p_forward(2);
			$this->p_forward(pow(2,$this->readbits($mybyte,5,3)+1)*3);
		}else{
			$this->p_forward(2);
		}

		$this->imageinfo["gifheader"]=$this->datapart(0,$this->pointer);
		if($this->decoding){
			$this->orgvars["gifheader"]=$this->imageinfo["gifheader"];
			$this->originalwidth = ord($this->orgvars["gifheader"][7])*256+ord($this->orgvars["gifheader"][6]);
			$this->originalheight = ord($this->orgvars["gifheader"][9])*256+ord($this->orgvars["gifheader"][8]);
			$this->orgvars["background_color"]=$this->orgvars["gifheader"][11];
		}

	}
	//-------------------------------------------------------
	private function get_application_data(){
		$startdata = $this->readbyte(2);
		if($startdata==chr(0x21).chr(0xff)){
			$start = $this->pointer - 2;
			$this->p_forward($this->readbyte_int());
			$this->read_data_stream($this->readbyte_int());
			$this->imageinfo["applicationdata"] = $this->datapart($start,$this->pointer-$start);
		}else{
			$this->p_rewind(2);
		}
	}
	//-------------------------------------------------------
	private function get_comment_data(){
		$startdata = $this->readbyte(2);
		if($startdata==chr(0x21).chr(0xfe)){
			$start = $this->pointer - 2;
			$this->read_data_stream($this->readbyte_int());
			$this->imageinfo["commentdata"] = $this->datapart($start,$this->pointer-$start);
		}else{
			$this->p_rewind(2);
		}
	}
	//-------------------------------------------------------
	private function get_graphics_extension($type){
		$startdata = $this->readbyte(2);
		if($startdata==chr(0x21).chr(0xf9)){
			$start = $this->pointer - 2;
			$this->p_forward($this->readbyte_int());
			$this->p_forward(1);
			if($type==2){
				$this->imagedata[$this->index]["graphicsextension"] = $this->datapart($start,$this->pointer-$start);
			}else if($type==1){
				$this->orgvars["hasgx_type_1"] = 1;
				$this->globaldata["graphicsextension"] = $this->datapart($start,$this->pointer-$start);
			}else if($type==0 && $this->decoding==false){
				$this->encdata[$this->index]["graphicsextension"] = $this->datapart($start,$this->pointer-$start);
			}else if($type==0 && $this->decoding==true){
				$this->orgvars["hasgx_type_0"] = 1;
				$this->globaldata["graphicsextension_0"] = $this->datapart($start,$this->pointer-$start);
			}
		}else{
			$this->p_rewind(2);
		}
	}
	//-------------------------------------------------------
	private function get_image_block($type){
		if($this->checkbyte(0x2c)){
			$start = $this->pointer;
			$this->p_forward(9);
			if($this->readbits(($mybyte=$this->readbyte_int()),0,1)==1){
				$this->p_forward(pow(2,$this->readbits($mybyte,5,3)+1)*3);
			}
			$this->p_forward(1);
			$this->read_data_stream($this->readbyte_int());
			$this->imagedata[$this->index]["imagedata"] = $this->datapart($start,$this->pointer-$start);

			if($type==0){
				$this->orgvars["hasgx_type_0"] = 0;
				if(isset($this->globaldata["graphicsextension_0"]))
					$this->imagedata[$this->index]["graphicsextension"]=$this->globaldata["graphicsextension_0"];
				else
					$this->imagedata[$this->index]["graphicsextension"]=null;
				unset($this->globaldata["graphicsextension_0"]);
			}elseif($type==1){
				if(isset($this->orgvars["hasgx_type_1"]) && $this->orgvars["hasgx_type_1"]==1){
					$this->orgvars["hasgx_type_1"] = 0;
					$this->imagedata[$this->index]["graphicsextension"]=$this->globaldata["graphicsextension"];
					unset($this->globaldata["graphicsextension"]);
				}else{
					$this->orgvars["hasgx_type_0"] = 0;
					$this->imagedata[$this->index]["graphicsextension"]=$this->globaldata["graphicsextension_0"];
					unset($this->globaldata["graphicsextension_0"]);
				}
			}

			$this->parse_image_data();
			$this->index++;

		}
	}
	//-------------------------------------------------------
	private function parse_image_data(){
		$this->imagedata[$this->index]["disposal_method"] = $this->get_imagedata_bit("ext",3,3,3);
		$this->imagedata[$this->index]["user_input_flag"] = $this->get_imagedata_bit("ext",3,6,1);
		$this->imagedata[$this->index]["transparent_color_flag"] = $this->get_imagedata_bit("ext",3,7,1);
		$this->imagedata[$this->index]["delay_time"] = $this->dualbyteval($this->get_imagedata_byte("ext",4,2));
		$this->imagedata[$this->index]["transparent_color_index"] = ord($this->get_imagedata_byte("ext",6,1));
		$this->imagedata[$this->index]["offset_left"] = $this->dualbyteval($this->get_imagedata_byte("dat",1,2));
		$this->imagedata[$this->index]["offset_top"] = $this->dualbyteval($this->get_imagedata_byte("dat",3,2));
		$this->imagedata[$this->index]["width"] = $this->dualbyteval($this->get_imagedata_byte("dat",5,2));
		$this->imagedata[$this->index]["height"] = $this->dualbyteval($this->get_imagedata_byte("dat",7,2));
		$this->imagedata[$this->index]["local_color_table_flag"] = $this->get_imagedata_bit("dat",9,0,1);
		$this->imagedata[$this->index]["interlace_flag"] = $this->get_imagedata_bit("dat",9,1,1);
		$this->imagedata[$this->index]["sort_flag"] = $this->get_imagedata_bit("dat",9,2,1);
		$this->imagedata[$this->index]["color_table_size"] = pow(2,$this->get_imagedata_bit("dat",9,5,3)+1)*3;
		$this->imagedata[$this->index]["color_table"] = substr($this->imagedata[$this->index]["imagedata"],10,$this->imagedata[$this->index]["color_table_size"]);
		$this->imagedata[$this->index]["lzw_code_size"] = ord($this->get_imagedata_byte("dat",10,1));
		if($this->decoding){
			$this->orgvars[$this->index]["transparent_color_flag"] = $this->imagedata[$this->index]["transparent_color_flag"];
			$this->orgvars[$this->index]["transparent_color_index"] = $this->imagedata[$this->index]["transparent_color_index"];
			$this->orgvars[$this->index]["delay_time"] = $this->imagedata[$this->index]["delay_time"];
			$this->orgvars[$this->index]["disposal_method"] = $this->imagedata[$this->index]["disposal_method"];
			$this->orgvars[$this->index]["offset_left"] = $this->imagedata[$this->index]["offset_left"];
			$this->orgvars[$this->index]["offset_top"] = $this->imagedata[$this->index]["offset_top"];
		}
	}
	//-------------------------------------------------------
	private function get_imagedata_byte($type,$start,$length){
		if($type=="ext")
			return substr($this->imagedata[$this->index]["graphicsextension"],$start,$length);
		elseif($type=="dat")
			return substr($this->imagedata[$this->index]["imagedata"],$start,$length);
	}
	//-------------------------------------------------------
	private function get_imagedata_bit($type,$byteindex,$bitstart,$bitlength){
		if($type=="ext")
			return $this->readbits(ord(substr($this->imagedata[$this->index]["graphicsextension"],$byteindex,1)),$bitstart,$bitlength);
		elseif($type=="dat")
			return $this->readbits(ord(substr($this->imagedata[$this->index]["imagedata"],$byteindex,1)),$bitstart,$bitlength);
	}
	//-------------------------------------------------------
	private function dualbyteval($s){
		$i = ord($s[1])*256 + ord($s[0]);
		return $i;
	}
	//------------   Helper Functions ---------------------
	private function read_data_stream($first_length){
		$this->p_forward($first_length);
		$length=$this->readbyte_int();
		if($length!=0) {
			while($length!=0){
				$this->p_forward($length);
				$length=$this->readbyte_int();
			}
		}
		return true;
	}
	//-------------------------------------------------------
	private function loadfile($filename){
		$this->handle = fopen($filename,"rb");
		$this->pointer = 0;
	}
	//-------------------------------------------------------
	private function closefile(){
		fclose($this->handle);
		$this->handle=0;
	}
	//-------------------------------------------------------
	private function readbyte($byte_count){
		$data = fread($this->handle,$byte_count);
		$this->pointer += $byte_count;
		return $data;
	}
	//-------------------------------------------------------
	private function readbyte_int(){
		$data = fread($this->handle,1);
		$this->pointer++;
		return ord($data);
	}
	//-------------------------------------------------------
	private function readbits($byte,$start,$length){
		$bin = str_pad(decbin($byte),8,"0",STR_PAD_LEFT);
		$data = substr($bin,$start,$length);
		return bindec($data);
	}
	//-------------------------------------------------------
	private function p_rewind($length){
		$this->pointer-=$length;
		fseek($this->handle,$this->pointer);
	}
	//-------------------------------------------------------
	private function p_forward($length){
		$this->pointer+=$length;
		fseek($this->handle,$this->pointer);
	}
	//-------------------------------------------------------
	private function datapart($start,$length){
		fseek($this->handle,$start);
		$data = fread($this->handle,$length);
		fseek($this->handle,$this->pointer);
		return $data;
	}
	//-------------------------------------------------------
	private function checkbyte($byte){
		if(fgetc($this->handle)==chr($byte)){
			fseek($this->handle,$this->pointer);
			return true;
		}else{
			fseek($this->handle,$this->pointer);
			return false;
		}
	}	
	//-------------------------------------------------------
	private function checkEOF(){
		if(fgetc($this->handle)===false){
			return true;
		}else{
			fseek($this->handle,$this->pointer);
			return false;
		}
	}	
	//-------------------------------------------------------
	/** 
	* Debug Functions. 
	* Parses the GIF animation into single frames.
	*/
	private function debug($string){
		echo "<pre>";
		for($i=0;$i<strlen($string);$i++){
			echo str_pad(dechex(ord($string[$i])),2,"0",STR_PAD_LEFT). " ";
		}
		echo "</pre>";
	}
	//-------------------------------------------------------
	private function debuglen($var,$len){
		echo "<pre>";
		for($i=0;$i<$len;$i++){
			echo str_pad(dechex(ord($var[$i])),2,"0",STR_PAD_LEFT). " ";
		}
		echo "</pre>";
	}	
	//-------------------------------------------------------
	private function debugstream($length){
		$this->debug($this->datapart($this->pointer,$length));
	}
	//-------------------------------------------------------
	/** 
	* GD Resizer Device
	* Resizes the animation frames
	*/
	private function resizeframes(){
		$k=0;
		foreach($this->parsedfiles as $img){
			$src = imagecreatefromgif($img);
			$sw = $this->imagedata[$k]["width"];
			$sh = $this->imagedata[$k]["height"];
			$nw = round($sw * $this->wr);
			$nh = round($sh * $this->hr);
			$sprite = imagecreatetruecolor($nw,$nh);	
			$trans = imagecolortransparent($sprite);
			imagealphablending($sprite, false);
			imagesavealpha($sprite, true);
			imagepalettecopy($sprite,$src);					
			imagefill($sprite,0,0,imagecolortransparent($src));
			imagecolortransparent($sprite,imagecolortransparent($src));						
			imagecopyresized($sprite,$src,0,0,0,0,$nw,$nh,$sw,$sh);		
			imagegif($sprite,$img);
			imagedestroy($sprite);
			imagedestroy($src);
			$k++;
		}
	}
}
