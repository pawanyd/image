<?php

namespace Intervention\Image\Imagick;

class Color extends \Intervention\Image\AbstractColor
{
    public $pixel;

    public function initFromInteger($value)
    {
        $a = ($value >> 24) & 0xFF;
        $r = ($value >> 16) & 0xFF;
        $g = ($value >> 8) & 0xFF;
        $b = $value & 0xFF;
        $a = $this->rgb2alpha($a);

        $this->setPixel($r, $g, $b, $a);
    }

    public function initFromArray($array)
    {
        $array = array_values($array);

        if (count($array) == 4) {

            // color array with alpha value
            list($r, $g, $b, $a) = $array;

        } elseif (count($array) == 3) {

            // color array without alpha value
            list($r, $g, $b) = $array;
            $a = 1;
        }
        
        $this->setPixel($r, $g, $b, $a);
    }

    public function initFromString($value)
    {
        if ($color = $this->rgbaFromString($value)) {
            $this->setPixel($color[0], $color[1], $color[2], $color[3]);
        }
    }

    public function initFromObject($value)
    {
        if (is_a($value, '\ImagickPixel')) {
            $this->pixel = $value;
        }
    }

    public function initFromRgb($r, $g, $b)
    {
        $this->setPixel($r, $g, $b);
    }

    public function initFromRgba($r, $g, $b, $a)
    {
        $this->setPixel($r, $g, $b, $a);
    }

    public function getInt()
    {
        $r = $this->getRedValue();
        $g = $this->getGreenValue();
        $b = $this->getBlueValue();
        $a = intval(round($this->getAlphaValue() * 255));

        return intval(($a << 24) + ($r << 16) + ($g << 8) + $b);
    }

    public function getHex($prefix = '')
    {
        return sprintf('%s%02x%02x%02x', $prefix, 
            $this->getRedValue(), 
            $this->getGreenValue(), 
            $this->getBlueValue()
        );
    }

    public function getArray()
    {
        return array(
            $this->getRedValue(),
            $this->getGreenValue(),
            $this->getBlueValue(),
            $this->getAlphaValue()
        );
    }

    public function getRgba()
    {
        return sprintf('rgba(%d, %d, %d, %.2f)', 
            $this->getRedValue(),
            $this->getGreenValue(),
            $this->getBlueValue(),
            $this->getAlphaValue()
        );
    }

    public function differs(\Intervention\Image\AbstractColor $color, $tolerance = 0)
    {
        $color_tolerance = round($tolerance * 2.55);
        $alpha_tolerance = round($tolerance);

        $delta = array(
            'r' => abs($color->getRedValue() - $this->getRedValue()),
            'g' => abs($color->getGreenValue() - $this->getGreenValue()),
            'b' => abs($color->getBlueValue() - $this->getBlueValue()),
            'a' => abs($color->getAlphaValue() - $this->getAlphaValue())
        );

        return (
            $delta['r'] > $color_tolerance or
            $delta['g'] > $color_tolerance or
            $delta['b'] > $color_tolerance or
            $delta['a'] > $alpha_tolerance
        );
    }

    public function getRedValue()
    {
        return intval(round($this->pixel->getColorValue(\Imagick::COLOR_RED) * 255));
    }

    public function getGreenValue()
    {
        return intval(round($this->pixel->getColorValue(\Imagick::COLOR_GREEN) * 255));
    }

    public function getBlueValue()
    {
        return intval(round($this->pixel->getColorValue(\Imagick::COLOR_BLUE) * 255));
    }

    public function getAlphaValue()
    {
        return round($this->pixel->getColorValue(\Imagick::COLOR_ALPHA), 2);
    }

    private function setPixel($r, $g, $b, $a = null)
    {
        $a = is_null($a) ? 1 : $a;

        return $this->pixel = new \ImagickPixel(
            sprintf('rgba(%d, %d, %d, %.2f)', $r, $g, $b, $a)
        );
    }

    public function getPixel()
    {
        return $this->pixel;
    }

    private function rgb2alpha($value)
    {
        // (255 -> 1.0) / (0 -> 0.0)
        return (float) round($value/255, 2);
    }

}
