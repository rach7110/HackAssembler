<?php
namespace App;

use App\Decoder;
use App\Parser;

/** Converts symbolic machine language (assembly language)
 * to binary machine language written for the Hack hardware
 * platform.
 *
 * The resultant, binary Hack program is a sequence of text lines,
 * each consisting of sixteen 0 and 1 characters.
 * If the line starts with a 0, it represents a binary
 * A-instruction. Otherwise, it represents a binary C-instruction.
 */
class HackAssembler
{
    /** A user-supplied file that contains the assembly instructions which are to be translated.*/
    protected string $assembly_file;
    public Parser $parser;
    public Decoder $decoder;

    const A_INSTRUCTION = "A_INSTRUCTION";
    const C_INSTRUCTION = "C_INSTRUCTION";
    // For Part II - symbols (labels, variables, & predefined symbols)
    const L_INSTRUCTION = "L_INSTRUCTION";
    const COMMENT = "COMMENT";

    public function __construct($input_file)
    {
        $this->parser = new Parser;
        $this->decoder = New Decoder;
        $this->assembly_file = $input_file;
    }

    /**
     * Translates the assembly language file from symbolic to binary instructions and write them to file.
     *
     * @return void
     */
    public function translate()
    {
        $assembly_instructions = file($this->assembly_file, FILE_SKIP_EMPTY_LINES);

        // Creates an output file
        $binary_file = fopen("Prog.hack", 'w')  or die("Problem creating Prog.hack file");

        // TODO: Improvement - give output file same name and location as input file.

        // Blank lines are actually removed.
        $assembly_instructions = array_values(array_filter($assembly_instructions, "trim"));

        foreach($assembly_instructions as $instruction) {
            // Strip away anything after the statement, like a comment.
            $white_space = strpos($instruction, ' ');

            if ($white_space) {
                $instruction = substr($instruction, 0, $white_space);
            }

            // Determine instruction type
            $type = $this->instruction_type($instruction);

            // Translate A-Instruction
            if ($type === self::A_INSTRUCTION) {
                $binary = $this->translate_a_instruction($instruction);
                $binary_formatted = "{$binary}\r\n";

                // fwrite($binary_file, "A-instruction: \n");
                fwrite($binary_file, $binary_formatted);
            }

            // Translate C-Instruction
            if ($type === self::C_INSTRUCTION) {
                $binary = $this->translate_c_instruction($instruction);
                $binary_formatted = "{$binary}\r\n";

                // fwrite($binary_file, "C-instruction: \n");
                fwrite($binary_file, $binary_formatted);
            }
        }

        fclose($binary_file);
    }

    /**
     * Converts symbolic A-instruction to binary. Removes address
     * character '@' and converts decimal value to a 15-bit
     * binary precluded with a 0 bit to indicate an address
     * instruction.
     *
     * For example:
     * '@650' returns '0000001010001010'
     *
     * @param string $instruction
     * @return string $binary
     */
    public function translate_a_instruction($instruction)
    {
        $length = 16;

        // Strip away '@'.
        $a_instruction = substr($instruction, 1);
        // echo $a_instruction . "\n";

        // Convert into binary.
        $a_binary = $this->hex_to_binary($a_instruction);
        // echo $binary . "\n";

        // Add enough zeros to beginning of binary value so there are 16 bits.
        $zeros_count = $length - strlen($a_binary);
        $zeros = '';

        for ($i=1; $i <= $zeros_count; $i++) {
            $zeros .= "0";
        }

        $binary = "{$zeros}{$a_binary}";

        return $binary;
    }

    /**
     * Converts symbolic C-instruction into its 16-bit binary.
     *
     * For example:
     *   'dest = comp;jump; returns a binary version of '111accccccdddjjj'.
     *
     * @param string $instruction
     * @return string $binary
     */
    public function translate_c_instruction($instruction)
    {
        // Parse each line into its pieces.
        $dest = $this->parser->dest($instruction);
        $comp = $this->parser->comp($instruction);
        $jump = $this->parser->jump($instruction);

        // Translate each piece into its binary equivalent - Decoder.
        $dest_binary = $this->decoder->dest_to_binary($dest);
        $comp_binary = $this->decoder->comp_to_binary($comp);
        $jump_binary = $this->decoder->jump_to_binary($jump);

        // Concatenate the pieces into a single binary string.
        $binary = "111{$comp_binary}{$dest_binary}{$jump_binary}";



        return $binary;
    }

    /**
     * Returns the type of instruction: Compute, Address, or Label instruction.
     *
     * @param string $instruction
     * @return string $type
     */
    protected function instruction_type($instruction)
    {
        $type = "";
        $first_char = substr($instruction, 0, 1);

        if ($first_char == '@') {
            $type = self::A_INSTRUCTION;
        } elseif ($first_char == '(') {
            $type = self::L_INSTRUCTION;
        } elseif ($first_char == '/') {
            $type = self::COMMENT;
        } else {
            $type = self::C_INSTRUCTION;
        }

        return $type;
    }

    /**
     * Converts a memory address in decimal form to its
     * binary version. The returned binary value does not have a fixed width.
     *
     * @param string $value
     * @return string binary_value
     */
    protected function hex_to_binary($value)
    {
        $binary_value = "";
        $quotient = 1;

        while ($quotient > 0) {
            $bit = $value%2;
            $value = $value/2;
            $quotient = round($value, 0, PHP_ROUND_HALF_DOWN);

            $binary_value = $bit . $binary_value;
        }

        return $binary_value;
    }
}

echo "Please enter the ABSOLUTE file path for your Hack assembly language file. \n";

// Gets the name of the source file from the command-line argument.
$stdin = fopen('php://stdin', 'r');
$assembly_file = trim(fgets($stdin));

$assembler = new HackAssembler($assembly_file);

$assembler->translate();