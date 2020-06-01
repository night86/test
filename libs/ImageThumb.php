<?php
/**
 * Created by PhpStorm.
 * User: Michal
 * Date: 10/01/2018
 * Time: 14:47
 */

namespace Signa\Libs;

use Phalcon\Mvc\User\Component;
use Phalcon\Image\Adapter\Imagick;

class ImageThumb extends Component
{

    CONST
        SMALL = 's',
        XSMALL = 'xs',
        PREFIX_DELIMITER = '-'
    ;

    private $sizes = [
        self::SMALL => [500, 500],
        self::XSMALL => [250, 250],
    ];

    /**
     *
     * @param $filePath
     */
    public function createAllSizes($filePath)
    {
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        $fileName = pathinfo($filePath, PATHINFO_FILENAME);
        $dirName = pathinfo($filePath, PATHINFO_DIRNAME);
        foreach ($this->sizes as $prefix => $size) {
            $sizeFilePath = $dirName . DIRECTORY_SEPARATOR . $prefix . self::PREFIX_DELIMITER . $fileName . '.' . $fileExtension;
            copy($filePath, $sizeFilePath);
            $image = new Imagick($sizeFilePath);
            $image->resize($size[0], $size[1]);
            $image->save();
        }
    }

}