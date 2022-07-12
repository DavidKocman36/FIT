"""
Interpret jazyka IPPcode22
Autor: David Kocman, xkocma08
"""

from collections import deque
from lib2to3.pgen2.parse import ParseError
from logging import exception
from operator import truediv
from os import write
from posixpath import split
import sys
import getopt
import re
import xml.etree.ElementTree as tr

#Class holding an argument of an instruction
class arg:
    def __init__(self, arg_type, value):
        self.arg_type = arg_type
        self.value = value

#Class holding the instruction itself
class instruction:
    def __init__(self, name):
        self.name = name
        self.args = []
    def arg_add(self, arg_type, value):
        self.args.append(arg(arg_type, value))  

#Super class defining a local or a temporary frame
#Sublasses are created based on CREATEFRAME or POPFRAME instructions
class frame:
    def __init__(self):
        self.vars = {}
    def add_var(self, name ,type_v, value):
        self.vars[name] = [type_v, value]

#Subclass holding a temporary frame 
class TemporaryFrame(frame):
    def __init__(self):
        super().__init__()
    def add_var(self, name ,type_v, value):
        self.vars[name] = [type_v, value]
    def new_frame(self):
        self.vars = {}

#Subclass holding a local frame
class LocalFrame(frame):
    def __init__(self):
        super().__init__()
    def add_var(self, name ,type_v, value):
        self.vars[name] = [type_v, value]

"""
Replaces escaped sequences in a string if contained
:param str string String which might contain an escaped sequence
:return str string String with replaced escape sequences or an unchenged string 
"""
def str_replace(string):
    sequence = []
    i = string.find("\\")
    if(i >= 0):
        while(i != -1):
            if(i + 3 > len(string)):
                break
            sequence.append(string[i+1])
            sequence.append(string[i+2])
            sequence.append(string[i+3])
            charac = int(''.join(sequence))
            charac = chr(charac)
            string = re.sub('\\\\\d\d\d', charac, string, 1)
            sequence = []
            i = string.find("\\", i)

    return string

"""
Function for determining if a var1 is less than var2
:param var arg1 First argument
:param var arg2 Second argument
:param string type_v Type of a variables passed in
:param dict variables_dict Dictionary of used variables
:param var index Index of a stored instruction in the dictionary
:param int pc Program counter
:param var temporary Temporary frame class
:param var local Local frame class
:param deque local_stack Stack of local frames
"""
def LT_func(arg1, arg2, type_v, variables_dict, index, pc, temporary, local, local_stack):
    # If variable is less than the other, writes a bool@true to a variable else bool@false
    if(type_v == 'int'):
        arg1 = int(arg1)
        arg2 = int(arg2)
        if(arg1 < arg2):
            variables_dict[index] = ['bool', 'true']
            write_value(pc, instructions, 3, temporary, local, local_stack, 'true', 'bool')
        else:
            variables_dict[index] = ['bool', 'false']
            write_value(pc, instructions, 3, temporary, local, local_stack, 'false', 'bool')
    elif(type_v == 'string'):
        if(arg1 < arg2):
            variables_dict[index] = ['bool', 'true']
            write_value(pc, instructions, 3, temporary, local, local_stack, 'true', 'bool')
        else:
            variables_dict[index] = ['bool', 'false']
            write_value(pc, instructions, 3, temporary, local, local_stack, 'false', 'bool')
    else:
        if((arg1 == 'false' and arg2 == 'true')):
            variables_dict[index] = ['bool', 'true']
            write_value(pc, instructions, 3, temporary, local, local_stack, 'true', 'bool')
        else:
            variables_dict[index] = ['bool', 'false']
            write_value(pc, instructions, 3, temporary, local, local_stack, 'false', 'bool')

"""
Function for determining if a var1 is more than var2
:param var arg1 First argument
:param var arg2 Second argument
:param string type_v Type of a variables passed in
:param dict variables_dict Dictionary of used variables
:param var index Index of a stored instruction in the dictionary
:param int pc Program counter
:param var temporary Temporary frame class
:param var local Local frame class
:param deque local_stack Stack of local frames
"""
def GT_func(arg1, arg2, type_v, variables_dict, index , pc, temporary, local, local_stack):
    # If variable is more than the other, writes a bool@true to a variable else bool@false
    if(type_v == 'int'):
        arg1 = int(arg1)
        arg2 = int(arg2)
        if(arg1 > arg2):
            variables_dict[index] = ['bool', 'true']
            write_value(pc, instructions, 3, temporary, local, local_stack, 'true', 'bool')
        else:
            variables_dict[index] = ['bool', 'false']
            write_value(pc, instructions, 3, temporary, local, local_stack, 'false', 'bool')
    elif(type_v == 'string'):
        if(arg1 > arg2):
            variables_dict[index] = ['bool', 'true']
            write_value(pc, instructions, 3, temporary, local, local_stack, 'true', 'bool')
        else:
            variables_dict[index] = ['bool', 'false']
            write_value(pc, instructions, 3, temporary, local, local_stack, 'false', 'bool')
    else:
        if((arg1 == 'false' and arg2 == 'false') or (arg1 == 'true' and arg2 == 'true') or (arg1 == 'false' and arg2 == 'true')):
            variables_dict[index] = ['bool', 'false']
            write_value(pc, instructions, 3, temporary, local, local_stack, 'false', 'bool')
        else:
            variables_dict[index] = ['bool', 'true']
            write_value(pc, instructions, 3, temporary, local, local_stack, 'true', 'bool')

"""
Function for determining if a var1 is equal to var2
:param var arg1 First argument
:param var arg2 Second argument
:param string type_v Type of a variables passed in
:param dict variables_dict Dictionary of used variables
:param var index Index of a stored instruction in the dictionary
:param int pc Program counter
:param var temporary Temporary frame class
:param var local Local frame class
:param deque local_stack Stack of local frames
"""
def EQ_func(arg1, arg2, type_v1, type_v2, variables_dict, index, pc, temporary, local, local_stack):
    # If variable is equal to the other, writes a bool@true to a variable else bool@false
    if(type_v1 == 'int' and type_v2 == 'int'):
        arg1 = int(arg1)
        arg2 = int(arg2)
        if(arg1 == arg2):
            variables_dict[index] = ['bool', 'true']
            write_value(pc, instructions, 3, temporary, local, local_stack, 'true', 'bool')
        else:
            variables_dict[index] = ['bool', 'false']
            write_value(pc, instructions, 3, temporary, local, local_stack, 'false', 'bool')

    elif(type_v1 == 'string' or type_v1 == 'nil' or type_v2 == 'nil' or type_v1 == 'bool'):
        if(arg1 == arg2):
            variables_dict[index] = ['bool', 'true']
            write_value(pc, instructions, 3, temporary, local, local_stack, 'true', 'bool')
        else:
            variables_dict[index] = ['bool', 'false']
            write_value(pc, instructions, 3, temporary, local, local_stack, 'false', 'bool')

"""
Function for determining if a variable, label or frame is defined/exists.
:param int pc Program counter
:param var instructions Class holding all the instructions
:param int num_of_args Number of instructions's arguments
:param dict label_dict Dictionary holding all the labels
:param dict variables_dict Dictionary holding all used variables
:param var temp Temporary frame class
:param var local Local frame class
:param int temp_exists 1 if temporary frame exists 0 if not
:param deque local_stack Local frames stack
:returns errcodes if an error occurs
"""
def check_variables(pc, instructions, num_of_args, label_dict, variables_dict, temp, local, temp_exists, local_stack, local_exists, temp_in_use):
    if(num_of_args == 1):
        if(instructions[pc].args[0].arg_type == 'label'):
            if(not(instructions[pc].args[0].value in label_dict)):
                sys.stderr.write("Jumping on an undefined label!\n")
                exit(52)
        elif(instructions[pc].args[0].arg_type == 'var'):
            splitted = instructions[pc].args[0].value.split('@')
            if(splitted[0] == 'GF'):
                if(not(instructions[pc].args[0].value in variables_dict)):
                    sys.stderr.write("Undefined variable!\n")
                    exit(54)
            elif(splitted[0] == 'LF'):
                if(not(local_stack)):
                    sys.stderr.write("Non existing frame!\n")
                    exit(55)
                #pops the local stack to check if a var is present
                #pushes back right after
                local = local_stack.pop()
                if(not(splitted[1] in local.vars)):
                    sys.stderr.write("Undefined variable!\n")
                    exit(54)
                local_stack.append(local)
                

            elif(splitted[0] == 'TF'):
                if(temp_exists == 0):
                    sys.stderr.write("Frame not existing!\n")
                    exit(55)
                if(not(splitted[1] in temp.vars)):
                    sys.stderr.write("Undefined variable!\n")
                    exit(54)
                
    elif(num_of_args == 2):
        
        if(instructions[pc].args[0].arg_type == 'var'):
            
            splitted = instructions[pc].args[0].value.split('@')
            if(splitted[0] == 'GF'):
                
                if(not(instructions[pc].args[0].value in variables_dict)):
                    sys.stderr.write("Undefined variable!\n")
                    exit(54)
            elif(splitted[0] == 'LF'):
                if(not(local_stack)):
                    sys.stderr.write("Non existing frame!\n")
                    exit(55)
                local = local_stack.pop()
                if(not(splitted[1] in local.vars)):
                    sys.stderr.write("Undefined variable!\n")
                    exit(54)
                local_stack.append(local)
                
            elif(splitted[0] == 'TF'):

                if(temp_exists == 0):
                    sys.stderr.write("Frame not existing!\n")
                    exit(55)
                if(not(splitted[1] in temp.vars)):
                    sys.stderr.write("Undefined variable!\n")
                    exit(54)
                
        if(instructions[pc].args[1].arg_type == 'var'):
            
            splitted = instructions[pc].args[1].value.split('@')
            if(splitted[0] == 'GF'):
                if(not(instructions[pc].args[1].value in variables_dict)):
                    sys.stderr.write("Undefined variable!\n")
                    exit(54)

            elif(splitted[0] == 'LF'):
                if(not(local_stack)):
                    sys.stderr.write("Non existing frame!\n")
                    exit(55)
                local = local_stack.pop()
                if(not(splitted[1] in local.vars)):
                    sys.stderr.write("Undefined variable!\n")
                    exit(54)
                local_stack.append(local)
                

            elif(splitted[0] == 'TF'):
                if(temp_exists == 0):
                    sys.stderr.write("Frame not existing!\n")
                    exit(55)
                if(not(splitted[1] in temp.vars)):
                    sys.stderr.write("Undefined variable!\n")
                    exit(54)
        
    elif(num_of_args == 3):

        if(instructions[pc].args[0].arg_type == 'label'):
            if(not(instructions[pc].args[0].value in label_dict)):
                sys.stderr.write("Jumping on an undefined label!\n")
                exit(52)
        if(instructions[pc].args[0].arg_type == 'var'):
            
            splitted = instructions[pc].args[0].value.split('@')
            if(splitted[0] == 'GF'):
                
                if(not(instructions[pc].args[0].value in variables_dict)):
                    sys.stderr.write("Undefined variable!\n")
                    exit(54)
            elif(splitted[0] == 'LF'):
                if(not(local_stack)):
                    sys.stderr.write("Non existing frame!\n")
                    exit(55)
                local = local_stack.pop()
                if(not(splitted[1] in local.vars)):
                    sys.stderr.write("Undefined variable!\n")
                    exit(54)
                local_stack.append(local)
                
            elif(splitted[0] == 'TF'):

                if(temp_exists == 0):
                    sys.stderr.write("Frame not existing!\n")
                    exit(55)
                if(not(splitted[1] in temp.vars)):
                    sys.stderr.write("Undefined variable!\n")
                    exit(54)
                
        if(instructions[pc].args[1].arg_type == 'var'):
            
            splitted = instructions[pc].args[1].value.split('@')
            if(splitted[0] == 'GF'):
                if(not(instructions[pc].args[1].value in variables_dict)):
                    sys.stderr.write("Undefined variable!\n")
                    exit(54)

            elif(splitted[0] == 'LF'):
                if(not(local_stack)):
                    sys.stderr.write("Non existing frame!\n")
                    exit(55)
                local = local_stack.pop()
                if(not(splitted[1] in local.vars)):
                    sys.stderr.write("Undefined variable!\n")
                    exit(54)
                local_stack.append(local)
                
            elif(splitted[0] == 'TF'):
                if(temp_exists == 0):
                    sys.stderr.write("Frame not existing!\n")
                    exit(55)
                if(not(splitted[1] in temp.vars)):
                    sys.stderr.write("Undefined variable!\n")
                    exit(54)

        if(instructions[pc].args[2].arg_type == 'var'):
            
            splitted = instructions[pc].args[2].value.split('@')
            if(splitted[0] == 'GF'):
                if(not(instructions[pc].args[2].value in variables_dict)):
                    sys.stderr.write("Undefined variable!\n")
                    exit(54)

            elif(splitted[0] == 'LF'):
                if(not(local_stack)):
                    sys.stderr.write("Non existing frame!\n")
                    exit(55)
                local = local_stack.pop()
                if(not(splitted[1] in local.vars)):
                    sys.stderr.write("Undefined variable!\n")
                    exit(54)
                local_stack.append(local)
                
            elif(splitted[0] == 'TF'):
                if(temp_exists == 0):
                    sys.stderr.write("Frame not existing!\n")
                    exit(55)
                if(not(splitted[1] in temp.vars)):
                    sys.stderr.write("Undefined variable!\n")
                    exit(54)
        
