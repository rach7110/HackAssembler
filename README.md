# HackAssembler
Simple assembly to binary translator app written in PHP.

#### Introduction
An assembler is a program that converts symbolic machine language (assembly language) to binary machine language. This assembler program was written for the Hack hardware platform.  Hack is a hardware platform created from the nand2tetris course - which teaches students how to build a computer starting from the most basic component, a nand logic gate. This program is part of the Assembler project assignment in the course.

The Hack CPU uses address (A), data (D), and memory (M) registers to store data and instructions. A conceptual model of the Hack memory system is shown in Figure 1. (The actual architecture is wired somewhat differently.) Symbolic Hack code is explained in Figure 2. And the translation from symbolic to binary code is shown in Figure 3.

[Figure 1](images/Figure1.png)
Conceptual model of the Hack memory system

[Figure 2](images/Figure2.png)
Symbolic Hack code

[Figure 3](images/Figure3.png)
Hack translation

*Input:* The program expects a text file written in Hack assembly language. It is assumed that valid Hack assembly language is supplied. Sample assembly programs are included in the `/assemblyPrograms` subfolder.

*Output:* The output file will contain 16 bit instructions. If the instruction starts with a '0', it represents an address register (A-instruction). Otherwise, if the instruction starts with a '1', it represents a compute instruction (C-instruction) that will be used by the ALU in the hardware platform.
#### Technologies
* PHP 8.0.15
* Composer 2.2.5

#### Installation
To run this project, clone it and install dependencies locally using Composer. From the command line, run:

```
# Clone this repo
$ git clone https://github.com/rach7110/HackAssembler

# Change into the directory
$ cd HackAssembler

# Install dependencies
$ composer install

# Run the main program
$ php main.php
```

#### Example
```
$ php main.php
> Please enter the ABSOLUTE file path for your Hack assembly language file.

$ /Users/my-name/hackAssembler/assemblyPrograms/rect/RectL.asm
> Success! Binary file written to: /Users/my-name/hackAssembler/assemblyPrograms/rect/RectL.hack
```

#### Testing
Unit tests are included in the `tests/` folder. Assuming dependencies have been installed via composer, the tests can be run using phpunit.
```
$ vendor/bin/phpunit tests/
> PHPUnit 9.5.16 by Sebastian Bergmann and contributors.
>
> ........                                                            8 / 8 (100%)
>
> Time: 00:00.018, Memory: 4.00 MB
>
> OK (8 tests, 26 assertions)
```
