<?php
namespace Tests;

use App\HackAssembler;
use PHPUnit\Framework\TestCase;

class DecoderTest extends TestCase
{
    public function setup() : void
    {
        $this->assembler = new HackAssembler('assembly_language_file');
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
        $dest3 = '';

        $this->assertEquals('010', $this->assembler->decoder->dest_to_binary($dest1));
        $this->assertEquals('101', $this->assembler->decoder->dest_to_binary($dest2));
        $this->assertEquals('000', $this->assembler->decoder->dest_to_binary($dest3));
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
        $comp5 = 'M';

        // Includes 'a' value in results.
        $this->assertEquals('0101010', $this->assembler->decoder->comp_to_binary($comp1));
        $this->assertEquals('1110011',$this->assembler->decoder->comp_to_binary($comp2));
        $this->assertEquals('1000000',$this->assembler->decoder->comp_to_binary($comp3));
        $this->assertEquals('0000000',$this->assembler->decoder->comp_to_binary($comp4));
        $this->assertEquals('1110000',$this->assembler->decoder->comp_to_binary($comp5));


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
        $jump3 = '';

        $this->assertEquals('001', $this->assembler->decoder->jump_to_binary($jump1));
        $this->assertEquals('010', $this->assembler->decoder->jump_to_binary($jump2));
        $this->assertEquals('000', $this->assembler->decoder->jump_to_binary($jump3));
    }

}