<div align="center">
	<h1>Thermian</h1>
    <br/>
	<h3>A web application built by <a href="http://ingenium.uclm.es">Ingenium</a></h3>
	<a href="https://github.com/jhomswk/thermian/blob/master/.github/workflows/ci.yml">
		<img src="https://github.com/jhomswk/thermian/actions/workflows/ci.yml/badge.svg" alt="CI badge"/>
	</a>
</div>

<br />

## :rocket: Installation

<br />

Requirements:
- [Install docker](https://www.docker.com/get-started)

Clone the project
```bash
git clone https://github.com/jhomswk/thermian
```

Move into the project directory
```bash
cd thermian
```

Install the application
```bash
./bin/prod/install
```

Wake the environment up
```bash
./bin/prod/up
```

Visit
```bash
http://localhost
```

<br />

## :houses: Architecture

<br />

Wake the container up
```bash
./bin/arch/up
```

Visit
```bash
http://localhost:3000
```

<br />

## :detective: CI

<br />

Check code standards
```bash
./bin/ci/code-standards
```

Perform a static analysis
```bash
./bin/ci/static-analysis
```

Run the tests
```bash
./bin/ci/test
```

Run the CI pipeline
```bash
./bin/ci/run
```

<br />

## :joystick: Commands

<br />

### Production Environment

<br />


Install the application
```bash
./bin/prod/install
```

Start the application
```bash
./bin/prod/up
```

Stop the application
```bash
./bin/prod/down
```

Uninstall the application
```bash
./bin/prod/uninstall
```

Run docker-compose commands
```bash
./bin/prod/docker-compose <command>
```

<br />

### Development Environment

<br />

Install the application
```bash
./bin/dev/install
```

Start the application
```bash
./bin/dev/up
```

Stop the application
```bash
./bin/dev/down
```

Uninstall the application
```bash
./bin/dev/uninstall
```

Run composer commands
```bash
./bin/dev/composer <command>
```

Run docker-compose commands
```bash
./bin/dev/docker-compose <command>
```