"""
Arithmetical variables check if initialized or are the right type
:param var var_type Type of a variable
:param string type_v Type which a variable must be
"""
def var_check_init(instruction, variables_dict, var_type, type_v):
    if((var_type == -1)):
        sys.stderr.write("Uninitialized variable!\n")
        exit(56)

    if((var_type != type_v)):
        sys.stderr.write("Wrong type of variable!\n")
        exit(53)

"""
Logical variables check if initialized or are the right type
:param string type_v Type of a variable
"""
def var_check_log(type_v, variables_dict):
    
    if((type_v == -1)):
        sys.stderr.write("Uninitialized variable!\n")
        exit(56)
    
    if(type_v == 'nil'):
        sys.stderr.write("Nil type variable\n")
        exit(53)

"""
Equal instruction variables check if initialized
:param string type_v Type of a variable
"""
def var_check_eq(type_v):
    if((type_v == -1)):
        sys.stderr.write("Uninitialized variable!\n")
        exit(56)

"""
AND and OR logical instructions variables check if initialized and are the right type
:param var type_v1 Type of a 1st variable
:param var type_v2 Type of a 2nd variable
"""
def and_or_var_check(type_v1, type_v2):
    if((type_v1 == -1) or (type_v2 == -1)):
        sys.stderr.write("Uninitialized variable!\n")
        exit(56)

    if(type_v1 != 'bool' or type_v2 != 'bool'):
        sys.stderr.write("Different types of variables!\n")
        exit(53)

"""
Gets a value from the corresponding frame. Is called after all vars are checked if defined
:param int pc Program counter
:param var instructions Instructions class
:param int num_of_args Number of the instructions's arguments
:param dict variables_dict Dictionary of all used variables
:param var temp Temporary frame class
:param var local Local frame class
:param deque local_stack Local frame stack
:return var value Value of the variable
:return var type_v Type of the variable
"""
def get_value(pc, instructions, num_of_args, variables_dict, temp, local, local_stack):
    if(num_of_args == 1):
        if(instructions[pc].args[0].arg_type == 'var'):
            
            splitted = instructions[pc].args[0].value.split('@')
            if(splitted[0] == 'GF'):
                
                value = variables_dict[instructions[pc].args[0].value][1]
                type_v = variables_dict[instructions[pc].args[0].value][0]
                return value, type_v
            elif(splitted[0] == 'LF'):
                local = local_stack.pop()
                value = local.vars[splitted[1]][1]
                type_v = local.vars[splitted[1]][0]
                local_stack.append(local)
                return value , type_v
                
            elif(splitted[0] == 'TF'):

                value = temp.vars[splitted[1]][1]
                type_v = temp.vars[splitted[1]][0]
                return value, type_v
    if(num_of_args == 2):
        if(instructions[pc].args[1].arg_type == 'var'):
            
            splitted = instructions[pc].args[1].value.split('@')
            if(splitted[0] == 'GF'):
                
                value = variables_dict[instructions[pc].args[1].value][1]
                type_v = variables_dict[instructions[pc].args[1].value][0]
                return value, type_v
            elif(splitted[0] == 'LF'):
                local = local_stack.pop()
                value = local.vars[splitted[1]][1]
                type_v = local.vars[splitted[1]][0]
                local_stack.append(local)
                return value , type_v
                
            elif(splitted[0] == 'TF'):

                value = temp.vars[splitted[1]][1]
                type_v = temp.vars[splitted[1]][0]
                return value, type_v
    if(num_of_args == 3 or num_of_args == 4):
        if(instructions[pc].args[1].arg_type == 'var' and num_of_args != 4):
            num_of_args = 4
            splitted = instructions[pc].args[1].value.split('@')
            if(splitted[0] == 'GF'):
                
                value = variables_dict[instructions[pc].args[1].value][1]
                type_v = variables_dict[instructions[pc].args[1].value][0]
                return value, type_v
            elif(splitted[0] == 'LF'):
                local = local_stack.pop()
                value = local.vars[splitted[1]][1]
                type_v = local.vars[splitted[1]][0]
                local_stack.append(local)
                return value , type_v
                
            elif(splitted[0] == 'TF'):

                value = temp.vars[splitted[1]][1]
                type_v = temp.vars[splitted[1]][0]
                return value, type_v

        if(instructions[pc].args[2].arg_type == 'var' and num_of_args == 4):
            
            splitted = instructions[pc].args[2].value.split('@')
            if(splitted[0] == 'GF'):
                
                value = variables_dict[instructions[pc].args[2].value][1]
                type_v = variables_dict[instructions[pc].args[2].value][0]
                return value, type_v
            elif(splitted[0] == 'LF'):
                local = local_stack.pop()
                value = local.vars[splitted[1]][1]
                type_v = local.vars[splitted[1]][0]
                local_stack.append(local)
                return value , type_v
                
            elif(splitted[0] == 'TF'):

                value = temp.vars[splitted[1]][1]
                type_v = temp.vars[splitted[1]][0]
                return value, type_v

"""
Writes changed value of a variable back to its corresponding frame
:param int pc Program counter
:param var Instructions istructions class
:param var temp Temporary frame class
:param var local Local frame class
:param deque local_stack Local frames stack
:param var value Value of the variable
:param var type_v type of the variable
"""
def write_value(pc, instructions, num_of_args, temp, local, local_stack, value, type_v):        
    if(instructions[pc].args[0].arg_type == 'var'):
        
        splitted = instructions[pc].args[0].value.split('@')
        if(splitted[0] == 'LF'):
            local = local_stack.pop()
            local.vars[splitted[1]] = [type_v ,value]
            local_stack.append(local)
            
        elif(splitted[0] == 'TF'):

            temp.vars[splitted[1]] = [type_v ,value]
                




#-------------------------------------------------------------
#                        INTERPRETACE
#-------------------------------------------------------------

# dictionary access:
#                   0-type of the var
#                   1-value of the var 
# 
# args access:
#                   0-arg1
#                   1-arg2
#                   2-arg3    

