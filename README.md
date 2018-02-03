# EggWars

### This plugin is under development (progress: 90%)

_EggWars minigame for PocketMine_

[![Build Status](https://travis-ci.org/GamakCZ/EggWars.svg?branch=master)](https://travis-ci.org/GamakCZ/EggWars)

[![Discord](https://img.shields.io/discord/102860784329052160.svg)](https://discord.gg/uwBf2jS)

[![Poggit-CI](https://poggit.pmmp.io/ci.shield/GamakCZ/EggWars/EggWars)](https://poggit.pmmp.io/ci/GamakCZ/EggWars/EggWars)


### TODO:

- [x] Commands:
    - [x] Create command
    - [x] Set command
    - [x] Level command
    - [x] Other commands

- [x] Setup Mode:
    - [x] Level setup mode
    - [x] Arenas setup mode
    - [x] Advanced settings
    
- [x] Voting:
    - [x] World choosing
    - [x] Voting

- [ ] Shop:
    - [x] Simple shop without config - INDEV (needs test, fix)

- [x] Gameplay system:
    - [x] Join signs
    - [x] Generators
    - [x] Joining to arena
    - [x] Joining to teams
    - [x] Task
    - [x] Custom teams
    - [x] Working deaths / respawns
    - [x] Arena restart (need test)

- [ ] Bug fixing:
    - [ ] Shop bugs


#### News:

- Voting completed!

![voting](https://preview.ibb.co/fz7Grm/Minecraft_13_01_2018_13_31_21.png)


**--- not completed part ---**

### Commands:

#### Vote Command:

- permission: ew.cmd.vote
- usage: /vote <map: 1-3>
- description: Voting command

#### EggWars Command:

- permission: ew.cmd

- subcommands:
    - /ew help:
        - permission: ew.cmd.help
        - description: Displays all commands
    - /ew create:
        - permission: ew.cmd.create
        - description: Create arena
        - usage: /ew create <arena>
    - /ew set:
        - permission: ew.cmd.set
        - description: Set arena
        - usage: /ew set <arena>
    - /ew arenas:
        - permission: ew.cmd.arenas:
        - description: ew.cmd.arenas
    - /ew level:
        - permission: ew.cmd.level:
        - description: Manage levels
        - usage: /ew level <add|set|remove|list>
        - subcommands:
            - /ew level add:
                - permission: ew.cmd.level.add
                - description: Add level to EggWars
                - usage: /ew level add <level> <customName>
            - /ew level set:
                - permission: ew.cmd.level.set
                - description: Set EggWars level
                - usage: /ew level set <customLevelName>
            - /ew level remove:
                - permission: ew.cmd.level.remove
                - description: Remove EggWars level
                - usage: /ew level remove <customLevelName>
            - /ew level list:
                - permission: ew.cmd.level.list
                - description: Displays list of all levels
               
