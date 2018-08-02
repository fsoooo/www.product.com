<?php

namespace App\Repositories;

use App\Models\RestrictGene;

class RestrictGeneRepository
{
    /**
     * @param $bind_id
     * @return array
     */
    public function findRestrictGenesRecursionByBindId($bind_id)
    {
        $result =
            RestrictGene::with(['values' => function ($query) {
                $query->select('type', 'value', 'ty_value', 'name', 'min', 'max', 'step', 'unit', 'rid');
            }])
                ->select('ty_key', 'key', 'name', 'clause_id as protectItemId', 'default_value as defaultValue', 'sort', 'type', 'display', 'id')
                ->where('bind_id', $bind_id)
                ->get()
                ->toArray();


        $result = $this->format($result);

        return $result;
    }

    protected function format($restrict_genes)
    {
        foreach ($restrict_genes as &$restrict_gene) {
            unset($restrict_gene['id']);
            if (!empty($restrict_gene['values'])) {
                foreach ($restrict_gene['values'] as &$value) {
                    unset($value['rid']);
                }
            } else {
                $restrict_gene['values'] = [];
            }
        }

        return $restrict_genes;
    }

    /**
     * 获得默认试算因子
     *
     * @param $bind_id
     * @return array|mixed
     */
    public function findDefaultRestrictGenes($bind_id)
    {
        $result =
            RestrictGene::select('ty_key', 'key','clause_id as protectItemId', 'default_value as value', 'sort')
                ->where('bind_id', $bind_id)
                ->get()
                ->toArray();

        return $result;
    }
}