"""
Iterprets the given code in IPPcode22
:param var instructions Instructions class
:param dict variables_dict Dictionary of all used variables
:param int num_of_ins Number of all instructions used in this program
"""
def my_interpret(instructions, variables_dict, num_of_ins):
    pc = 0
    label_jump = 0
    label_dict = {}
    temp_exists = 0
    local_exists = 0
    local_stack = deque()
    call_stack = deque()
    symb_stack = deque()
    temporary = 0
    local = 0
    temp_in_use = 0

    #get all positions of lables
    for i in instructions:
        if(i.name == 'LABEL'):
            if(i.args[0].value in label_dict):
                sys.stderr.write("Redefining a label!\n")
                exit(52)
            if(i.args[0].arg_type != 'label'):
                sys.stderr.write("Wrong type!\n")
                exit(53)
            label_dict[i.args[0].value] = [pc]
        pc = pc + 1

    if(is_input == True):
        f = open(source_arg, 'r')
    
    pc = 0
    #loop until all instructions are interpreted
    while pc != num_of_ins:

        in_name = instructions[pc].name.upper()

        #moves a constant or a var to another var
        if(in_name == 'MOVE'):
            check_variables(pc, instructions, 2, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)
            if(instructions[pc].args[1].arg_type != "var"):
                #writes the var to currently used vars and to its corresponding frame
                variables_dict[instructions[pc].args[0].value] = [instructions[pc].args[1].arg_type, instructions[pc].args[1].value]
                write_value(pc, instructions, 2, temporary, local, local_stack, variables_dict[instructions[pc].args[0].value][1], variables_dict[instructions[pc].args[0].value][0])
                pc = pc + 1
            else:
                value, type_v = get_value(pc, instructions, 2, variables_dict, temporary, local, local_stack)
                if(type_v == -1):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)
                variables_dict[instructions[pc].args[0].value] = [type_v , value]
                write_value(pc, instructions, 2, temporary, local, local_stack, variables_dict[instructions[pc].args[0].value][1], variables_dict[instructions[pc].args[0].value][0])
                pc = pc + 1

        #creates a temporary frame
        elif(in_name == 'CREATEFRAME'):
            if(temp_exists == 0):
                temporary = TemporaryFrame()
                temp_exists = 1

            elif(temp_exists == 1):
                temporary.new_frame()
                temp_exists = 1
            pc = pc + 1

        #pushed temporary frame to stack of local frames, creating a local frame
        elif(in_name == 'PUSHFRAME'):
            if(temp_exists == 0):
                sys.stderr.write("Undefined frame!\n")
                exit(55)
            
            local = LocalFrame()
            local = temporary
            
            local_exists = local_exists + 1
            temp_exists = 0
            temp_in_use = 1
            local_stack.append(local)

            pc = pc + 1

        #pops a local frame drom stack of local frames, makes it a temporary
        elif(in_name == 'POPFRAME'):
            if(not(local_stack)):
                sys.stderr.write("No frame to pop!\n")
                exit(55)

            temporary = TemporaryFrame()
            temporary = local_stack.pop()
            temp_in_use = 0
            temp_exists = 1
            local_exists = local_exists - 1

            pc = pc + 1

        #defines a new variable, its written in its corresponding frame
        #makes in unitialized with -1 as its type and -1 as its value
        #using this its checked if the variable is initialized
        elif(in_name == 'DEFVAR'):
            splitted = instructions[pc].args[0].value.split('@')
            if(splitted[0] == 'GF'):
                if(instructions[pc].args[0].value in variables_dict):
                    sys.stderr.write("Redefining a variable!\n")
                    exit(52)
                variables_dict[instructions[pc].args[0].value] = [-1, -1]
            elif(splitted[0] == 'TF'):
                if(temp_exists == 0):
                    sys.stderr.write("Non existing frame!\n")
                    exit(55)

                if(splitted[1] in temporary.vars):
                    sys.stderr.write("Redefining a variable!\n")
                    exit(52)
                temporary.add_var(splitted[1], -1, -1)
                
            elif(splitted[0] == 'LF'):
                if(not(local_stack) or local_exists == 0):
                    sys.stderr.write("Non existing frame!\n")
                    exit(55)
                if(splitted[1] in local.vars):
                    sys.stderr.write("Redefining a variable!\n")
                    exit(52)
                
                local = local_stack.pop()
                local.add_var(splitted[1], -1, -1)
                local_stack.append(local)
                
            
            pc = pc + 1
            
        #calls a label, stores the last known location 
        elif(in_name == 'CALL'):
            check_variables(pc, instructions, 1, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)
            a = pc + 1
            call_stack.append(a)
            label_jump = label_dict[instructions[pc].args[0].value]
            pc = label_jump[0] + 1
            
        #returns to the last known location
        elif(in_name == 'RETURN'):
            if(not(call_stack)):
                sys.stderr.write("No position to be returned to!\n")
                exit(56)
            pc = call_stack.pop()

        #pushes a value to the value stack
        elif(in_name == 'PUSHS'):
            check_variables(pc, instructions, 1, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)
            if(instructions[pc].args[0].arg_type== 'var'): 
                value0, type_v0 = get_value(pc,instructions, 1, variables_dict, temporary, local, local_stack)

                if(type_v0 == -1):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)

                symb_stack.append([type_v0, value0])
            else: 
                symb_stack.append([instructions[pc].args[0].arg_type, instructions[pc].args[0].value])
            pc = pc + 1

        #pops a value from thw value stack 
        elif(in_name == 'POPS'): # TODO !!!!!!!!
            
            check_variables(pc, instructions, 1, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)
            if(not(symb_stack)): 
                sys.stderr.write("No value to be popped!\n") 
                exit(56)
            i = symb_stack.pop()
            variables_dict[instructions[pc].args[0].value] = i
            write_value(pc, instructions, 3, temporary, local, local_stack, i[1], i[0])                
            pc = pc + 1
            
        #adds two ints 
        elif(in_name == 'ADD'):
            check_variables(pc, instructions, 3, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)
            #based on type of arguments we choose an appropriate combination and interpret this instruction
            if(instructions[pc].args[1].arg_type == 'var' and instructions[pc].args[2].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                var_check_init(value1, variables_dict,type_v1, 'int')
                var_check_init(value2, variables_dict,type_v2 ,'int')

                result = int(value1) + int(value2)

                variables_dict[instructions[pc].args[0].value] = ['int', result]
                write_value(pc, instructions, 3, temporary, local, local_stack, result, 'int')

            elif(instructions[pc].args[1].arg_type == 'var'):
                value1, type_v = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                var_check_init(value1, variables_dict,type_v, 'int')

                result = int(value1) + int(instructions[pc].args[2].value)

                variables_dict[instructions[pc].args[0].value] = ['int', result]
                write_value(pc, instructions, 3, temporary, local, local_stack, result, 'int')

            elif(instructions[pc].args[2].arg_type == 'var'):
                value2, type_v = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                var_check_init(value2, variables_dict,type_v, 'int')

                result = int(instructions[pc].args[1].value) + int(value2)
                
                variables_dict[instructions[pc].args[0].value] = ['int', result]
                write_value(pc, instructions, 3, temporary, local, local_stack, result, 'int')
            else:

                if(instructions[pc].args[1].arg_type != 'int' or instructions[pc].args[2].arg_type != 'int'):
                    sys.stderr.write("Wrong type of variable!\n")
                    exit(53)

                result = int(instructions[pc].args[1].value) + int(instructions[pc].args[2].value)

                variables_dict[instructions[pc].args[0].value] = ['int', result]
                write_value(pc, instructions, 3, temporary, local, local_stack, result, 'int')

            pc = pc + 1

        # subs two ints
        elif(in_name == 'SUB'):
            check_variables(pc, instructions, 3, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)

            if(instructions[pc].args[1].arg_type == 'var' and instructions[pc].args[2].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                var_check_init(value1, variables_dict,type_v1, 'int')
                var_check_init(value2, variables_dict,type_v2 ,'int')

                result = int(value1) - int(value2)

                variables_dict[instructions[pc].args[0].value] = ['int', result]
                write_value(pc, instructions, 3, temporary, local, local_stack, result, 'int')

            elif(instructions[pc].args[1].arg_type == 'var'):
                value1, type_v = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                var_check_init(value1, variables_dict,type_v, 'int')

                result = int(value1) - int(instructions[pc].args[2].value)

                variables_dict[instructions[pc].args[0].value] = ['int', result]
                write_value(pc, instructions, 3, temporary, local, local_stack, result, 'int')

            elif(instructions[pc].args[2].arg_type == 'var'):
                value2, type_v = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                var_check_init(value2, variables_dict,type_v, 'int')

                result = int(instructions[pc].args[1].value) - int(value2)

                variables_dict[instructions[pc].args[0].value] = ['int', result]
                write_value(pc, instructions, 3, temporary, local, local_stack, result, 'int')
            else:

                if(instructions[pc].args[1].arg_type != 'int' or instructions[pc].args[2].arg_type != 'int'):
                    sys.stderr.write("Wrong type of variable!\n")
                    exit(53)

                result = int(instructions[pc].args[1].value) - int(instructions[pc].args[2].value)
                
                variables_dict[instructions[pc].args[0].value] = ['int', result]
                write_value(pc, instructions, 3, temporary, local, local_stack, result, 'int')
            
            pc = pc + 1

        #multiplies two ints
        elif(in_name == 'MUL'):
            check_variables(pc, instructions, 3, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)

            if(instructions[pc].args[1].arg_type == 'var' and instructions[pc].args[2].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                var_check_init(value1, variables_dict,type_v1, 'int')
                var_check_init(value2, variables_dict,type_v2 ,'int')

                result = int(value1) * int(value2)

                variables_dict[instructions[pc].args[0].value] = ['int', result]
                write_value(pc, instructions, 3, temporary, local, local_stack, result, 'int')

            elif(instructions[pc].args[1].arg_type == 'var'):
                value1, type_v = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                var_check_init(value1, variables_dict,type_v, 'int')

                result = int(value1) * int(instructions[pc].args[2].value)

                variables_dict[instructions[pc].args[0].value] = ['int', result]
                write_value(pc, instructions, 3, temporary, local, local_stack, result, 'int')

            elif(instructions[pc].args[2].arg_type == 'var'):
                value2, type_v = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                var_check_init(value2, variables_dict, type_v, 'int')

                result = int(instructions[pc].args[1].value) * int(value2)

                variables_dict[instructions[pc].args[0].value] = ['int', result]
                write_value(pc, instructions, 3, temporary, local, local_stack, result, 'int')
            else:

                if(instructions[pc].args[1].arg_type != 'int' or instructions[pc].args[2].arg_type != 'int'):
                    sys.stderr.write("Wrong type of variable!\n")
                    exit(53)

                result = int(instructions[pc].args[1].value) * int(instructions[pc].args[2].value)
                
                variables_dict[instructions[pc].args[0].value] = ['int', result]
                write_value(pc, instructions, 3, temporary, local, local_stack, result, 'int')

            pc = pc + 1

        # divides two ints
        elif(in_name == 'IDIV'):
            check_variables(pc, instructions, 3, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)

            if(instructions[pc].args[1].arg_type == 'var' and instructions[pc].args[2].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                var_check_init(value1, variables_dict, type_v1, 'int')
                var_check_init(value2, variables_dict, type_v2 ,'int')
                if(int(value2) == 0):
                    sys.stderr.write("Division by zero!\n")
                    exit(57)

                result = int(value1) // int(value2)

                variables_dict[instructions[pc].args[0].value] = ['int', result]
                write_value(pc, instructions, 3, temporary, local, local_stack, result, 'int')

            elif(instructions[pc].args[1].arg_type == 'var'):
                value1, type_v = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                var_check_init(value1, variables_dict,type_v, 'int')
                if(int(instructions[pc].args[2].value) == 0):
                    sys.stderr.write("Division by zero!\n")
                    exit(57)

                result = int(value1) // int(instructions[pc].args[2].value)

                variables_dict[instructions[pc].args[0].value] = ['int', result]
                write_value(pc, instructions, 3, temporary, local, local_stack, result, 'int')

            elif(instructions[pc].args[2].arg_type == 'var'):
                value2, type_v = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                var_check_init(value2, variables_dict,type_v, 'int')
                if(int(value2) == 0):
                    sys.stderr.write("Division by zero!\n")
                    exit(57)

                result = int(instructions[pc].args[1].value) // int(value2)

                variables_dict[instructions[pc].args[0].value] = ['int', result]
                write_value(pc, instructions, 3, temporary, local, local_stack, result, 'int')
            else:

                if(instructions[pc].args[1].arg_type != 'int' or instructions[pc].args[2].arg_type != 'int'):
                    sys.stderr.write("Wrong type of variable!\n")
                    exit(53)
                if(int(instructions[pc].args[2].value) == 0):
                    sys.stderr.write("Division by zero!\n")
                    exit(57)

                result = int(instructions[pc].args[1].value) // int(instructions[pc].args[2].value)
                
                variables_dict[instructions[pc].args[0].value] = ['int', result]
                write_value(pc, instructions, 3, temporary, local, local_stack, result, 'int')

            pc = pc + 1

        #compares two operads if one is less than the other
        elif(in_name == 'LT'):
            check_variables(pc, instructions, 3, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)
            index = instructions[pc].args[0].value
            if(instructions[pc].args[1].arg_type == 'var' and instructions[pc].args[2].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                var_check_log(type_v1, variables_dict)
                var_check_log(type_v2, variables_dict)
                if(type_v1 != type_v2):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                if(value1 is None):
                    value1 = ''
                if(value2 is None):
                    value2 = ''

                if(type_v1 == "string"):
                    value1 = str_replace(value1)
                if(type_v2 == "string"):
                    value2 = str_replace(value2)

                arg1 = value1
                arg2 = value2
                LT_func(arg1, arg2, type_v1,variables_dict ,index, pc, temporary, local, local_stack)
                
            elif(instructions[pc].args[1].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                var_check_log(type_v1, variables_dict)
                if(type_v1 != instructions[pc].args[2].arg_type):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                if(value1 is None):
                    value1 = ''
                if(instructions[pc].args[2].value is None):
                    instructions[pc].args[2].value = ''

                if(type_v1 == "string"):
                    value1 = str_replace(value1)
                if(instructions[pc].args[2].arg_type == "string"):
                    instructions[pc].args[2].value = str_replace(instructions[pc].args[2].value)
                
                arg1 = value1
                arg2 = instructions[pc].args[2].value
                LT_func(arg1, arg2, type_v1,variables_dict ,index, pc, temporary, local, local_stack)

            elif(instructions[pc].args[2].arg_type == 'var'):
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                var_check_log(type_v2, variables_dict)
                if(type_v2 != instructions[pc].args[1].arg_type):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                if(value2 is None):
                    value2 = ''
                if(instructions[pc].args[1].value is None):
                    instructions[pc].args[1].value = ''
                
                if(type_v2 == "string"):
                    value2 = str_replace(value2)
                if(instructions[pc].args[1].arg_type == "string"):
                    instructions[pc].args[1].value = str_replace(instructions[pc].args[1].value)

                arg2 = value2
                arg1 = instructions[pc].args[1].value
                LT_func(arg1, arg2, type_v2,variables_dict ,index, pc, temporary, local, local_stack)
                
            else:
                if((instructions[pc].args[2].arg_type == 'nil') or (instructions[pc].args[1].arg_type == 'nil')):
                    sys.stderr.write("Nil type variable\n")
                    exit(53)

                if(instructions[pc].args[2].arg_type != instructions[pc].args[1].arg_type):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                if(instructions[pc].args[1].value is None):
                    instructions[pc].args[1].value = ''
                if(instructions[pc].args[2].value is None):
                    instructions[pc].args[2].value = ''

                if(instructions[pc].args[2].arg_type == "string"):
                    instructions[pc].args[2].value = str_replace(instructions[pc].args[2].value)
                if(instructions[pc].args[1].arg_type == "string"):
                    instructions[pc].args[1].value = str_replace(instructions[pc].args[1].value)

                arg1 = instructions[pc].args[1].value
                arg2 = instructions[pc].args[2].value
                type_v = instructions[pc].args[1].arg_type
                LT_func(arg1, arg2, type_v,variables_dict ,index, pc, temporary, local, local_stack)

            pc = pc + 1

        #compares two operads if one is greater than the other
        elif(in_name == 'GT'):
            check_variables(pc, instructions, 3, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)
            index = instructions[pc].args[0].value
            if(instructions[pc].args[1].arg_type == 'var' and instructions[pc].args[2].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                var_check_log(type_v1, variables_dict)
                var_check_log(type_v2, variables_dict)
                if(type_v1 != type_v2):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                if(value1 is None):
                    value1 = ''
                if(value2 is None):
                    value2 = ''

                if(type_v1 == "string"):
                    value1 = str_replace(value1)
                if(type_v2 == "string"):
                    value2 = str_replace(value2)

                arg1 = value1
                arg2 = value2
                GT_func(arg1, arg2, type_v1,variables_dict ,index, pc, temporary, local, local_stack)
                
            elif(instructions[pc].args[1].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                var_check_log(type_v1, variables_dict)
                if(type_v1 != instructions[pc].args[2].arg_type):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                if(value1 is None):
                    value1 = ''
                if(instructions[pc].args[2].value is None):
                    instructions[pc].args[2].value = ''

                if(type_v1 == "string"):
                    value1 = str_replace(value1)
                if(instructions[pc].args[2].arg_type == "string"):
                    instructions[pc].args[2].value = str_replace(instructions[pc].args[2].value)
                
                arg1 = value1
                arg2 = instructions[pc].args[2].value
                GT_func(arg1, arg2, type_v1,variables_dict ,index, pc, temporary, local, local_stack)

            elif(instructions[pc].args[2].arg_type == 'var'):
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                var_check_log(type_v2, variables_dict)
                if(type_v2 != instructions[pc].args[1].arg_type):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                if(value2 is None):
                    value2 = ''
                if(instructions[pc].args[1].value is None):
                    instructions[pc].args[1].value = ''

                if(type_v2 == "string"):
                    value2 = str_replace(value2)
                if(instructions[pc].args[1].arg_type == "string"):
                    instructions[pc].args[1].value = str_replace(instructions[pc].args[1].value)

                arg2 = value2
                arg1 = instructions[pc].args[1].value
                GT_func(arg1, arg2, type_v2,variables_dict ,index, pc, temporary, local, local_stack)
                
            else:
                if((instructions[pc].args[2].arg_type == 'nil') or (instructions[pc].args[1].arg_type == 'nil')):
                    sys.stderr.write("Nil type variable\n")
                    exit(53)

                if(instructions[pc].args[2].arg_type != instructions[pc].args[1].arg_type):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                if(instructions[pc].args[1].value is None):
                    instructions[pc].args[1].value = ''
                if(instructions[pc].args[2].value is None):
                    instructions[pc].args[2].value = ''

                if(instructions[pc].args[2].arg_type == "string"):
                    instructions[pc].args[2].value = str_replace(instructions[pc].args[2].value)
                if(instructions[pc].args[1].arg_type == "string"):
                    instructions[pc].args[1].value = str_replace(instructions[pc].args[1].value)

                arg1 = instructions[pc].args[1].value
                arg2 = instructions[pc].args[2].value
                type_v = instructions[pc].args[1].arg_type
                GT_func(arg1, arg2, type_v,variables_dict ,index, pc, temporary, local, local_stack)

            pc = pc + 1

        #compares two operads if one is equal to the other
        elif(in_name == 'EQ'):
            check_variables(pc, instructions, 3, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)
            index = instructions[pc].args[0].value
            if(instructions[pc].args[1].arg_type == 'var' and instructions[pc].args[2].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                var_check_eq(type_v1)
                var_check_eq(type_v2)
                if(type_v1 != type_v2 and (type_v1 != 'nil' and type_v2 != 'nil')):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                if(value1 is None):
                    value1 = ''
                if(value2 is None):
                    value2 = ''
                
                if(type_v1 == "string"):
                    value1 = str_replace(value1)
                if(type_v2 == "string"):
                    value2 = str_replace(value2)
                
                arg1 = value1
                arg2 = value2
                EQ_func(arg1, arg2, type_v1, type_v2,variables_dict ,index, pc, temporary, local, local_stack)
                
            elif(instructions[pc].args[1].arg_type == 'var'):
                
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                var_check_eq(type_v1)
                if(type_v1 != instructions[pc].args[2].arg_type and (type_v1 != 'nil' and instructions[pc].args[2].arg_type != 'nil')):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                if(value1 is None):
                    value1 = ''
                if(instructions[pc].args[2].value is None):
                    instructions[pc].args[2].value = ''

                if(type_v1 == "string"):
                    value1 = str_replace(value1)
                if(instructions[pc].args[2].arg_type == "string"):
                    instructions[pc].args[2].value = str_replace(instructions[pc].args[2].value)
                
                arg1 = value1
                arg2 = instructions[pc].args[2].value
                type_v2 = instructions[pc].args[2].arg_type
                EQ_func(arg1, arg2, type_v1, type_v2,variables_dict ,index, pc, temporary, local, local_stack)

            elif(instructions[pc].args[2].arg_type == 'var'):
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                var_check_eq(type_v2)
                if(type_v2 != instructions[pc].args[1].arg_type and (type_v2 != 'nil' and instructions[pc].args[1].arg_type != 'nil')):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                if(value2 is None):
                    value2 = ''
                if(instructions[pc].args[1].value is None):
                    instructions[pc].args[1].value = ''
                
                if(type_v2 == "string"):
                    value2 = str_replace(value2)
                if(instructions[pc].args[1].arg_type == "string"):
                    instructions[pc].args[1].value = str_replace(instructions[pc].args[1].value)
                
                arg1 = instructions[pc].args[1].value
                arg2 = value2
                type_v1 = instructions[pc].args[1].arg_type
                EQ_func(arg1, arg2, type_v1, type_v2,variables_dict ,index, pc, temporary, local, local_stack)
                
            else:

                if(instructions[pc].args[2].arg_type != instructions[pc].args[1].arg_type and ((instructions[pc].args[2].arg_type != 'nil') and (instructions[pc].args[1].arg_type != 'nil'))):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                if(instructions[pc].args[1].value is None):
                    instructions[pc].args[1].value = ''
                if(instructions[pc].args[2].value is None):
                    instructions[pc].args[2].value = ''

                if(instructions[pc].args[2].arg_type == "string"):
                    instructions[pc].args[2].value = str_replace(instructions[pc].args[2].value)
                if(instructions[pc].args[1].arg_type == "string"):
                    instructions[pc].args[1].value = str_replace(instructions[pc].args[1].value)

                arg1 = instructions[pc].args[1].value
                arg2 = instructions[pc].args[2].value
                type_v1 = instructions[pc].args[1].arg_type
                type_v2 = instructions[pc].args[2].arg_type
                EQ_func(arg1, arg2, type_v1, type_v2,variables_dict ,index, pc, temporary, local, local_stack)

            pc = pc + 1

        # logical AND
        elif(in_name == 'AND'):
            check_variables(pc, instructions, 3, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)
            if(instructions[pc].args[1].arg_type == 'var' and instructions[pc].args[2].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                and_or_var_check(type_v1,type_v2)
                if(value1 == 'true' and  value2 == 'true'):
                    variables_dict[instructions[pc].args[0].value] = ['bool', 'true']
                    write_value(pc, instructions, 3, temporary, local, local_stack, 'true', 'bool')
                else:
                    variables_dict[instructions[pc].args[0].value] = ['bool', 'false']
                    write_value(pc, instructions, 3, temporary, local, local_stack, 'false', 'bool')
                
            elif(instructions[pc].args[1].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                and_or_var_check(type_v1, instructions[pc].args[2].arg_type)

                if(value1 == 'true' and  instructions[pc].args[2].value == 'true'):
                    variables_dict[instructions[pc].args[0].value] = ['bool', 'true']
                    write_value(pc, instructions, 3, temporary, local, local_stack, 'true', 'bool')
                else:
                    variables_dict[instructions[pc].args[0].value] = ['bool', 'false']
                    write_value(pc, instructions, 3, temporary, local, local_stack, 'false', 'bool')

            elif(instructions[pc].args[2].arg_type == 'var'):
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                and_or_var_check(instructions[pc].args[1].arg_type, type_v2 )

                if(value2 == 'true' and  instructions[pc].args[1].value == 'true'):
                    variables_dict[instructions[pc].args[0].value] = ['bool', 'true']
                    write_value(pc, instructions, 3, temporary, local, local_stack, 'true', 'bool')
                else:
                    variables_dict[instructions[pc].args[0].value] = ['bool', 'false']
                    write_value(pc, instructions, 3, temporary, local, local_stack, 'false', 'bool')
            else:
                if(instructions[pc].args[1].arg_type != 'bool' or instructions[pc].args[2].arg_type != 'bool'):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                if(instructions[pc].args[1].value == 'true' and  instructions[pc].args[2].value == 'true'):
                    variables_dict[instructions[pc].args[0].value] = ['bool', 'true']
                    write_value(pc, instructions, 3, temporary, local, local_stack, 'true', 'bool')
                else:
                    variables_dict[instructions[pc].args[0].value] = ['bool', 'false']
                    write_value(pc, instructions, 3, temporary, local, local_stack, 'false', 'bool')

            pc = pc + 1

        # logical OR
        elif(in_name == 'OR'):
            check_variables(pc, instructions, 3, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)
            if(instructions[pc].args[1].arg_type == 'var' and instructions[pc].args[2].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                and_or_var_check(type_v1,type_v2)
                if((value1 == 'true' and  value2  == 'true') or (value1 == 'false' and  value2  == 'true') or (value1 == 'true' and value2  == 'false')):    
                    variables_dict[instructions[pc].args[0].value] = ['bool', 'true']
                    write_value(pc, instructions, 3, temporary, local, local_stack, 'true', 'bool')
                else:
                    variables_dict[instructions[pc].args[0].value] = ['bool', 'false']
                    write_value(pc, instructions, 3, temporary, local, local_stack, 'false', 'bool')
                
            elif(instructions[pc].args[1].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                and_or_var_check(type_v1, instructions[pc].args[2].arg_type)

                if((value1 == 'true' and  instructions[pc].args[2].value == 'true') or (value1 == 'false' and  instructions[pc].args[2].value == 'true') or (value1 == 'true' and  instructions[pc].args[2].value == 'false')):
                    variables_dict[instructions[pc].args[0].value] = ['bool', 'true']
                    write_value(pc, instructions, 3, temporary, local, local_stack, 'true', 'bool')
                else:
                    variables_dict[instructions[pc].args[0].value] = ['bool', 'false']
                    write_value(pc, instructions, 3, temporary, local, local_stack, 'false', 'bool')

            elif(instructions[pc].args[2].arg_type == 'var'):
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                and_or_var_check(instructions[pc].args[1].arg_type, type_v2 )

                if((value2 == 'true' and  instructions[pc].args[1].value == 'true') or (value2 == 'false' and  instructions[pc].args[1].value == 'true') or (value2 == 'true' and  instructions[pc].args[1].value == 'false')):
                    variables_dict[instructions[pc].args[0].value] = ['bool', 'true']
                    write_value(pc, instructions, 3, temporary, local, local_stack, 'true', 'bool')
                else:
                    variables_dict[instructions[pc].args[0].value] = ['bool', 'false']
                    write_value(pc, instructions, 3, temporary, local, local_stack, 'false', 'bool')
            else:
                if(instructions[pc].args[1].arg_type != 'bool' or instructions[pc].args[2].arg_type != 'bool'):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                if((instructions[pc].args[1].value == 'true' and  instructions[pc].args[2].value == 'true') or (instructions[pc].args[1].value == 'false' and  instructions[pc].args[2].value == 'true') or (instructions[pc].args[1].value == 'true' and  instructions[pc].args[2].value == 'false')):
                    variables_dict[instructions[pc].args[0].value] = ['bool', 'true']
                    write_value(pc, instructions, 3, temporary, local, local_stack, 'true', 'bool')
                else:
                    variables_dict[instructions[pc].args[0].value] = ['bool', 'false']
                    write_value(pc, instructions, 3, temporary, local, local_stack, 'false', 'bool')

            pc = pc + 1

        # logical NOT
        elif(in_name == 'NOT'):
            check_variables(pc, instructions, 2, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)
            if(instructions[pc].args[1].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 2, variables_dict, temporary, local, local_stack)
                if(type_v1 == -1):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)

                if(type_v1 != 'bool'):
                    sys.stderr.write("Wrong variable type!\n")
                    exit(53)

                if(value1 == 'true'):
                    variables_dict[instructions[pc].args[0].value] = ['bool', 'false']
                    write_value(pc, instructions, 2, temporary, local, local_stack, 'false', 'bool')
                else:
                    variables_dict[instructions[pc].args[0].value] = ['bool', 'true']
                    write_value(pc, instructions, 2, temporary, local, local_stack, 'true', 'bool')

            else:
                if(instructions[pc].args[1].arg_type != 'bool'):
                    sys.stderr.write("Wrong variable type!\n")
                    exit(53)

                if(instructions[pc].args[1].value == 'true'):
                    variables_dict[instructions[pc].args[0].value] = ['bool', 'false']
                    write_value(pc, instructions, 2, temporary, local, local_stack, 'false', 'bool')
                else:
                    variables_dict[instructions[pc].args[0].value] = ['bool', 'true']
                    write_value(pc, instructions, 2, temporary, local, local_stack, 'true', 'bool')
            
            pc = pc + 1

        # converts integer to character
        elif(in_name == 'INT2CHAR'):
            check_variables(pc, instructions, 2, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)
            if(instructions[pc].args[1].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 2, variables_dict, temporary, local, local_stack)
                if(type_v1 == -1):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)
                
                if(type_v1 != 'int'):
                    sys.stderr.write("Wrong variable type!\n")
                    exit(53)

                convert = int(value1)
                try:
                    result = chr(convert)
                except ValueError:
                    sys.stderr.write("Character out of range!\n")
                    exit(58)

                variables_dict[instructions[pc].args[0].value] = ['string', result]
                write_value(pc, instructions, 2, temporary, local, local_stack, result, 'string')

            else:
                if(instructions[pc].args[1].arg_type != 'int'):
                    sys.stderr.write("Wrong variable type!\n")
                    exit(53)

                convert = int(instructions[pc].args[1].value)
                try:
                    result = chr(convert)
                except ValueError:
                    sys.stderr.write("Character out of range!\n")
                    exit(58)

                variables_dict[instructions[pc].args[0].value] = ['string', result]
                write_value(pc, instructions, 2, temporary, local, local_stack, result, 'string')

            pc = pc + 1

        #converts character to integer
        elif(in_name == 'STRI2INT'):
            check_variables(pc, instructions, 3, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)
            if(instructions[pc].args[1].arg_type == 'var' and instructions[pc].args[2].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                if(type_v1 == -1 or type_v2 == -1):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)
                
                if(type_v1 != 'string' or type_v2 != 'int'):
                    sys.stderr.write("Wrong variable type!\n")
                    exit(53)
                    
                stri = value1
                ind = int(value2)
                err = len(stri)
                if(ind+1 > err or ind < 0):
                    sys.stderr.write("Index out of range!\n")
                    exit(58)
                result = ord(stri[ind])
                variables_dict[instructions[pc].args[0].value] = ['int', result]
                write_value(pc, instructions, 3, temporary, local, local_stack, result, 'int')

            elif(instructions[pc].args[1].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                if(type_v1 == -1):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)
                
                if(type_v1 != 'string' or instructions[pc].args[2].arg_type != 'int'):
                    sys.stderr.write("Wrong variable type!\n")
                    exit(53)

                stri = value1
                ind = int(instructions[pc].args[2].value)
                err = len(stri)
                if(ind+1 > err or ind < 0):
                    sys.stderr.write("Index out of range!\n")
                    exit(58)
                result = ord(stri[ind])
                variables_dict[instructions[pc].args[0].value] = ['int', result]
                write_value(pc, instructions, 3, temporary, local, local_stack, result, 'int')

            elif(instructions[pc].args[2].arg_type == 'var'):
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                if(type_v2 == -1):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)
                
                if(type_v2 != 'int' or instructions[pc].args[1].arg_type != 'string'):
                    sys.stderr.write("Wrong variable type!\n")
                    exit(53)

                stri = instructions[pc].args[1].value
                ind = int(value2)
                err = len(stri)
                if(ind+1 > err or ind < 0):
                    sys.stderr.write("Index out of range!\n")
                    exit(58)
                result = ord(stri[ind])
                variables_dict[instructions[pc].args[0].value] = ['int', result]
                write_value(pc, instructions, 3, temporary, local, local_stack, result, 'int')

            else:
                if(instructions[pc].args[2].arg_type  != 'int' or instructions[pc].args[1].arg_type != 'string'):
                    sys.stderr.write("Wrong variable type!\n")
                    exit(53)

                stri = instructions[pc].args[1].value
                ind = int(instructions[pc].args[2].value)
                err = len(stri)
                if(ind+1 > err or ind < 0):
                    sys.stderr.write("Index out of range!\n")
                    exit(58)
                result = ord(stri[ind])
                variables_dict[instructions[pc].args[0].value] = ['int', result]
                write_value(pc, instructions, 3, temporary, local, local_stack, result, 'int')

            pc = pc + 1
            
        # reads input
        elif(in_name == 'READ'):
            check_variables(pc, instructions, 1, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)
            try:
                if(is_input == False):
                    inp = input()
                else:
                    
                    inp = f.readline().rstrip('\n')
                    
            except:
                variables_dict[instructions[pc].args[0].value] = ['nil', 'nil']
                write_value(pc, instructions, 3, temporary, local, local_stack, 'nil', 'nil')
                pc = pc + 1
                continue
            
            if(instructions[pc].args[1].value == 'int' ):
                try:
                    inp = int(inp)
                except:
                    variables_dict[instructions[pc].args[0].value] = ['nil', 'nil']
                    write_value(pc, instructions, 3, temporary, local, local_stack, 'nil', 'nil')
                    pc = pc + 1
                    continue
                variables_dict[instructions[pc].args[0].value] = [instructions[pc].args[1].value, inp]
                write_value(pc, instructions, 3, temporary, local, local_stack, inp, instructions[pc].args[1].value)

            elif(instructions[pc].args[1].value == 'string'):
                variables_dict[instructions[pc].args[0].value] = [instructions[pc].args[1].value, inp]
                write_value(pc, instructions, 3, temporary, local, local_stack, inp, instructions[pc].args[1].value)

            elif(instructions[pc].args[1].value == 'bool'):
                inp = inp.lower()
                if(inp == 'true'):
                    variables_dict[instructions[pc].args[0].value] = [instructions[pc].args[1].value, 'true']
                    write_value(pc, instructions, 3, temporary, local, local_stack, 'true', instructions[pc].args[1].value)
                else:
                    variables_dict[instructions[pc].args[0].value] = [instructions[pc].args[1].value, 'false']
                    write_value(pc, instructions, 3, temporary, local, local_stack, 'false', instructions[pc].args[1].value)
            
            pc = pc + 1

        # writes to stdout
        elif(in_name == 'WRITE'):
            if(instructions[pc].args[0].arg_type != "var" and instructions[pc].args[0].arg_type != "nil"):
                if(instructions[pc].args[0].arg_type == "string"):
                    if(instructions[pc].args[0].value is None):
                            instructions[pc].args[0].value = ""
                    instructions[pc].args[0].value = str_replace(instructions[pc].args[0].value)
                print(instructions[pc].args[0].value, end='')
                pc = pc + 1

            elif(instructions[pc].args[0].arg_type == "nil"):
                print('', end='')
                pc = pc + 1

            else:
                check_variables(pc, instructions, 1, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)
                value, type_v = get_value(pc,instructions, 1, variables_dict, temporary, local, local_stack)
                if(type_v == -1):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)

                if(value is None):
                    print("", end='')
                if(type_v == "nil"):
                    print('', end='')
                else:
                    if(type_v == "string"):
                        if(value is None):
                            value = ""
                        value = str_replace(value)
                    print(value, end='')
                pc = pc + 1

        # concats two strings
        elif(in_name == 'CONCAT'):
            check_variables(pc, instructions, 3, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)
            if(instructions[pc].args[1].arg_type == 'var' and instructions[pc].args[2].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                if((type_v1 == -1) or (type_v2 == -1)):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)

                if(type_v1 != 'string' or type_v2 != 'string'):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                if(value1 is None):
                    value1 = ''
                if(value2 is None):
                    value2 = ''

                conc = value1+value2
                variables_dict[instructions[pc].args[0].value] = ['string', conc]
                write_value(pc, instructions, 3, temporary, local, local_stack, conc, 'string')
                
            elif(instructions[pc].args[1].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                if((type_v1 == -1)):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)

                if(type_v1 != 'string' or instructions[pc].args[2].arg_type != 'string'):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                if(value1 is None):
                    value1 = ''
                if(instructions[pc].args[2].value is None):
                    instructions[pc].args[2].value = ''

                conc = value1 + instructions[pc].args[2].value
                variables_dict[instructions[pc].args[0].value] = ['string', conc]
                write_value(pc, instructions, 3, temporary, local, local_stack, conc, 'string')                

            elif(instructions[pc].args[2].arg_type == 'var'):
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                if((type_v2 == -1)):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)
                

                if(type_v2 != 'string' or instructions[pc].args[1].arg_type != 'string'):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                
                if(value2 is None):
                    value2 = ''
                if(instructions[pc].args[1].value is None):
                    instructions[pc].args[1].value = ''

                conc = instructions[pc].args[1].value+value2
                variables_dict[instructions[pc].args[0].value] = ['string', conc]
                write_value(pc, instructions, 3, temporary, local, local_stack, conc, 'string')
                
            else:
                if(instructions[pc].args[2].arg_type != 'string' or instructions[pc].args[1].arg_type != 'string'):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)

                if(instructions[pc].args[1].value is None):
                    instructions[pc].args[1].value = ''
                if(instructions[pc].args[2].value is None):
                    instructions[pc].args[2].value = ''

                conc = instructions[pc].args[1].value + instructions[pc].args[2].value
                variables_dict[instructions[pc].args[0].value] = ['string', conc]
                write_value(pc, instructions, 3, temporary, local, local_stack, conc, 'string')
                
            pc = pc + 1
            
        # gets a length of a string
        elif(in_name == 'STRLEN'):
            check_variables(pc, instructions, 2, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)
            if(instructions[pc].args[1].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 2, variables_dict, temporary, local, local_stack)
                if(type_v1 == -1):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)
                
                if(type_v1 != 'string'):
                    sys.stderr.write("Wrong variable type!\n")
                    exit(53)
                if(value1 is None):
                    value2 = ''

                stri = value1
                length = len(stri)
                variables_dict[instructions[pc].args[0].value] = ['int', length]
                write_value(pc, instructions, 3, temporary, local, local_stack, length, 'int')

            else:
                if(instructions[pc].args[1].arg_type != 'string'):
                    sys.stderr.write("Wrong variable type!\n")
                    exit(53)
                if(instructions[pc].args[1].value is None):
                    instructions[pc].args[1].value = ''

                stri = instructions[pc].args[1].value
                length = len(stri)
                variables_dict[instructions[pc].args[0].value] = ['int', length]
                write_value(pc, instructions, 3, temporary, local, local_stack, length, 'int')

            pc = pc + 1

        #TESTOVAT !!!!!!!!!!!
        #DODELAT POPS

        # gets a character from a string
        elif(in_name == 'GETCHAR'):
            check_variables(pc, instructions, 3, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)
            
            if(instructions[pc].args[1].arg_type == 'var' and instructions[pc].args[2].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                if((type_v1 == -1) or (type_v2 == -1)):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)

                if(type_v1 != 'string' or type_v2 != 'int'):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                if(value1 is None):
                    value1 = ''

                stri = value1
                ind = int(value2)
                err = len(stri)
                if(ind+1 > err or ind < 0):
                    sys.stderr.write("Index out of range!\n")
                    exit(58)
                char = stri[ind]
                variables_dict[instructions[pc].args[0].value] = ['string', char]
                write_value(pc, instructions, 3, temporary, local, local_stack, char, 'string')
                
            elif(instructions[pc].args[1].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                if((type_v1 == -1)):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)

                if(type_v1 != 'string' or instructions[pc].args[2].arg_type != 'int'):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)

                if(value1 is None):
                    value1 = ''
                stri = value1
                ind = int(instructions[pc].args[2].value)
                err = len(stri)
                if(ind+1 > err or ind < 0):
                    sys.stderr.write("Index out of range!\n")
                    exit(58)
                char = stri[ind]
                variables_dict[instructions[pc].args[0].value] = ['string', char]
                write_value(pc, instructions, 3, temporary, local, local_stack, char, 'string')

            elif(instructions[pc].args[2].arg_type == 'var'):
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)

                if((type_v2 == -1)):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)

                if(type_v2 != 'int' or instructions[pc].args[1].arg_type != 'string'):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                if(instructions[pc].args[1].value is None):
                    instructions[pc].args[1].value = ''
                
                stri = instructions[pc].args[1].value
                ind = int(value2)
                err = len(stri)
                if(ind+1 > err or ind < 0):
                    sys.stderr.write("Index out of range!\n")
                    exit(58)
                char = stri[ind]
                variables_dict[instructions[pc].args[0].value] = ['string', char]
                write_value(pc, instructions, 3, temporary, local, local_stack, char, 'string')

            else:
                if(instructions[pc].args[2].arg_type != 'int' or instructions[pc].args[1].arg_type != 'string'):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                if(instructions[pc].args[1].value is None):
                    instructions[pc].args[1].value = ''

                stri = instructions[pc].args[1].value
                ind = int(instructions[pc].args[2].value)
                err = len(stri)
                if(ind+1 > err or ind < 0):
                    sys.stderr.write("Index out of range!\n")
                    exit(58)
                char = stri[ind]
                variables_dict[instructions[pc].args[0].value] = ['string', char]
                write_value(pc, instructions, 3, temporary, local, local_stack, char, 'string')
                
            pc = pc + 1
            
        #sets a character in a string
        elif(in_name == 'SETCHAR'):
            check_variables(pc, instructions, 3, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)

            if(instructions[pc].args[1].arg_type == 'var' and instructions[pc].args[2].arg_type == 'var'):
                value0, type_v0 = get_value(pc, instructions, 1, variables_dict, temporary, local, local_stack)
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                if((type_v0 == -1) or (type_v1 == -1) or (type_v2 == -1)):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)

                if(type_v0 != 'string' or type_v1 != 'int' or type_v2 != 'string'):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)

                if(value0 is None):
                    value0 = ''
                
                stri = value2
                if(stri == None):
                    sys.stderr.write("String is empty!\n")
                    exit(58)
                
                if(type_v2 == "string"):
                    value2 = str_replace(value2)
                stri = value2

                stri1 =value0
                ind = int(value1)
                err = len(stri)
                if(ind+1 > err or ind < 0):
                    sys.stderr.write("Index out of range!\n")
                    exit(58)
                stri1 = stri1.replace(stri1[ind], stri[0])
                variables_dict[instructions[pc].args[0].value] = ['string', stri1]
                write_value(pc, instructions, 3, temporary, local, local_stack, stri1, 'string')
                
            elif(instructions[pc].args[1].arg_type == 'var'):
                value0, type_v0 = get_value(pc, instructions, 1, variables_dict, temporary, local, local_stack)
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                if((type_v0 == -1) or (type_v1 == -1)):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)

                if(type_v0 != 'string' or type_v1 != 'int' or instructions[pc].args[2].arg_type != 'string'):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                if(value0 is None):
                    value0 = ''
                
                stri = instructions[pc].args[2].value
                if(stri == None):
                    sys.stderr.write("String is empty!\n")
                    exit(58)

                if(instructions[pc].args[2].arg_type == "string"):
                    instructions[pc].args[2].value = str_replace(instructions[pc].args[2].value)
                stri = instructions[pc].args[2].value

                stri1 = value0
                ind = int(value1)
                err = len(stri)
                if(ind+1 > err or ind < 0):
                    sys.stderr.write("Index out of range!\n")
                    exit(58)
                stri1 = stri1.replace(stri1[ind], stri[0])
                variables_dict[instructions[pc].args[0].value] = ['string', stri1]
                write_value(pc, instructions, 3, temporary, local, local_stack, stri1, 'string')

            elif(instructions[pc].args[2].arg_type == 'var'):
                value0, type_v0 = get_value(pc, instructions, 1, variables_dict, temporary, local, local_stack)
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                if((type_v2 == -1) or (type_v0 == -1)):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)
                

                if(type_v0 != 'string' or type_v2 != 'string' or instructions[pc].args[1].arg_type != 'int'):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                if(value0 is None):
                    value0 = ''

                stri = value2
                if(stri == None):
                    sys.stderr.write("String is empty!\n")
                    exit(58)
                if(type_v2 == "string"):
                    value2 = str_replace(value2)
                stri = value2

                stri1 = value0
                ind = int(instructions[pc].args[1].value)
                err = len(stri)
                if(ind+1 > err or ind < 0):
                    sys.stderr.write("Index out of range!\n")
                    exit(58)
                stri1 = stri1.replace(stri1[ind], stri[0])
                variables_dict[instructions[pc].args[0].value] = ['string', stri1]
                write_value(pc, instructions, 3, temporary, local, local_stack, stri1, 'string')

            else:
                value0, type_v0 = get_value(pc, instructions, 1, variables_dict, temporary, local, local_stack)
                if((type_v0 == -1)):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)
                if(type_v0 != 'string' or instructions[pc].args[2].arg_type != 'string' or instructions[pc].args[1].arg_type != 'int'):
                    sys.stderr.write("Different types of variables!\n")
                    exit(53)
                if(value0 is None):
                    value0 = ''

                stri = instructions[pc].args[2].value
                if(stri == None):
                    sys.stderr.write("String is empty!\n")
                    exit(58)
                
                if(instructions[pc].args[2].arg_type == "string"):
                    instructions[pc].args[2].value = str_replace(instructions[pc].args[2].value)
                stri = instructions[pc].args[2].value
                
                stri1 = value0
                ind = int(instructions[pc].args[1].value)
                err = len(stri1)
                if(ind+1 > err or ind < 0):
                    sys.stderr.write("Index out of range!\n")
                    exit(58)
                stri1 = stri1.replace(stri1[ind], stri[0])
                variables_dict[instructions[pc].args[0].value] = ['string', stri1]
                write_value(pc, instructions, 3, temporary, local, local_stack, stri1, 'string')
                
            pc = pc + 1
            
        # gets a type of given variable
        elif(in_name == 'TYPE'):
            check_variables(pc, instructions, 2, label_dict, variables_dict, temporary, local, temp_exists, local_stack, local_exists, temp_in_use)
            if(instructions[pc].args[1].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 2, variables_dict, temporary, local, local_stack)
                if(type_v1 == -1):
                    variables_dict[instructions[pc].args[0].value] = ['string', '']
                    write_value(pc, instructions, 3, temporary, local, local_stack, '', 'string')
                else:
                    variables_dict[instructions[pc].args[0].value] = ['string', type_v1]
                    write_value(pc, instructions, 3, temporary, local, local_stack, type_v1, 'string')
            else:
                variables_dict[instructions[pc].args[0].value] = ['string', instructions[pc].args[1].arg_type]
                write_value(pc, instructions, 3, temporary, local, local_stack, instructions[pc].args[1].arg_type, 'string')

            pc = pc + 1

        #label instruction, skipped because labels are already accounted for
        elif(in_name == 'LABEL'):
            pc = pc + 1
            continue

        # jumps to a given label
        elif(in_name == 'JUMP'):
            check_variables(pc, instructions, 1, label_dict, variables_dict, temporary, local, temp_exists, local_stack, temp_exists, temp_in_use)
            label_jump = label_dict[instructions[pc].args[0].value]
            pc = label_jump[0] + 1
        
        #jumps to a given label if oprands are equal
        elif(in_name == 'JUMPIFEQ'):
            check_variables(pc, instructions, 3, label_dict, variables_dict, temporary, local, temp_exists, local_stack, temp_exists, temp_in_use)
            if(instructions[pc].args[1].arg_type == 'var' and instructions[pc].args[2].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                if(type_v1 == -1 or type_v2 == -1):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)

                if(type_v1 != type_v2 and (type_v1 != 'nil' and type_v2 != 'nil')):
                    sys.stderr.write("Wrong variable type!\n")
                    exit(53)
                
                if(value1 is None):
                    value1 = ''
                if(value2 is None):
                    value2 = ''
                if(type_v1 == "string"):
                    value1 = str_replace(value1)
                if(type_v2 == "string"):
                    value2 = str_replace(value2)

                if(type_v1 == "int" and value1 != 'nil'):
                    value1 = int(value1)
                if(type_v2 == "int" and value2 != 'nil'):
                    value2 = int(value2)

                if((value1 == value2)):
                    label_jump = label_dict[instructions[pc].args[0].value]
                    pc = label_jump[0] + 1
                else:
                    pc = pc + 1
                
            elif(instructions[pc].args[1].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                if(type_v1 == -1):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)

                if(type_v1 != instructions[pc].args[2].arg_type and (type_v0 != 'nil' and instructions[pc].args[2].arg_type != 'nil')):
                    sys.stderr.write("Wrong variable type!\n")
                    exit(53)

                if(value1 is None):
                    value1 = ''
                if(instructions[pc].args[2].value is None):
                    instructions[pc].args[2].value = ''

                if(type_v1 == "string"):
                    value1 = str_replace(value1)
                if(instructions[pc].args[2].arg_type == "string"):
                    instructions[pc].args[2].value = str_replace(instructions[pc].args[2].value)
                
                if(type_v1 == "int" and value1 != 'nil'):
                    value1 = int(value1)
                if(instructions[pc].args[2].arg_type == "int" and instructions[pc].args[2].value != 'nil'):
                    instructions[pc].args[2].value = int(instructions[pc].args[2].value)

                if((value1 == instructions[pc].args[2].value)):
                    label_jump = label_dict[instructions[pc].args[0].value]
                    pc = label_jump[0] + 1
                else:
                    pc = pc + 1

            elif(instructions[pc].args[2].arg_type == 'var'):
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                if(type_v2 == -1):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)

                if(type_v2 != instructions[pc].args[1].arg_type and (type_v2 != 'nil' and instructions[pc].args[1].arg_type != 'nil')):
                    sys.stderr.write("Wrong variable type!\n")
                    exit(53)

                if(value2 is None):
                    value2 = ''
                if(instructions[pc].args[1].value is None):
                    instructions[pc].args[1].value = ''
                
                if(type_v2 == "string"):
                    value2 = str_replace(value2)
                if(instructions[pc].args[1].arg_type == "string"):
                    instructions[pc].args[1].value = str_replace(instructions[pc].args[1].value)

                if(instructions[pc].args[1].arg_type == "int" and instructions[pc].args[1].value != 'nil'):
                    instructions[pc].args[1].value = int(instructions[pc].args[1].value)
                if(type_v2 == "int" and value2 != 'nil'):
                    value2 = int(value2)

                if((value2 == instructions[pc].args[1].value)):
                    label_jump = label_dict[instructions[pc].args[0].value]
                    pc = label_jump[0] + 1
                else:
                    pc = pc + 1
            else:
                if(instructions[pc].args[1].arg_type  != instructions[pc].args[2].arg_type and (instructions[pc].args[1].arg_type != 'nil' and instructions[pc].args[2].arg_type != 'nil')):
                    sys.stderr.write("Wrong variable type!\n")
                    exit(53)
                if(instructions[pc].args[1].value is None):
                    instructions[pc].args[1].value = ''
                if(instructions[pc].args[2].value is None):
                    instructions[pc].args[2].value = ''
                
                if(instructions[pc].args[2].arg_type == "string"):
                    instructions[pc].args[2].value = str_replace(instructions[pc].args[2].value)
                if(instructions[pc].args[1].arg_type == "string"):
                    instructions[pc].args[1].value = str_replace(instructions[pc].args[1].value)

                if(instructions[pc].args[1].arg_type == "int"):
                    instructions[pc].args[1].value = int(instructions[pc].args[1].value)
                if(instructions[pc].args[2].arg_type == "int"):
                    instructions[pc].args[2].value = int(instructions[pc].args[2].value)

                if((instructions[pc].args[1].value == instructions[pc].args[2].value)):
                    label_jump = label_dict[instructions[pc].args[0].value]
                    pc = label_jump[0] + 1
                else:
                    pc = pc + 1

        # jumps to a given label if operands are unequal
        elif(in_name == 'JUMPIFNEQ'):
            check_variables(pc, instructions, 3, label_dict, variables_dict, temporary, local, temp_exists, local_stack, temp_exists, temp_in_use)

            if(instructions[pc].args[1].arg_type == 'var' and instructions[pc].args[2].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                if(type_v1 == -1 or type_v2 == -1):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)

                if(type_v1 != type_v2 and (type_v1 != 'nil' and type_v2 != 'nil')):
                    sys.stderr.write("Wrong variable type!\n")
                    exit(53)
                
                if(value1 is None):
                    value1 = ''
                if(value2 is None):
                    value2 = ''
                if(type_v1 == "string"):
                    value1 = str_replace(value1)
                if(type_v2 == "string"):
                    value2 = str_replace(value2)

                if(type_v1 == "int" and value1 != 'nil'):
                    value1 = int(value1)
                if(type_v2 == "int" and value2 != 'nil'):
                    value2 = int(value2)

                if((value1 != value2)):
                    label_jump = label_dict[instructions[pc].args[0].value]
                    pc = label_jump[0] + 1
                else:
                    pc = pc + 1
                
            elif(instructions[pc].args[1].arg_type == 'var'):
                value1, type_v1 = get_value(pc, instructions, 3, variables_dict, temporary, local, local_stack)
                if(type_v1 == -1):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)

                if(type_v1 != instructions[pc].args[2].arg_type and (type_v1 != 'nil' and instructions[pc].args[2].arg_type != 'nil')):
                    sys.stderr.write("Wrong variable type!\n")
                    exit(53)

                if(value1 is None):
                    value1 = ''
                if(instructions[pc].args[2].value is None):
                    instructions[pc].args[2].value = ''
                
                if(type_v1 == "string"):
                    value1 = str_replace(value1)
                if(instructions[pc].args[2].arg_type == "string"):
                    instructions[pc].args[2].value = str_replace(instructions[pc].args[2].value)
                
                if(type_v1 == "int" and value1 != 'nil'):
                    value1 = int(value1)
                if(instructions[pc].args[2].arg_type == "int" and instructions[pc].args[2].value != 'nil'):
                    instructions[pc].args[2].value = int(instructions[pc].args[2].value)

                if((value1 != instructions[pc].args[2].value)):
                    label_jump = label_dict[instructions[pc].args[0].value]
                    pc = label_jump[0] + 1
                else:
                    pc = pc + 1

            elif(instructions[pc].args[2].arg_type == 'var'):
                value2, type_v2 = get_value(pc, instructions, 4, variables_dict, temporary, local, local_stack)
                if(type_v2 == -1):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)
                
                if(type_v2 != instructions[pc].args[1].arg_type and (type_v2 != 'nil' and instructions[pc].args[1].arg_type != 'nil')):
                    sys.stderr.write("Wrong variable type!\n")
                    exit(53)

                if(value2 is None):
                    value2 = ''
                if(instructions[pc].args[1].value is None):
                    instructions[pc].args[1].value = ''

                if(type_v2 == "string"):
                    value2 = str_replace(value2)
                if(instructions[pc].args[1].arg_type == "string"):
                    instructions[pc].args[1].value = str_replace(instructions[pc].args[1].value)

                if(instructions[pc].args[1].arg_type == "int" and instructions[pc].args[1].value != 'nil'):
                    instructions[pc].args[1].value = int(instructions[pc].args[1].value)
                if(type_v2 == "int" and value2 != 'nil'):
                    value2 = int(value2)

                if((value2 != instructions[pc].args[1].value)):
                    label_jump = label_dict[instructions[pc].args[0].value]
                    pc = label_jump[0] + 1
                else:
                    pc = pc + 1
            else:
                if(instructions[pc].args[1].arg_type  != instructions[pc].args[2].arg_type and (instructions[pc].args[1].arg_type != 'nil' and instructions[pc].args[2].arg_type != 'nil')):
                    sys.stderr.write("Wrong variable type!\n")
                    exit(53)
                if(instructions[pc].args[1].value is None):
                    instructions[pc].args[1].value = ''
                if(instructions[pc].args[2].value is None):
                    instructions[pc].args[2].value = ''

                if(instructions[pc].args[2].arg_type == "string"):
                    instructions[pc].args[2].value = str_replace(instructions[pc].args[2].value)
                if(instructions[pc].args[1].arg_type == "string"):
                    instructions[pc].args[1].value = str_replace(instructions[pc].args[1].value)

                if(instructions[pc].args[1].arg_type == "int"):
                    instructions[pc].args[1].value = int(instructions[pc].args[1].value)
                if(instructions[pc].args[2].arg_type == "int"):
                    instructions[pc].args[2].value = int(instructions[pc].args[2].value)

                if((instructions[pc].args[1].value != instructions[pc].args[2].value)):
                    label_jump = label_dict[instructions[pc].args[0].value]
                    pc = label_jump[0] + 1
                else:
                    pc = pc + 1

        # exits a program with according code
        elif(in_name == 'EXIT'):
            check_variables(pc, instructions, 1, label_dict, variables_dict, temporary, local, temp_exists, local_stack, temp_exists, temp_in_use)
            if(instructions[pc].args[0].arg_type == 'var'):
                value0, type_v0 = get_value(pc, instructions, 1, variables_dict, temporary, local, local_stack)
                if(type_v0 == -1):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)

                if(type_v0 != 'int'):
                    sys.stderr.write("Wrong variable type!\n")
                    exit(53)

                if(int(value0) >= 0 and int(value0) <= 49):
                    exit(int(value0))
                else:
                    sys.stderr.write("Wrong value of argument!\n")
                    exit(57)

            elif(instructions[pc].args[0].arg_type == 'int'):

                if(int(instructions[pc].args[0].value) >= 0 and int(instructions[pc].args[0].value) <= 49):
                    exit(int(instructions[pc].args[0].value))
                else:
                    sys.stderr.write("Wrong value of argument!\n")
                    exit(57)

            else:
                sys.stderr.write("Wrong type of argument!\n")
                exit(53)
        
        elif(in_name == 'DPRINT'):
            if(instructions[pc].args[0].arg_type == 'var'):
                check_variables(pc, instructions, 1, label_dict, variables_dict, temporary, local, temp_exists, local_stack, temp_exists, temp_in_use)

                if(variables_dict[instructions[pc].args[0].value][0] == -1):
                    sys.stderr.write("Uninitialized variable!\n")
                    exit(56)

                sys.stderr.write(variables_dict[instructions[pc].args[0].value][1])
            else:
                sys.stderr.write(instructions[pc].args[0].value)
            pc = pc + 1

        elif(in_name == 'BREAK'):
            pc = pc + 1
        else:
            sys.stderr.write("Unknown code!\n")
            exit(32)

    if(is_input == True):
        f.close()

