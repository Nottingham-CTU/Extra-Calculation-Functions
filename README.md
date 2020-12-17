# Extra-Calculation-Functions
This REDCap module adds extra functions for use in calculated fields.

## Functions
* **concat( string1, string2, ... )**<br>
  concatenates an arbitrary number of string arguments
* **datalookup( name, param1, param2, ... )**<br>
  supply a lookup name followed by parameters to invoke a REDCap data lookup defined in the module
  project settings
* **ifnull( arg1, arg2, ... )**<br>
  returns the first argument supplied which is not null
* **randomnumber()**<br>
  returns a cryptographically secure random number between 0 and 1
  * Note that this function will return a different value each time the calculation is run. To
    preserve a generated random number, consider pairing this function with the *ifnull* function.
* **strenc( string )**<br>
  Converts a string value to a numeric encoding, which can be saved in a REDCap calculated field.
  Use the @STRENC action tag on the field to instruct the form/survey to decode and display the
  string value.
* **substr( string, start, length )**<br>
  Extracts a portion of a string. *Start* positions count from 0. Negative *start* and *length*
  values count backwards from the end of the string.
