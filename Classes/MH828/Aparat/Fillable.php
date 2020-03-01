<?php

namespace MH828\Aparat;

trait Fillable
{
    /**
     * @param $params
     * @return self|null
     */
    public static function newInstance($params)
    {
        return self::FillBaseFunction($params, self::class, null);
    }

    /**
     * @param $params
     * @return self|null
     */
    public function fill($params)
    {
        return self::FillBaseFunction($params, self::class, $this);
    }

    private static function FillBaseFunction($params, $class, $instant = null)
    {
        $class = new \ReflectionClass($class);
        $props = $class->getProperties();
        $instant = $instant === null ? $class->newInstance() : $instant;
        /** @var \ReflectionProperty $pr */
        foreach ($props as $pr) {
            $pn = $pr->name;
            if (isset($params->$pn))
                $instant->$pn = $params->$pn;
        }

        return $instant;
    }

}