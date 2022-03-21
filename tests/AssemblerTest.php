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
        $instruction = 'D=D+A';
        $binary_instruction = $this->assembler->translate_c_instruction($instruction);

        $this->assertEquals('1110000010010000', $binary_instruction);

    }
}
