# list-ansible-tested-platforms
Simple php script to scan a directory containing Ansible roles and lists for each role which platforms are tested by Molecule

## Running
To run simply run the following command with directory being the directory that contains the ansible roles or if left out 
```./latp.php <directory>```

## Example Output
| role                     | centos-7 | opensuse-leap-15.2 |
| ------------------------ |:--------:|:------------------:|
| common                   | x        | x                  |
| docker                   | x        | -                  |
