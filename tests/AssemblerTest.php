<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Projects\P_06\HackAssembler;

class AssemblerTest extends TestCase
{
    public function setup() : void
    {
        $this->assembler = new HackAssembler('assembly_language_file');
    }

    /**
     * Tests the HackAssembler class converts symbolic a-instructions to binary for the Hack machine.
     *
     * @group Assembler
     */
    public function testAssemblerTranslatesAddressInstructions()
    {
        $instruction = '@443';
        $binary_instruction = $this->assembler->translate_a_instruction($instruction);

        $this->assertEquals('0000000110111011', $binary_instruction);
    }

    /**
     * Tests the HackAssembler class converts symbolic a-instructions to binary for the Hack machine.
     *
     * @group Assembler
     */
    public function testAssemblerTranslatesComputeInstructions()
    {
        $instruction = 'D=D+A';
        $binary_instruction = $this->assembler->translate_c_instruction($instruction);


        $this->assertEquals('1110000010010000', $binary_instruction);
        // 1110000010010000
        // 1110000010010000
        //

    }

    // TODO: move Parser tests to separate file
    // TODO: move Decoder tests to separate file.

    /**
     * Tests the Parser class will extract the destination segment of a compute instruction.
     *
     * @group Parser-Dest
     */
    public function testParserExtractsDestinationSegment()
    {
        $instruction1 = "dest=comp; jump";
        $instruction2 = "D=M";
        $instruction3 = 'M=M+1';
        $instruction4 = '0;JGT';

        $destination1 = $this->assembler->parser->dest($instruction1);
        $destination2 = $this->assembler->parser->dest($instruction2);
        $destination3 = $this->assembler->parser->dest($instruction3);
        $destination4 = $this->assembler->parser->dest($instruction4);

        $this->assertEquals('dest', $destination1);
        $this->assertEquals('D', $destination2);
        $this->assertEquals('M', $destination3);
        $this->assertEquals('', $destination4);
    }

    /**
     * Tests the Parser class will extract the jump segment of a compute instruction.
     *
     * @group Parser-Jump
     */
    public function testParserExtractsComputeSegment()
    {
        $instruction1 = "dest=comp; jump";
        $instruction2 = "D=M";
        $instruction3 = 'M=M+1';
        $instruction4 = '0;JGT';

        $compute1 = $this->assembler->parser->comp($instruction1);
        $compute2 = $this->assembler->parser->comp($instruction2);
        $compute3 = $this->assembler->parser->comp($instruction3);
        $compute4 = $this->assembler->parser->comp($instruction4);

        $this->assertEquals('comp', $compute1);
        $this->assertEquals('M', $compute2);
        $this->assertEquals('M+1', $compute3);
        $this->assertEquals('0', $compute4);
    }

    /**
     * Tests the Parser class will extract the jump segment of a compute instruction.
     *
     * @group Parser-Comp
     */
    public function testParserExtractsJumpSegment()
    {
        $instruction1 = "dest=comp; jump";
        $instruction2 = "D=M";
        $instruction3 = 'M=M+1';
        $instruction4 = '0;JGT';

        $jump1 = $this->assembler->parser->jump($instruction1);
        $jump2 = $this->assembler->parser->jump($instruction2);
        $jump3 = $this->assembler->parser->jump($instruction3);
        $jump4 = $this->assembler->parser->jump($instruction4);

        $this->assertEquals('jump', $jump1);
        $this->assertEquals('', $jump2);
        $this->assertEquals('', $jump3);
        $this->assertEquals('JGT', $jump4);
    }

    /**
     * Tests the Decoder class translates the destination segment of a c-instruction to binary.
     *
     * @group Decoder
     */
    public function testDecoderTranslatesDestinationSegment()
    {
        $dest1 = 'D';
        $dest2 = 'AM';
        $dest8 = '';

        $this->assertEquals('010', $this->assembler->decoder->dest_to_binary($dest1));
        $this->assertEquals('101', $this->assembler->decoder->dest_to_binary($dest2));
        $this->assertEquals('000', $this->assembler->decoder->dest_to_binary($dest8));
    }

    /**
     * Tests the Decoder class translates the computation segment of a c-instruction to binary.
     *
     * @group Decoder
     */
    public function testDecoderTranslatesComputeSegment()
    {
        $comp1 = '0';
        $comp2 = '-M';
        $comp3 = 'D&M';
        $comp4 = 'D&A';

        // Includes 'a' value in results.
        $this->assertEquals('0101010', $this->assembler->decoder->comp_to_binary($comp1));
        $this->assertEquals('1110011',$this->assembler->decoder->comp_to_binary($comp2));
        $this->assertEquals('1000000',$this->assembler->decoder->comp_to_binary($comp3));
        $this->assertEquals('0000000',$this->assembler->decoder->comp_to_binary($comp4));

    }

    /**
     * Tests the Decoder class translates the jump segment of a c-instruction to binary.
     *
     * @group Decoder
     */
    public function testDecoderTranslatesJumpSegment()
    {
        $jump1 = 'JGT';
        $jump2 = 'JEQ';
        $jump8 = '';

        // $this->assertEquals('001', $this->assembler->decoder->jump_to_binary($jump1));
        // $this->assertEquals('010', $this->assembler->decoder->jump_to_binary($jump2));
        $this->assertEquals('000', $this->assembler->decoder->jump_to_binary($jump8));
    }
}
