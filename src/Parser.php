<?php

namespace App;

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
        $pattern = '/(.*)=/';  // Everything before the '=' character.
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