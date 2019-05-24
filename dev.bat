@echo off

set cmd=%1
set allArgs=%*
for /f "tokens=1,* delims= " %%a in ("%*") do set allArgsExceptFirst=%%b
set secondArg=%2
set valid=false

:: If no command provided, list commands
if "%cmd%" == "" (
    set valid=true
    echo The following commands are available:
    echo   .\dev up
    echo   .\dev down
    echo   .\dev test
    echo   .\dev phpstan [args]
    echo   .\dev phpunit [args]
    echo   .\dev yarn [args]
    echo   .\dev composer [args]
    echo   .\dev login [args]
    echo   .\dev package
)

:: If command is up or run, we need to run the docker containers and install composer and yarn dependencies
if "%cmd%" == "up" (
    set valid=true
    docker-compose -f docker-compose.yml -p cast up -d
    docker exec -it --user root --workdir /app php-cast bash -c "cd /app && composer install"
    docker exec -it --user root --workdir /app node-cast bash -c "yarn"

    if not "%cmd%" == "run" (
        docker exec -it --user root --workdir /app node-cast bash -c "yarn run fab --build-only"
    )

    cd platform
    call yarn
    cd ..
)

:: If the command is down, then we want to stop docker
if "%cmd%" == "down" (
    set valid=true
    docker-compose -f docker-compose.yml -p cast down
)

:: Run test if requested
if "%cmd%" == "test" (
    set valid=true
    call :phpstan
    call :phpunit
)

:: Run phpstan if requested
if "%cmd%" == "phpstan" (
    set valid=true
    call :phpstan
)

:: Run phpunit if requested
if "%cmd%" == "phpunit" (
    set valid=true
    call :phpunit
)

:: Run yarn if requested
if "%cmd%" == "yarn" (
    set valid=true
    docker kill node-cast
    docker-compose -f docker-compose.yml -p cast up -d
    docker exec -it --user root --workdir /app node-cast bash -c "%allArgs%"
)

:: Run composer if requested
if "%cmd%" == "composer" (
    set valid=true
    docker exec -it --user root --workdir /app php-cast bash -c "%allArgs%"
)

:: Login to a container if requested
if "%cmd%" == "login" (
    set valid=true
    docker exec -it --user root %secondArg%-cast bash
)

:: Package
if "%cmd%" == "package" (
    set valid=true
    docker exec -it --user root --workdir /app/work/ee-standalone-packaging php-cast bash -c "cd /app/work/ee-standalone-packaging && php package-for-ee"
)

:: If there was no valid command found, warn user
if not "%valid%" == "true" (
    echo Specified command not found
    exit /b 1
)

:: Exit with no error
exit /b 0

:: phpstan function
:phpstan
    docker exec -it --user root --workdir /app php-cast bash -c "chmod +x /app/vendor/bin/phpstan && /app/vendor/bin/phpstan analyse src %allArgsExceptFirst%"
exit /b 0

:: phpunit function
:phpunit
    docker exec -it --user root --workdir /app php-cast bash -c "chmod +x /app/vendor/bin/phpunit && /app/vendor/bin/phpunit --configuration /app/phpunit.xml %allArgsExceptFirst%"
exit /b 0
