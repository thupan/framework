<?php

/**
 * Classe de Html.
 * Para usar é só declarar use \Service\Html;
 * @package \Service\Html
 * @version 1.0
 *
 */

namespace Service;

class Html
{
    public static $voidElements = [
        'area' => 1,
        'base' => 1,
        'br' => 1,
        'col' => 1,
        'command' => 1,
        'embed' => 1,
        'hr' => 1,
        'img' => 1,
        'input' => 1,
        'keygen' => 1,
        'link' => 1,
        'meta' => 1,
        'param' => 1,
        'source' => 1,
        'track' => 1,
        'wbr' => 1,
    ];
    
    public static $attributeOrder = [
            'type',
            'id',
            'class',
            'name',
            'value',

            'href',
            'src',
            'srcset',
            'form',
            'action',
            'method',

            'selected',
            'checked',
            'readonly',
            'disabled',
            'multiple',

            'size',
            'maxlength',
            'width',
            'height',
            'rows',
            'cols',

            'alt',
            'title',
            'rel',
            'media',
        ];

    public static $dataAttributes = ['data', 'data-ng', 'ng'];

     
    /**
     * Método público beginTag.
     *
     * @method beginTag()
     * @param String
     * @param Array
     */
     public static function beginTag($name, $options = [])
    {
        if ($name === null || $name === false) {
            return '';
        }
        return "<$name" . static::renderTagAttributes($options) . '>';
    }
     /**
     * Método público endTag.
     *
     * @method endTag()
     * @param String
     */
    public static function endTag($name)
    {
        if ($name === null || $name === false) {
            return '';
        }
        return "</$name>";
    }
     /**
     * Método público tag.
     *
     * @method tag()
     * @param String
     * @param Array
     */
    public static function tag($name, $content = '', $options = [])
    {
        if ($name === null || $name === false) {
            return $content;
        }
        $html = "<$name" . static::renderTagAttributes($options) . '>';
        return isset(static::$voidElements[strtolower($name)]) ? $html : "$html$content</$name>";
    }  
    /**
     * Método público input.
     *
     * @method input()
     * @param String
     * @param String
     * @param String
     * @param Array
     */
    public static function input($type, $name = null, $value = null, $options = [])
    {
        if (!isset($options['type'])) {
            $options['type'] = $type;
        }
        $options['name'] = $name;
        $options['value'] = $value === null ? null : (string) $value;
        return static::tag('input', '', $options);
    }
    /**
     * Método público buttonInput.
     *
     * @method buttonInput()
     * @param String
     * @param Array
     */
    public static function buttonInput($label = 'Button', $options = [])
    {
        $options['type'] = 'button';
        $options['value'] = $label;
        return static::tag('input', '', $options);
    }
    /**
     * Método público button.
     *
     * @method button()
     * @param String
     * @param Array
     */
    public static function button($content = 'Button', $options = [])
    {
        if (!isset($options['type'])) {
            $options['type'] = 'button';
        }
        return static::tag('button', $content, $options);
    }
     /**
     * Método público a.
     *
     * @method a()
     * @param String
     * @param Array
     */
    public static function a($text, $url = null, $options = [])
    {
        if ($url !== null) {
            $options['href'] = $url;
        }
        return static::tag('a', $text, $options);
    }  
    
    public static function label($content, $for = null, $options = [])
    {
        $options['for'] = $for;
        return static::tag('label', $content, $options);
    }

    public static function dropDownList($name, $selection = null, $items = [], $options = [])
    {
        $options['name'] = $name;
        return static::tag('select', self::optionTag($items, $selection), $options);
    }

    public static function optionTag($items = [], $selection = null, $options = [])
    {
        foreach($items as $key =>$v)
        {
            ($key == $selection)? $op = ['value'=>$key,'selected'=>true]:$op = ['value'=>$key];
            $option .= static::tag('option', $v ,array_merge($op,$options));
        }

        return $option;
    }
    /**
     * Método público encode.
     *
     * @method encode()
     * @param String
     * @param Bool
     */
    public static function encode($content, $doubleEncode = true)
    {
        return htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }
    /**
    * Método público cssStyleFromArray.
    *
    * @method cssStyleFromArray()
    * @param Array
    */
    public static function cssStyleFromArray(array $style)
    {
        $result = '';
        foreach ($style as $name => $value) {
            $result .= "$name: $value; ";
        }
        // return null if empty to avoid rendering the "style" attribute
        return $result === '' ? null : rtrim($result);
    }
    /**
     * Método público renderTagAttributes.
     *
     * @method renderTagAttributes()
     * @param Array
     */
    public static function renderTagAttributes($attributes)
    {
        if (count($attributes) > 1) {
            $sorted = [];
            foreach (static::$attributeOrder as $name) {
                if (isset($attributes[$name])) {
                    $sorted[$name] = $attributes[$name];
                }
            }
            $attributes = array_merge($sorted, $attributes);
        }

        $html = '';
        foreach ($attributes as $name => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $html .= " $name";
                }
            } elseif (is_array($value)) {
                if (in_array($name, static::$dataAttributes)) {
                    foreach ($value as $n => $v) {
                        if (is_array($v)) {
                            $html .= " $name-$n='" . json_encode($v) . "'";
                        } else {
                            $html .= " $name-$n=\"" . static::encode($v) . '"';
                        }
                    }
                } elseif ($name === 'class') {
                    if (empty($value)) {
                        continue;
                    }
                    $html .= " $name=\"" . static::encode(implode(' ', $value)) . '"';
                } elseif ($name === 'style') {
                    if (empty($value)) {
                        continue;
                    }
                    $html .= " $name=\"" . static::encode(static::cssStyleFromArray($value)) . '"';
                } else {
                    $html .= " $name='" . json_encode($value) . "'";
                }
            } elseif ($value !== null) {
                $html .= " $name=\"" . static::encode($value) . '"';
            }
        }

        return $html;
    }
}