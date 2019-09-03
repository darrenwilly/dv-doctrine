<?php
namespace DV\Doctrine\ORM\Query ;

use Doctrine\ORM\Query\Expr;

class Expression extends Expr
{
	
	public function like($x, $y)
	{
		return new Expr\Comparison($x, 'LIKE', $y);
	}
}