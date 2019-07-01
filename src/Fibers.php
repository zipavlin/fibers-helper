<?php

namespace Fibers\Helper;

use Fibers\Helper\Facades\ModelHelper;
use Fibers\Helper\Facades\ModelsHelper;
use Fibers\Helper\Facades\MemoryHelper;
use Fibers\Helper\Facades\TemplateHelper;
use Fibers\Helper\Facades\UserHelper;
use Fibers\Helper\Facades\ViewHelper;

class Fibers
{
    private $defaultHidden = ['id', 'created_at', 'updated_at'];

    // bootstrap model helpers class
    public function model ()
    {
        return ModelHelper::class;
    }

    // bootstrap models helpers class
    public function models ()
    {
        return ModelsHelper::class;
    }

    // bootstrap template helpers class
    public function template ()
    {
        return TemplateHelper::class;
    }

    // bootstrap global storage helpers class
    public function storage ()
    {
        return MemoryHelper::class;
    }

    public function view ()
    {
        return ViewHelper::class;
    }

    public function user ()
    {
        return UserHelper::class;
    }

    public function getAttributeFields ($modelClass, $hidden = [], $fields = [])
    {
        // get columns
        return $this->getAttributes($modelClass)->mapWithKeys(function($item) use ($fields) {
            return [$item->Field => [
                'type' => $this->getColumnType($item),
                'required' => ($item->Null === "NO") and ($item->Default === null),
                'hidden' => $item->Hidden
            ]];
        })->forget(collect($this->defaultHidden)->concat($hidden)->toArray());
    }
    public function getColumnType($item, $customFields = null)
    {
        if ($customFields and isset($customFields[$item->Field])) return $customFields[$item->Field];
        preg_match("/(\w+)(?:\(\d+\))?.*/", $item->Type, $matches);
        $type = $matches[1];
        switch ($type) {
            case "bigint":case "int":case "float":
            return "number";
            case "varchar":
                return "text";
            case "timestamp":
                return "date";
            case "tinyint":
                return "boolean";
            case "text":
                return "wysiwyg";
        }
    }
    public function isEmailVerificationRequired()
    {
        return $this->getUserClass()->hasMethod("hasVerifiedEmail");
    }
}
