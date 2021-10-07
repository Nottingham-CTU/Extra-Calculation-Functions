# Extra-Calculation-Functions
This REDCap module adds extra functions for use in calculated fields.

## Functions
* **datalookup( name, param1, param2, ... )**<br>
  supply a lookup name followed by parameters to invoke a REDCap data lookup defined in the module
  project settings
  * The behaviour of this function will depend on the defined custom data lookups in the module
    settings.
* **ifenum( comparator, default, value1, result1, value2, result2, ... )**<br>
  if-enumerated: returns the result corresponding to the first value which is equal to the
  comparator, or the default value if none of the values are equal
* **ifnull( arg1, arg2, ... )**<br>
  returns the first argument supplied which is not null
  * e.g. `ifnull( [field1], [field2] )` will return the value of `field1` unless it is empty, in
    which case it will return the value of `field2`.
* **randomnumber()**<br>
  returns a cryptographically secure random number between 0 and 1
  * Note that this function will return a different value each time the calculation is run. To
    preserve a generated random number, consider pairing this function with the *ifnull* function,
    so that the calculated field's current value (once set) is preferred over a new value.
    <br>e.g. `ifnull( [calc_field_name], randomnumber() )`

Note that where the arguments to *ifenum* and *ifnull* are themselves functions, they will all be
evaluated prior to the *ifenum* or *ifnull* logic execution (eager evaluation), even if those
arguments are not needed. Using REDCap's built-in *if* function instead will avoid this issue, at
the expense of more complicated calculation logic.


## Project-level configuration options
These settings are only available to administrators.

### Enable custom data lookup
This setting enables the *datalookup* function and provides the options to configure data lookup
settings. If custom data lookup is enabled, at least one custom data lookup must be defined.

### Lookup name
This is a unique name for the data lookup. The lookup name is supplied as the first parameter in the
*datalookup* function.

### Project in which to perform lookup
Specifies which REDCap project the lookup is applied to. If this is not set, the current project is
used.

### Record filter logic
Criteria to filter the records. The filter logic should be specific enough to return only a single
record. If multiple records are returned, there is the possibility of an erroneous or inconsistent
result from the lookup. Any `?` characters in the filter logic act as placeholders and will be
replaced by the values specified as parameters in the *datalookup* function.

The number of placeholders in the filter logic and the number of placeholder values supplied in the
*datalookup* function should be equal. If there are more placeholders in the logic, the remaining
placeholders will be replaced by the empty string. If there are more placeholder values, the surplus
values will be ignored.

### Lookup field
The value of this field (from the returned record) will be returned by the *datalookup* function.

### Return label instead of raw value
If checked, the datalookup function will return the label instead of the value when looking up a
multiple choice field.
