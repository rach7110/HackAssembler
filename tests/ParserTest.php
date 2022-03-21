<?php
namespace Tests;

use App\HackAssembler;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function setup() : void
    {
        $this->instruction1 = "dest=comp; jump";
        $this->instruction2 = "D=M";
        $this->instruction3 = 'M=M+1';
        $this->instruction4 = '0;JGT';

        $this->assembler = new HackAssembler('assembly_language_file');
    }

    /**
     * Tests the Parser class will extract the destination segment of a compute instruction.
     *
     * @group Parser
     */
    public function testParserExtractsDestinationSegment()
    {
        $destination1 = $this->assembler->parser->dest($this->instruction1);
        $destination2 = $this->assembler->parser->dest($this->instruction2);
        $destination3 = $this->assembler->parser->dest($this->instruction3);
        $destination4 = $this->assembler->parser->dest($this->instruction4);

        $this->assertEquals('dest', $destination1);
        $this->assertEquals('D', $destination2);
        $this->assertEquals('M', $destination3);
        $this->assertEquals('', $destination4);
    }

    /**
     * Tests the Parser class will extract the jump segment of a compute instruction.
     *
     * @group Parser
     */
    public function testParserExtractsComputeSegment()
    {
        $compute1 = $this->assembler->parser->comp($this->instruction1);
        $compute2 = $this->assembler->parser->comp($this->instruction2);
        $compute3 = $this->assembler->parser->comp($this->instruction3);
        $compute4 = $this->assembler->parser->comp($this->instruction4);

        $this->assertEquals('comp', $compute1);
        $this->assertEquals('M', $compute2);
        $this->assertEquals('M+1', $compute3);
        $this->assertEquals('0', $compute4);
    }

    /**
     * Tests the Parser class will extract the jump segment of a compute instruction.
     *
     * @group Parser
     */
    public function testParserExtractsJumpSegment()
    {
        $jump1 = $this->assembler->parser->jump($this->instruction1);
        $jump2 = $this->assembler->parser->jump($this->instruction2);
        $jump3 = $this->assembler->parser->jump($this->instruction3);
        $jump4 = $this->assembler->parser->jump($this->instruction4);

        $this->assertEquals('jump', $jump1);
        $this->assertEquals('', $jump2);
        $this->assertEquals('', $jump3);
        $this->assertEquals('JGT', $jump4);
    }
}