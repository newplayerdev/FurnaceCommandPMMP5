# FurnaceCommandPMMP4

**Installation**
Donwload Commando virion (https://poggit.pmmp.io/ci/CortexPE/Commando/~) and put it inside your `/virions` folder on your server

**Usage**

/furnace [all]

**Permissions**

`furnace.use` -> `/furnace`

`furnace.all` -> `/furnace all`

**Config**

`config.yml` file:

```yml
---
# command description
command_description: ""

# message when player doesn't have the permission to use the command
permission_message: "You don't have the permission to use this command !"

# messages
furnace_message: "Your item has been furnaced"
furnace_all_message: "All your items have been furnaced"

item_not_furnacable: "You cannot furnace this item"

# Cooldown in seconds
cooldown: 10

# Cooldown message
cooldown_message: "§cYou are currently in cooldown, please wait {cooldown} seconds"

...
```
