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
* **loglookup( type, field, record, event, instance )**<br>
  lookup the first or last entry in the project log, filtered by field, record, event and instance,
  and return a metadata value
  * Valid lookup types are:
    * **first-user** (username from first log entry)
    * **last-user** (username from latest log entry)
    * **first-user-fullname** (user's full name from first log entry)
    * **last-user-fullname** (user's full name from latest log entry)
    * **first-user-email** (user's primary email from first log entry)
    * **last-user-email** (user's primary email from latest log entry)
    * **first-ip** (user's IP address from first log entry)
    * **last-ip** (user's IP address from latest log entry)
    * **first-date** (date of first log entry - Y-M-D format)
    * **last-date** (date of latest log entry - Y-M-D format)
    * **first-date-dmy** (date of first log entry - D-M-Y format)
    * **last-date-dmy** (date of latest log entry - D-M-Y format)
    * **first-date-mdy** (date of first log entry - M-D-Y format)
    * **last-date-mdy** (date of latest log entry - M-D-Y format)
    * **first-datetime** (date/time of first log entry - Y-M-D format)
    * **last-datetime** (date/time of latest log entry - Y-M-D format)
    * **first-datetime-dmy** (date/time of first log entry - D-M-Y format)
    * **last-datetime-dmy** (date/time of latest log entry - D-M-Y format)
    * **first-datetime-mdy** (date/time of first log entry - M-D-Y format)
    * **last-datetime-mdy** (date/time of latest log entry - M-D-Y format)
    * **first-datetime-seconds** (date/time w/seconds of first log entry - Y-M-D format)
    * **last-datetime-seconds** (date/time w/seconds of latest log entry - Y-M-D format)
    * **first-datetime-seconds-dmy** (date/time w/seconds of first log entry - D-M-Y format)
    * **last-datetime-seconds-dmy** (date/time w/seconds of latest log entry - D-M-Y format)
    * **first-datetime-seconds-mdy** (date/time w/seconds of first log entry - M-D-Y format)
    * **last-datetime-seconds-mdy** (date/time w/seconds of latest log entry - M-D-Y format)
  * Example: get the date/time that the *initials* field was last updated
    * loglookup( 'last-datetime', 'initials', [record-name] )
* **makedate( format, year, month, day )**<br>
  returns the date value for the supplied year, month and day components, according to the specified
  format ('dmy', 'mdy', or 'ymd')
* **randomnumber()**<br>
  returns a cryptographically secure random number between 0 and 1
  * Note that this function will return a different value each time the calculation is run. To
    preserve a generated random number, consider pairing this function with the *ifnull* function,
    so that the calculated field's current value (once set) is preferred over a new value.
    <br>e.g. `ifnull( [calc_field_name], randomnumber() )`
* **sysvar( varname )**<br>
 returns the value for the specified system variable as defined in the module system settings

Note that where the arguments to *ifenum* and *ifnull* are themselves functions, they will all be
evaluated prior to the *ifenum* or *ifnull* logic execution (eager evaluation), even if those
arguments are not needed. Using REDCap's built-in *if* function instead will avoid this issue, at
the expense of more complicated calculation logic.


## Project-level configuration options

### Automatically update calculated values
If this setting is enabled, the data quality rule for *Incorrect values for calculated fields* will
be automatically run to fix calculated values on page load if at least 10 minutes has passed since
it was last run.

Note that if there is a lot of data in the project and updating calculated values takes a long time,
this feature may apply to only a subset of data at a time. In this case, several runs will need to
complete in order for all calculated values to be fixed.

***The following settings are only available to administrators.***

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
Criteria to filter the records. Any `?` characters in the filter logic act as placeholders and will
be replaced by the values specified as parameters in the *datalookup* function.

The number of placeholders in the filter logic and the number of placeholder values supplied in the
*datalookup* function should be equal. If there are more placeholders in the logic, the remaining
placeholders will be replaced by the empty string. If there are more placeholder values, the surplus
values will be ignored.

### Lookup field
The value of this field (from the returned record) will be returned by the *datalookup* function.

### Return label instead of raw value
If checked, the datalookup function will return the label instead of the value when looking up a
multiple choice field.

### Type of lookup
If multiple records are returned by the filter logic, this option specifies how the result is to be
returned.

### List separator
If a list of items is to be returned, specify the separator character/string here.


## System-level configuration options

### Enable system variables
This setting enables the *sysvar* function and provides the options to set the variable names and
values. If system variables are enabled, at least one must be defined.

### Variable name
The name of the system variable. This is used as the parameter to the *sysvar* function.

### Variable value
The value of the system variable. This is the value returned by the *sysvar* function.
