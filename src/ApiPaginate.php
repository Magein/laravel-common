<?php

namespace Magein\Common;

use Illuminate\Pagination\LengthAwarePaginator;

class ApiPaginate
{
    public static function toArray(LengthAwarePaginator $paginator): array
    {
        return [
            'pages' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'has_more' => $paginator->hasMorePages(),
                //获取结果集中第一个数据的编号
                'from' => $paginator->firstItem(),
                // 获取结果集中最后一个数据的编号
                'to' => $paginator->lastItem(),
            ],
            'items' => $paginator->items(),
        ];
    }
}