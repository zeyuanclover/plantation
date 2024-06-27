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