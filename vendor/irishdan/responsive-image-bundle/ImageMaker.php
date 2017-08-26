<?php

namespace IrishDan\ResponsiveImageBundle;

use Intervention\Image\ImageManager;

/**
 * Class ImageMaker
 *
 * @package ResponsiveImageBundle
 */
class ImageMaker
{
    use CoordinateLengthCalculator;

    /**
     * @var
     */
    private $compression;
    /**
     * @var
     */
    private $cropCoordinates = [];
    /**
     * @var
     */
    private $driver;
    /**
     * @var FileManager
     */
    private $fileManager;
    /**
     * @var
     */
    private $focusCoordinates = [];
    /**
     * @var
     */
    private $img;
    /**
     * @var
     */
    private $manager;
    /**
     * @var array
     */
    private $styleData = [];

    /**
     * Imager constructor.
     *
     * @param FileManager $system
     * @param array       $responsiveImageConfig
     * @internal param $driver
     * @internal param $compression
     */
    public function __construct(FileManager $system, $responsiveImageConfig = [])
    {
        $this->fileManager = $system;
        if (!empty($responsiveImageConfig['debug'])) {
            $this->debug = $responsiveImageConfig['debug'];
        }
        if (!empty($responsiveImageConfig['image_driver'])) {
            $this->driver = $responsiveImageConfig['image_driver'];
        }
        if (!empty($responsiveImageConfig['image_compression'])) {
            $this->compression = $responsiveImageConfig['image_compression'];
        }
    }

    /**
     * Separates the crop and focus coordinates from the image object and stores them.
     *
     * @param $cropFocusCoords
     */
    public function setCoordinateGroups($cropFocusCoords)
    {
        // x1, y1, x2, y2:x3, y3, x4, y4
        $coordsSets = explode(':', $cropFocusCoords);
        $this->cropCoordinates = explode(', ', $coordsSets[0]);
        $this->focusCoordinates = explode(', ', $coordsSets[1]);
    }

    /**
     * Returns the style information of a defined style.
     *
     * @param array $style
     */
    public function setStyleData($style = [])
    {
        $this->styleData['effect'] = empty($style['effect']) ? null : $style['effect'];
        $this->styleData['width'] = empty($style['width']) ? null : $style['width'];
        $this->styleData['height'] = empty($style['height']) ? null : $style['height'];
        $this->styleData['greyscale'] = empty($style['greyscale']) ? null : $style['greyscale'];
    }

    protected function scaleImage($width, $height)
    {
        $this->img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
    }

    protected function setImage($source, $driver = 'gd')
    {
        if (empty($this->manager)) {
            $this->manager = new ImageManager(['driver' => $driver]);
        }
        $this->img = $this->manager->make($source);
    }

    /**
     * Performs the image manipulation using current style information
     * and user defined crop and focus rectangles.
     *
     * @param       $source
     * @param       $destination
     * @param array $style
     * @param null  $cropFocusCoords
     * @return string
     */
    public function createImage($source, $destination, array $style = [], $cropFocusCoords = null)
    {
        $this->setImage($source, $this->driver);

        if (!empty($style)) {
            $this->setStyleData($style);
        }

        if (!empty($cropFocusCoords)) {
            $this->setCoordinateGroups($cropFocusCoords);
        }

        if (!empty($this->styleData)) {
            switch ($this->styleData['effect']) {
                case 'scale':
                    // Do the crop rectangle first
                    // then scale the image
                    $this->doCropRectangle();
                    $this->scaleImage($this->styleData['width'], $this->styleData['height']);
                    break;

                case 'crop':
                    // If there's no focus rectangle,
                    // just cut out the crop rectangle.
                    if (empty($this->getCoordinates('focus'))) {
                        $this->doCropRectangle();
                    } else {

                        $focusOffsetFinder = new FocusCropDataCalculator(
                            $this->getCoordinates('crop'),
                            $this->getCoordinates('focus'),
                            $this->styleData['width'],
                            $this->styleData['height']
                        );

                        $focusCropData = $focusOffsetFinder->getFocusCropData();
                        if (!empty($focusCropData)) {
                            $this->cropImage($focusCropData['width'], $focusCropData['height'], $focusCropData['x'], $focusCropData['y']);
                        }
                    }

                    $this->img->fit($this->styleData['width'], $this->styleData['height'], function ($constraint) {
                        $constraint->upsize();
                    });

                    break;
            }

            // Do greyscale.
            if (!empty($this->styleData['greyscale'])) {
                $this->img->greyscale();
            }
        }

        return $this->saveImage($destination, $source);
    }

    protected function cropImage($width, $height, $xOffset, $yOffset)
    {
        $this->img->crop(
            round($width),
            round($height),
            round($xOffset),
            round($yOffset)
        );
    }

    /**
     *  Crops out defined crop area.
     */
    protected function doCropRectangle()
    {
        // Get the offset.
        $cropCoords = $this->getCoordinates('crop');
        if (!empty($cropCoords)) {
            $x1 = $cropCoords[0];
            $y1 = $cropCoords[1];

            // Get the lengths.
            // @TODO: Methods not existing
            $newWidth = $this->getLength('x', $cropCoords);
            $newHeight = $this->getLength('y', $cropCoords);

            // Do the initial crop.
            $this->img->crop($newWidth, $newHeight, $x1, $y1);
        }
    }

    /**
     * Returns either the crop or focus rectangle coordinates.
     *
     * @param string $type
     * @return mixed
     */
    protected function getCoordinates($type = 'crop')
    {
        $coords = $this->{$type . 'Coordinates'};
        $valid = 0;
        foreach ($coords as $id => $coord) {
            if ($coord > 0) {
                $valid++;
            }
            $coords[$id] = round($coord);
        }

        if ($valid == 0) {
            return false;
        }

        return $coords;
    }

    /**
     * Saves the new image.
     *
     * @param $destination
     * @param $source
     * @return string
     */
    protected function saveImage($destination, $source)
    {
        // Check if directory exists and if not create it.
        $this->fileManager->directoryExists($destination, true);

        // Get the file name from source path.
        $filename = $this->fileManager->getFilenameFromPath($source);
        $fullPath = $destination . '/' . $filename;

        $this->img->save($fullPath, $this->compression);

        return $fullPath;
    }

    /**
     * Gets vertical or horizontal length between two coordinates (x, y, x2, y2).
     *
     * @param string $type
     * @param array  $coords
     * @return mixed
     */
    protected function getLength($type = 'x', array $coords)
    {
        $type = strtolower($type);
        if ($type == 'x') {
            return $coords[2] - $coords[0];
        } else {
            return $coords[3] - $coords[1];
        }
    }
}