#-------------------------------------------------------------
#                           MAIN
#-------------------------------------------------------------

argv = sys.argv[1:]
optlist, args = getopt.getopt(argv, "", ["input=", "source=", "help"])
is_file = False
is_input = False
file_arg = " "
source_arg = " "

#options parsing
for c in optlist:
    if(c[0] == "--source"):
        is_file = True
        file_arg = c[1]
    elif(c[0] == "--input"):
        is_input = True
        source_arg = c[1]
    elif(c[0] == "--help"):
        print("interpret.py [--source=file] [--input=source] [--help]")
        exit(0)

if((is_file==False) and (is_input==False)):
    sys.stderr.write("Both files are missing!\n")
    exit(10)

#XML tree parse
if(is_file == True):
    try:
        tree = tr.parse(file_arg)
    except:
        sys.stderr.write("Wrong XML structure!\n")
        exit(31)
else:
    try:
        tree = tr.parse(sys.stdin)
    except:
        sys.stderr.write("Wrong XML structure!\n")
        exit(31)

#gets a root and checks if structure is correct
root = tree.getroot()
if(root.tag != "program"):
    sys.stderr.write("Wrong header!\n")
    exit(32)

for key in root.attrib.keys():
    if(key == "name"):
        continue
    elif(key == "description"):
        continue
    elif(key == "language"):
        continue
    else:
        sys.stderr.write("Wrong header!\n")
        exit(32)
    
