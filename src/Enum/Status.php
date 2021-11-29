<?php
namespace App\Enum;

class Status extends EnumType {
    protected $name = 'status';
    protected $values = array('CLOSED', 'ESCALATED', 'OPEN', 'REJECTED', 'WAITING');
}