<?php

namespace App\Repositories;

use App\Models\InsuranceAttributeModule;

class InsuranceAttributesRepository
{
    public function findAttributesRecursionByBindId($bind_id)
    {
        $result =
            InsuranceAttributeModule::with(['attributes' => function ($query) {
                $query
                    ->with(['values' => function ($q) {
                        $q->select('value', 'control_value as controlValue', 'ty_value', 'conditions', 'regex', 'remind', 'attribute_type as attributeType', 'unit', 'aid');
                    }])
                    ->select('id', 'name', 'api_name as apiName', 'ty_key', 'type', 'regex', 'default_remind as defaultRemind', 'error_remind as errorRemind', 'required', 'mid')
                    ->orderBy('sort', 'asc');
            }])
            ->select('id as moduleId', 'name', 'remark', 'id', 'module_key')
            ->where('bind_id', $bind_id)
            ->get()
            ->toArray();
        $result = $this->format($result);

        return $result;
    }

    protected function format($modules)
    {
        foreach ($modules as &$module) {
            unset($module['id']);
            if (isset($module['attributes'])) {
                foreach ($module['attributes'] as &$attribute) {
                    unset($attribute['mid']);
                    unset($attribute['id']);
                    if (!empty($attribute['values'])) {
                        foreach ($attribute['values'] as &$value) {
                            unset($value['aid']);
                        }
                        $attribute['attributeValues'] = $attribute['values'];
                        unset($attribute['values']);
                    } else {
                        $attribute['attributeValues'] = [];
                    }
                }
                $module['productAttributes'] = $module['attributes'];
                unset($module['attributes']);
            } else {
                $module['productAttributes'] = [];
            }
        }

        return $modules;
    }

    public function unsetAfterFormat($insurance_attributes)
    {
        foreach ($insurance_attributes as &$insurance_attribute) {
            if (isset($insurance_attribute['productAttributes'])) {
                foreach ($insurance_attribute['productAttributes'] as &$insurance_attribute) {
                    if (isset($insurance_attribute['attributeValues'])) {
                        foreach ($insurance_attribute['attributeValues'] as &$value) {
                            unset($value['controlValue']);
                        }
                    }
                    unset($insurance_attribute['apiName']);
                }
            }
        }
        return $insurance_attributes;
    }

    //内外建值转换
    public function inToOut($bind_id, $attributes)
    {
        $insurance_attributes = $this->findAttributesRecursionByBindId($bind_id);
        $tmp_data = [];
        foreach ($insurance_attributes as $insurance_attribute) {
            $module_key = $insurance_attribute['module_key'];
            if (isset($attributes[$module_key]) && isset($insurance_attribute['productAttributes'])) {
                if (isset($attributes[$module_key][0])) { // 如果是二维数组
                    foreach ($attributes[$module_key] as $key => $item) {
                        foreach ($insurance_attribute['productAttributes'] as $productAttribute) {
                            $ty_key = $productAttribute['ty_key'];
                            if (isset($attributes[$module_key][$key][$ty_key]) && isset($productAttribute['attributeValues'])) {
                                foreach ($productAttribute['attributeValues'] as $attributeValue) {
                                    $ty_value = $attributeValue['ty_value'];
                                    if ($ty_value == $attributes[$module_key][$key][$ty_key]) {
                                        $attributes[$module_key][$key][$ty_key] = $attributeValue['controlValue'];
                                    }
                                }
                                if ($ty_key == 'ty_beibaoren_job') {
                                    $occupation = $attributes[$module_key][$ty_key];
                                    list(,,$code) = explode('-', $occupation);
                                    $attributes[$module_key][$key][$ty_key] = $code;
                                }
                            }
                            $tmp_data[$module_key][$key][$productAttribute['apiName']] = $attributes[$module_key][$key][$ty_key];
                        }
                    }
                } else {
                    foreach ($insurance_attribute['productAttributes'] as $productAttribute) {
                        $ty_key = $productAttribute['ty_key'];
                        if (isset($attributes[$module_key][$ty_key]) && isset($productAttribute['attributeValues'])) {
                            foreach ($productAttribute['attributeValues'] as $attributeValue) {
                                $ty_value = $attributeValue['ty_value'];
                                if ($ty_value == $attributes[$module_key][$ty_key]) {
                                    $attributes[$module_key][$ty_key] = $attributeValue['controlValue'];
                                }
                            }
                            if ($ty_key == 'ty_toubaoren_job') {
                                $occupation = $attributes[$module_key][$ty_key];
                                list(,,$code) = explode('-', $occupation);
                                $attributes[$module_key][$ty_key] = $code;
                            }
                        }
                        $tmp_data[$module_key][$productAttribute['apiName']] = $attributes[$module_key][$ty_key];
                    }
                }
            }
        }
        return $tmp_data;
    }
}
