<?php
namespace Plantation\Clover\Functions;
/**
 * 校验json字符串
 * @param string $stringData
 * @return bool
 */
if (!function_exists('json_validate')){
    function json_validate($stringData)
    {
        if (empty($stringData)) return false;

        try
        {
            //校验json格式
            json_decode($stringData, true);
            return JSON_ERROR_NONE === json_last_error();
        }
        catch (\Exception $e)
        {
            return false;
        }
    }
}

if (!function_exists('getParents')){
    function getParents($elementId, $elements) {
        $parents = [];
        foreach ($elements as $element) {
            if ($element['ID'] == $elementId) {
                if ($element['Parent']) {
                    $parents = array_merge($parents, getParents($element['Parent'], $elements));
                }
                array_push($parents, $element);
                break;
            }
        }
        return $parents;
    }
}

if (!function_exists('getCurrency')){
    function getCurrency($price){
        if(!$price){
            $price = '0.00';
        }
        return '$'.$price;
    }
}
if (!function_exists('getCurrency')) {
    function getBrowserLanguage($availableLanguages = null)
    {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

            // 如果提供了可用语言数组，则进行筛选
            if ($availableLanguages) {
                foreach ($langs as $lang) {
                    $pieces = explode(';q=', trim($lang));
                    if (in_array($pieces[0], $availableLanguages)) {
                        return $pieces[0];
                    }
                }
            }

            // 如果没有提供可用语言数组，则返回最高优先级的语言
            return $langs[0];
        }
        return null;
    }
}