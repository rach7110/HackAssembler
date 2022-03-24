<?php
namespace Tests;

use App\HackAssembler;
use PHPUnit\Framework\TestCase;

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
        $instruction1 = 'D=D+A';
        $instruction2 = 'D=M';

        $binary_instruction1 = $this->assembler->translate_c_instruction($instruction1);
        $binary_instruction2 = $this->assembler->translate_c_instruction($instruction2);

        $this->assertEquals('1110000010010000', $binary_instruction1);
        $this->assertEquals('1111110000010000', $binary_instruction2);
    }
}
