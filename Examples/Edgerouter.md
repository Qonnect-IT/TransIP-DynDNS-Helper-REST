For Ubiquiti Edgerouter (Lite) a configuration can be defined in the task-scheduler, either via GUI or via CLI.

The finished configuration should look something like this:

```
system {
    task-scheduler {
        task updatedyndns {
            executable {
                arguments "-k https://{{ SERVER }}/dyndns/update.php?token={{ TOKEN }}"
                path /usr/bin/curl
            }
            interval 1m
        }
    }
}
```

This can be achieved by the running the following commands:

```
admin@router~$ configure
admin@router# set system task-scheduler task updatedyndns executable path /usr/bin/curl
admin@router# set system task-scheduler task updatedyndns executable arguments "-k https://{{ SERVER }}/dyndns/update.php?token={{ TOKEN }}"
admin@router# set system task-scheduler task updatedyndns interval 1m
admin@router# save
admin@router# exit
```

Or via the GUI as shown below

![Egerouter Image](Edgerouter.png?raw=true "Edgerouter")
