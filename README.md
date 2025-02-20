# CHECK24 Car Insurance Provider Integration

This project demonstrates integration with car insurance providers, showcasing clean architecture and test-driven development practices.

## Getting Started

### Running the Application

To run the application first it is required to run the following command in order to build infrastructure and install dependencies:
```bash
make build

make install 
```

Now you could get the shell with the following command: 
```bash
make shell
```

To run application command with a specific input file and provider, run the command in your container:
Note 1: Input file should be the address of json file in container
Note 2: The provider is the id of the insurance provider in our system like acme
```sh
$ php bin/console app:map-request -i ./input.json -p acme
```
## running tests

```bash
make test 
```
