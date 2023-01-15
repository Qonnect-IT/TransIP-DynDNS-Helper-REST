For Mikrotik Routers a configuration can be defined in the Script section, either via GUI or via CLI (please figure this out yourself since the CLI is meh).

The finished configuration should look something like this:

Via the GUI, the following script should be issued as shown below

```
:log info ("Starting DynDNS Script")

#Defining Variables
:local token "{{ TOKEN }}"
:local url "https://{{ SERVER }}/update.php?token=$token"

#Running Fetch utility with variables.
:log info ("Updating DynDNS")
/tool fetch url=$url mode=http
:log info ("Stopping DynDNS Script")
```

![Mikrotik Image](Mikrotik.png?raw=true "Mikrotik")
