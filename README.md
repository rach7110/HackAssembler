# HackAssembler
Simple assembly to binary translator app written in PHP.

## Table of Contents
* Introduction
* Technologies
* Installation
* Example

#### Introduction
This program converts symbolic machine language (assembly language) to binary machine language written for the Hack hardware platform.  Hack is a hardware platform created from the nand2tetris course - which teaches students how to build a computer starting from the most component, a nand logic gate. This program is part of the Assembler project assignment. 

*Input:* The program accepts an input file and produces written in Hack asse,bly language. 

*Output:* The output file will contain 16 bit instructions. If the instruction starts with a '0', it represents an address register (A-instruction). Otherwise, if the instruction starts with a '1', it represents a compute instruction (C-instruction) that will be used by the ALU in the hardware platform.

#### Technologies
* PHP 8.0.15
* Composer 2.2.5

#### Installation
To run this project, clone it and install dependencies locally using Composer. From the command line, run:

```
# Clone this repo
git clone https://github.com/rach7110/HackAssembler

# Change into the directory
cd HackAssembler

# Install dependencies
composer install

# Run the main program
php main.php
```

#### Example
