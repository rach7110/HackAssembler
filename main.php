<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\HackAssembler;

/** When the user supplies a Prog.asm file via the command line argument,
 * it is translated into the correct Hack binary code and stored in a file
 * named Prog.hack. THe output file will be located in the same folder as
 * the source file. And if a file by this name exists, it will be overwritten.
*/

// PART I: Handling Instructions
// PART II: Handling Symbols
class Main {
    public function run() {
        echo "Please enter the ABSOLUTE file path for your Hack assembly language file. \n";

        // Gets the name of the source file from the command-line argument.
        $stdin = fopen('php://stdin', 'r');
        $assembly_file = trim(fgets($stdin));

        $assembler = new HackAssembler($assembly_file);

        try {
            $assembler->translate();

            print_r("Success! Binary file written to: {$assembler->binary_file_name()}\n");
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }
}

$init = new Main;
$init->run();
exit;