i = 0
for value in root.attrib.values():
    value = value.upper()
    if(value != "IPPCODE22" and i == 0):
        sys.stderr.write("Wrong header!\n")
        exit(32)
    i = i + 1

for child in root:
    child_args = list(child.attrib.keys())
    if(not("order" in child_args) or not("opcode" in child_args)):
        sys.stderr.write("Wrong child keys!\n")
        exit(32)

try:
    root[:] = sorted(root, key=lambda child: int(child.get('order')))
except:
    sys.stderr.write("Error order!\n")
    exit(32)

b4_order = 0
normal_order = 0

#whole tree structure check
for child in root:

    normal_order = int(b4_order) + 1
    if((int(child.get('order')) <= int(b4_order)) or (int(child.get('order')) < normal_order) or (int(child.get('order')) <= 0)):
        sys.stderr.write("Wrong order!\n")
        exit(32)
    b4_order = child.get('order')

    if(child.tag != "instruction"):
        sys.stderr.write("Wrong child tag!\n")
        exit(32)

    for sub_child in child:

        if(not(re.match(r"arg[123]", sub_child.tag))):
            sys.stderr.write("Wrong subchild structure!\n")
            exit(32)

        for key in sub_child.attrib.keys():

            if(key != "type"):
                sys.stderr.write("Wrong subchild structure!\n")
                exit(32) 

#Parses XML tree to instructions class
instructions = []
for element in root:
    ins_name = list(element.attrib.values())
    ins = instruction(ins_name[1])
    for sub_elem in element:
        for typ in sub_elem.attrib.values():
            arguments = arg(typ , sub_elem.text)
            ins.arg_add(arguments.arg_type, arguments.value)
    instructions.append(ins)

#interpretation
variables_dict = {}
num_of_ins = len(instructions)
my_interpret(instructions, variables_dict, num_of_ins)

exit(0)