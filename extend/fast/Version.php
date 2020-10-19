<?php

namespace fast;

/**
 * 版本检测和对比
 */
class Version
{


    /**
     * 比较两个版本号
     *
     * @param string $v1
     * @param string $v2
     * @return boolean
     */
    public static function compare($v1, $v2)
    {
        if ($v2 == "*" || $v1 == $v2)
        {
            return TRUE;
        }
        else
        {
            $values = [];
            $k = explode(',', $v2);
            foreach ($k as $v)
            {
                if (strpos($v, '-') !== FALSE)
                {
                    list($start, $stop) = explode('-', $v);
                    for ($i = $start; $i <= $stop; $i++)
                    {
                        $values[] = $i;
                    }
                }
                else
                {
                    $values[] = $v;
                }
            }
            return in_array($v1, $values) ? TRUE : FALSE;
        }
    }

}
