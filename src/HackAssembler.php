<?php
namespace Projects\P_06;

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

/** For symbolic (assembly) C-instructions, parses the instruction into its fields (dest, comp, & jump). */
class Parser {
    /**
     * Returns the dest segment of the current C-instruction (8 possibilities)
     * For example:
     *  'D=M' returns 'D'
     *
     * @param string $instruction
     * @return string $dest
     */
    public function dest($instruction)
    {
        // Everything before the '=' character.
        $pattern = '/(.*)=/';
        preg_match($pattern, $instruction, $matches);

        // Return empty string if there is no destination (ie: 0;JMP)
        $dest = empty($matches) ? '' : $matches[1];

        return $dest;
    }

    /**
     * Returns the comp segment of the current C-instruction (28 possibilities)
     * For example:
     *  'D=M' returns 'M'
     *  'M=M+1 returns 'M+1'
     *
     * @param string $instruction
     * @return string $comp
     */
    public function comp($instruction)
    {
        $jump_delineator = ';';
        $equation_pattern = '/=(.*)/';
        $condition_pattern = '/(.*);/'; // If it contains a jump.

        preg_match($equation_pattern, $instruction, $matches_equation);
        preg_match($condition_pattern, $instruction, $matches_conditional);

        // Case 1: 'comp' includes an equation.
        if (! empty($matches_equation)) {
            // Everything after the '=' character.
            $preg_result = $matches_equation[1];

            // If the string contains a ';jump', remove it.
            $jump_position = strpos($preg_result, $jump_delineator);

            $comp = $jump_position ? substr($preg_result, 0, $jump_position) : $preg_result;
        // Case 2: 'comp' is a conditional statement.
        } elseif (! empty($matches_conditional)) {
            $comp = $matches_conditional[1];
        // Future cases
        } else {
            $comp = '';
        }

        return $comp;
    }

    /**
     * Returns the jump segment of the current C-instruction (8 possibilities)
     *
     * For example,
     *  'D;JGT' returns 'JGT'
     *
     * @param string $instruction
     * @return string $jump
     */
    public function jump($instruction)
    {
        $pattern = '/;(.*)/';

        preg_match($pattern, $instruction, $matches);

        // Return empty string if there is no jump.)
        $jump = empty($matches) ? '' : trim($matches[1]);

        return $jump;
    }
}

/** Converts symbolic (assembly) c-instructions into binary c-instructions for the Hack computer.*/
class Decoder
{
    /** The destination lookup table */
    CONST DEST = [
        'null' => '000',
        'M' => '001',
        'D' => '010',
        'DM' => '011',
        'A' => '100',
        'AM' => '101',
        'AD' => '110',
        'AMD' => '111'
    ];

    /** The computation lookup table. Does NOT include 'a' value. */
    CONST  COMP = [
            '0'     => '101010',
            '1'     => '111111',
            '-1'    => '111010',
            'D'     => '001100',
            'A'     => '110000',
            'M'     => '110000',
            '!D'    => '001101',
            '!A'    => '110001',
            '!M'    => '110001',
            '-D'    => '001111',
            '-A'    => '110011',
            '-M'    => '110011',
            'D+1'   => '011111',
            'A+1'   => '110111',
            'M+1'   => '110111',
            'D-1'   => '001110',
            'A-1'   => '110010',
            'M-1'   => '110010',
            'D+A'   => '000010',
            'D+M'   => '000010',
            'D-A'   => '010011',
            'D-M'   => '010011',
            'A-D'   => '000111',
            'M-D'   => '000111',
            'D&A'   => '000000',
            'D&M'   => '000000',
            'D|A'   => '010101',
            'D|M'   => '010101'
    ];

    /** The jump lookup table */
    CONST  JUMP = [
        'null'  => '000',
        'JGT' => '001',
        'JEQ' => '010',
        'JGE' => '011',
        'JLT' => '100',
        'JNE' => '101',
        'JLE' => '110',
        'JMP' => '111'
        ];

    /**
     * Returns the symbolic dest part of the current C-instruction (8 possibilities)
     * For example:
     *  'D' returns '001100'
     *
     * @pre $dest_assembly can be an empty string.
     * @param string $dest_assembly
     * @return string $binary
     */
    public function dest_to_binary($dest_assembly)
    {
        $dest_assembly = ($dest_assembly === "") ? 'null' : trim($dest_assembly);

        $binary = self::DEST[$dest_assembly];

        return $binary;

        // TODO: Improvement: Allow for destination with multiple targets to be
        // written in any order.
        // Example: DM=D+1 can also be written as MD=D+1;
    }

    /**
     * Returns the symbolic comp part of the current C-instruction (8 possibilities)
     * For example:
     *  'M' returns '110000'
     *
     * @param string $comp_assembly
     * @return string $binary
     */
    public function comp_to_binary($comp_assembly)
    {
        $c_bits = '';
        $a_bit = '';

        // Get bits for 'c' positions.
        $c_bits = self::COMP[trim($comp_assembly)];


        // Get bit for 'a' position.
        $contains_a = strpos($comp_assembly, 'A');
        $contains_m = strpos($comp_assembly, 'M');

        if ($contains_a || ! $contains_m) {
            $a_bit="0";
        } else {
            $a_bit="1";
        }

        $binary = "{$a_bit}{$c_bits}";

        return $binary;
    }

    /**
     * Returns the symbolic jump part of the current C-instruction (8 possibilities)
     * For example,
     *  'JGT' returns '001'
     *
     * @pre $jump_assembly can be an empty string.
     * @param string $jump_assembly
     * @return string $binary
     */
    public function jump_to_binary($jump_assembly)
    {
        $jump_assembly = ($jump_assembly === "") ? 'null' : trim($jump_assembly);

        $binary = self::JUMP[$jump_assembly];


        return $binary;
    }
}

echo "Please enter the ABSOLUTE file path for your Hack assembly language file. \n";

// Gets the name of the source file from the command-line argument.
$stdin = fopen('php://stdin', 'r');
$assembly_file = trim(fgets($stdin));

$assembler = new HackAssembler($assembly_file);

$assembler->translate();