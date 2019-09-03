<?php
namespace DV\Doctrine\ORM\Extension;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * DateFunction ::= "Year" "(" ArithmeticPrimary ")"
 * @author
 */
class Year extends FunctionNode
{
    public $dateExpression;

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->dateExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        return 'YEAR(' .
            $this->dateExpression->dispatch($sqlWalker) .
        ')';
    }
}