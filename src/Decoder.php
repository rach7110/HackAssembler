<?php

namespace App;

/** Converts symbolic (assembly) c-instructions into binary c-instructions for the Hack computer.*/
class Decoder
{
    /** The destination lookup table */
    CONST DEST = [
        'null' => '000',
        'M' => '001',
        'D' => '010',
        'DM' => '011',
        'MD' => '011',
        'A' => '100',
        'AM' => '101',
        'MA' => '101',
        'AD' => '110',
        'AMD' => '111',
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
     * Converts the 'dest' part of the current C-instruction from assembly to binary (8 possibilities).
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
     * Converts the 'comp' part of the current C-instruction from assembly to binary (8 possibilities).
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
        $contains_a = is_int(strpos($comp_assembly, 'A'));
        $contains_m = is_int(strpos($comp_assembly, 'M'));

        if ($contains_a || ! $contains_m) {
            $a_bit="0";
        } else {
            $a_bit="1";
        }

        $binary = "{$a_bit}{$c_bits}";

        return $binary;
    }

    /**
     * Converts the 'jump' part of the current C-instruction from assembly to binary (8 possibilities).
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
