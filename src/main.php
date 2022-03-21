<?php
namespace Projects\P_06;

// BUG Uncaught Error: Class "Projects\P_06\HackAssembler" not found
use Projects\P_06\HackAssembler;

/** When the user supplies a Prog.asm file via the command line argument,
 * it is translated into the correct Hack binary code and stored in a file
 * named Prog.hack. THe output file will be located in the same folder as
 * the source file. And if a file by this name exists, it will be overwritten.
*/

// PART I: Handling Instructions
// PART II: Handling Symbols
class Main {
    public function run() {
        echo "Hello World! Please enter the file name for your Hack assembly language file. \n";

        // Gets the name of the input source file from the command-line argument.
        $stdin = fopen('php://stdin', 'r');
        $assembly_file = fgets($stdin);

        $assembler = new HackAssembler($assembly_file);

        $assembler->translate();
    }
}

$init = new Main;
$init->run();
exit;
