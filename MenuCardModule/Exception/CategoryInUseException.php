<?php

namespace HetBonteHert\Module\MenuCard\Exception;

class CategoryInUseException extends \RuntimeException
{
    /**
     * @var string
     */
    protected $message = 'category.in_use_exception';
}
