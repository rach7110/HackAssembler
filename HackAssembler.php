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

    const A_INSTRUCTION = "A_INSTRUCTION";
    const C_INSTRUCTION = "C_INSTRUCTION";
    // For Part II - symbols (labels, variables, & predefined symbols)
    const L_INSTRUCTION = "L_INSTRUCTION";
    const COMMENT = "COMMENT";

    public function __construct($input_file)
    {
        $input_file = "add/Add.asm"; // BUG: can't enter filepath from command line.
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

        foreach($assembly_instructions as $instruction) {
            // Strip away anything after the statement, like a comment.
            $white_space = strpos($instruction, ' ');

            if ($white_space) {
                $instruction = substr($instruction, 0, $white_space);
            }

            $type = $this->instruction_type($instruction);

            if ($type === self::A_INSTRUCTION) {
                $binary = $this->translate_a_instruction($instruction);
            }

            // C-Instruction
            if ($type === self::C_INSTRUCTION) {
                $binary = $this->translate_c_instruction($instruction);
            }

            // Writes it as the next line in the output .hack file.
            fwrite($binary_file, $binary);
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
        $address = $this->address_to_binary($a_instruction);
        // echo $binary . "\n";

        // Add enough zeros to beginning of binary value so there are 16 bits.
        $zeros_count = $length - strlen($address);
        $zeros = '';

        for ($i=1; $i < $zeros_count; $i++) {
            $zeros = "0{$zeros}";
        }

        $binary = "{$zeros}{$address}";

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
        $comp = $this->parser->comp($instruction);
        $dest = $this->parser->dest($instruction);
        $jump = $this->parser->jump($instruction);

        // Translate each piece into its binary equivalent - Decoder.
        $dest_binary = $this->decoder->dest($dest);
        $comp_binary = $this->decoder->comp($comp);
        $jump_binary = $this->decoder->jump($jump);

        // TODO determined by the Decoder.
        $a = '';

        // Concatenate the pieces into a single binary string.
        $binary = "111{$a}{$comp_binary}{$dest_binary}{$jump_binary}";

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
     * Converts a memory address in decimal form to its 15-bit
     * binary version, precluded with a 0 to indicate an address.
     * The returned string will have 16 characters.
     *
     * @param string $value
     * @return string binary_value
     */
    protected function address_to_binary($value)
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
     *  'M=M=1 returns 'M+1'
     *
     * @param string $instruction
     * @return string $comp
     */
    public function comp($instruction)
    {
        $jump_delineator = ';';
        $equation_pattern = '/=(.*)/';
        $condition_pattern = '/(.*);/';

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
    /**
     * Returns the symbolic dest part of the current C-instruction (8 possibilities)
     * For example:
     *  'D' returns '001100'
     *
     * @param string $instruction
     * @return string $dest
     */
    protected function dest($instruction)
    {
        // TODO
    }

    /**
     * Returns the symbolic comp part of the current C-instruction (8 possibilities)
     * For example:
     *  'M' returns '110000'
     *
     * @param string $instruction
     * @return string $comp
     */
    protected function comp($instruction)
    {
        // TODO
    }

    /**
     * Returns the symbolic jump part of the current C-instruction (8 possibilities)
     * For example,
     *  'JGT' returns '001'
     *
     * @param string $instruction
     * @return string $jump
     */
    protected function jump($instruction)
    {
        // TODO
    }
}
