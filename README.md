# Wordpress React Plugin Boilerplate

A boilerplate for starting WordPress React plugins quickly.

## Details
Use this project as a starter for your [modular][modularity] WordPress plugin!

### Features
- **Docker** - Develop and test your plugin with Docker. Use an environment
    tailored for your plugin. See changes instantly in the browser. Build
    a Docker image containing a complete WordPress installation with your
    plugin and all pre-requisites.
    
- **PHPStorm** - Configuration for integrations of arguably the best PHP
    IDE out there, including:
    
    * **Database client** - View and manipulate the database from PHPStorm.
    * **Composer** - Install and manage PHP dependencies on the correct version of PHP without leaving the IDE.
    * **PHPUnit** - Run tests and get reports directly in PHPStorm.
    * **xDebug** - Set breakpoints and inspect your code in PHPStorm.
    * **Code coverage** - See what has not been tested yet in a friendly GUI.

- **Static Code Analysis** - Maintain a consistent coding style, and catch problems early.

    * **[Psalm][]** - Inspects your code for problems.
    * **[PHPCS][]** - Checks your code style. [PHPCBF][] can fix some of them automatically.

- **Continuous Integration** - Automatically verify that all contributions comply with
    project standards with [GitHub Actions][].

- **Modularity** - Keep concerns separated into [modules][modularity], which can be freely
    moved out of the package at any time thanks to the [`composer-merge-plugin`][].

- **Build Script** - Use a single [GNU Make][] entrypoint to build the plugin, including modules,
    in place; or, build a dist version without affecting current working directory.


### Usage

#### Getting Started

1. Use this template

    This GitHub repository is a [template][template-repo]. [Use it][use-template-repo] to create a project of your own from.
    
    Of course, you can always clone and push it elsewhere manually, or use another method of forking, if more appropriate.

2. Customize project

    _Note_: Asterisk `*` below denotes that changing this value requires rebuild of the images in order
    to have effect on the dev environment.

    - Copy `.env.example` to `.env`.
    ```
       cp .env.example .env
    ```
    
    - `.env`:
        * `PLUGIN_NAME` - The slug of your plugin. Must correspond to the name of the plugin folder.
        * `BASE_PATH` - If you are using [Docker Machine][], i.e. on any non-Linux system, set this
            to the absolute path to the project folder _inside the machine_. If you are on Linux,
            you do not need to change this.
        * `PROJECT_MOUNT_PATH` - The path to mount the project folder into. This should be the absolute
            path to the folder of your plugin inside the container.
        * `PROJECT_NAME` - Slug of your project. Used mainly for naming containers with [`container_name`][].
            This is helpful to run multiple projects on the same machine.
        * `PHP_BUILD_VERSION` - The version of PHP, on which the plugin will be _built_. This should
            correspond to the minimal PHP requirement of your plugin. Used to determine the tag of
            the [`php`][] image.
        * `PHP_TEST_VERSION`* - The version of PHP, on which the plugin will be _run_. This should
            correspond to the maximal PHP requirement of your plugin. Used to determine the tag of
            the [`wordpress`][] image.
        * `WORDPRESS_VERSION`* - The version of WordPress, on which the plugin will be _run_. Used to determine the tag of
            the [`wordpress`][] image.
        * `DB_USER_PASSWORD`* - This and other `DB_*` variables are used to determine the password
            to the WordPress database. Change these if you want to secure your deployed application.
        * `WP_DOMAIN`* - The domain name of the WordPress application, which contains your plugin.
            Among other things, used to set up the local dev image. Corresponds to the alias
            used in the `hosts` file, if local. This value is also used in the PHPStorm's DB integration.
            If this value is changed, PHPStorm's configuration must be updated.
        * `WP_TITLE`* - The title of the WordPress application, which contains your plugin.
            No quotes, because Docker does not expand variables in this file. It is used during automatic
            installation of WordPress inside the local dev image. This value is also used in the
            PHPStorm's DB integration. If this value is changed, PHPStorm's configuration must be updated.
        * `ADMIN_USER`* - This and other `ADMIN_*` variables are used to determine WordPress admin
            details during automatic WordPress installation with [WP-CLI][].

    - `composer.json`:
        * `name` - Name of your package.
        * `description` - Description of your package.
        * `authors` - You and/or your company details.
        * `require` - Your project's package and platform requirements. You may want to change the PHP
            version if your minimal requirement is different. Don't forget to update `PHP_BUILD_VERSION`
            in `.env`.
        * `require-dev` - Your project's development requirements. You may want to add plugins here
            to get related IDE features; see [Adding Plugins][adding-plugins] for more information on this subject.

    - Module `composer.json`:
        This bootstrap uses the awesome [`composer-merge-plugin`][] to keep module dependencies
        together with modules. This allows keeping track of which dependencies belong to which
        modules, detect dependency incompatibilities, and moving modules out of the package
        into packages of their own when necessary.
        
        Modules can be installed from other packages, or included in the package. In the latter
        case, they should be added to the directory `modules`. One such module, the `demo`
        module of the plugin, is already included in the package. This is there only for demonstration
        purposes, and can be renamed and re-written, or entirely removed. Either way, see
        [Adding Modules][adding-modules] for more information on how to configure which modules
        the application will use.

        **Important**: Adding modules to other modules as dependencies is considered bad practice.
        Modules don't interact with each other directly, but use an approach similar to DDD but applied
        to service definitions to ensure that they are as isolated as possible. Instead, have the app
        (project's main package) depend on other modules, and wire them together in the app's main module.
        One legit reason for a module to depend on another module is if that other module isn't necessarily
        loaded as a module, but its symbols are needed somewhere. However, in this case it's important to
        avoid thinking of it as a module until it is added as the app's dependency, and to think of it
        as simply a _library_.

3. Build everything
    1. Install node and compile js files
    
        ```
       docker compose run --rm build npm install
       ```
       
       then compile
        ```
       docker compose run --rm build npm install
       ```
       
    
    2. Build the environment.

        In order to develop, build, and test the plugin, certain things are required first.
        These include: the database, WordPress core, PHP, Composer, and web server.
        The project ships with all of this pre-configured, and the Docker services must first
        be built:

       ```
       bash setup.sh && docker compose build
       ```
       
    3. Build the plugin in place.
   
        In order for the project source files to have the desired effect,
        they first must be built into their runtime version. This may include:
        installing dependencies, transpilation, copying or archiving files, whatever
        the modules require to have desired effect, etc.
        At the same time, a single entrypoint to various tasks performed as part
        of the project build or QA allows for more centralized and automated control
        over the project.

        For this reason, the Makefile is shipped with the project, declaring commands
        for commonly run tasks, including build. Run the following command to build
        the plugin, including modules, in the plugin source directory: this makes it
        possible to preview and test changes instantly after they are made.

        ```
        docker compose run --rm build make build
        ```
       
        _Note_: This step includes installation of declared dependencies.
        See [Updating Dependencies][updating-dependencies] for more info on this subject.
    
4. Spin up the dev environment
    
    Run the following command in the terminal. If you use Docker Machine, you will need to
    start it and configure your environment first with [`docker-machine start`][] and
    [`docker-machine env`].
    
    ```bash
    docker compose up -d wp_dev 
    ```

   This will bring up only the dev environment and its dependencies, which right now is
   the database. The database is a separate service, because in a deployed environment
   you may choose to use a different DB server.

   After this, add an entry to your local [hosts file][]. The host should correspond to
   the value of `WP_DOMAIN` from the `.env` file. The IP would be Docker machine's IP
   address. On Linux, this is the same as [your machine's IP address][] on the local
   network, and usually `127.0.0.1` (localhost) works. If you are using Docker
   Machine (in a non-Linux environment), use `docker-machine ip` to find it.

   Now you should be able to visit that domain, and see the website. The admin username
   and password are both `admin` by default, and are determined by the `ADMIN_USER`
   and `ADMIN_PASS` variables from the `.env` file. Your plugin should already be
   installed and active, and no other plugins should be installed. If this is not
   the case, inspect the output you got from `docker compose up`.

   If you use PHPStorm integrations that involve Docker, such as Composer,
   you maybe receive the error "Docker account not found". This is because, for some reason,
   PHPStorm requires the same name of the Docker deployment configuration to be used in all
   projects, and there currently does not seem to be a way to commit that to the VCS.
   Because of this, you are required to create a Docker deployment yourself. Simply go to
   _Project Settings_ > _Docker_ and create a configuration named precisely "Docker Machine".

5. Release

    When you would like to release the current working directory as an installable plugin archive,
    the shipped build script needs to perform a few transformations (like optimize prod dependencies),
    and archive the package in a specific way. The following command will result in an archive
    with name similar to `plugin-0.1.1-beta21+2023-08-12-12-37-22_105188ec9180.zip` being added
    to `build/release`:

    ```sh
     docker compose run --rm build make release RELEASE_VERSION=0.1.1-beta21
    ```
   
    As you can see, the resulting archive's name will reflect the time and commit hash
    as SemVer metadata, aside from the version itself. If `RELEASE_VERSION` is omitted,
    `dev` is used by default to indicate that this is not a tagged milestone, but work in progress.

    _Note_: If the current working directory contains any edits registerable by Git
    (disregarding any `.gitignore` rules), the commit hash will reflect a point in history
    of the files in `build/dist`, rather than of project history. To ensure that a concrete
    version is being released, clean the directory tree entirely. The best way to do that is
    probably to create a fresh clone.

#### Updating Dependencies
Composer is installed into the `build` service's image. To run composer commands,
use `docker compose run`. For example, to update dependencies you can run the following:

```bash
docker compose run --rm build composer update
```

If you use PHPStorm, you can use the [composer integration][], as the project
is already configured for this.

_Note_: If PHPStorm does not automatically assign a PHP interpreter for Composer,
set it to use the "Build" interpreter. All build tasks, including dep installation,
must be run inside the `build` service, which corresponds to that interpreter.

**Do not run `composer update` for the modules' `composer.json` file!**
All Composer operations must be performed on the root package's `composer.json` file.

Any changes to the project folder are immediately reflected in the dev environment,
and this includes the `vendor` folder and `composer.lock` file. This is because
the project's folder is mounted into the correct place in the WordPress container.

#### Generating distribution js files

Distribution js files are need, so next command is required: 

```bash
docker compose run --rm build npm run compile
```

#### Adding Modules
This boilerplate promotes modularity, and supports [Dhii modules][] out of the box.
Any such module that exposes a [`ModuleInterface`][] implementation can be loaded,
allowing it to run in the application, and making its services available.

The list of modules returned by `src/modules.php` is the authoritative source
of modules in the application. Because it is PHP code, modules can be loaded
in any required way, including:

- Simple instantiation of a module class that will be autoloaded.

    If your module class is on one of the autoload paths registered with e.g. Composer,
    you can just instantiate it as you would any other class. This is a
    very quick and simple way to load some modules.


- Usage of a factory class or file.

    In order to make modules de-coupled from the application, but to still be able
    to provide dependencies from the application to the module, it is sometimes
    desirable to use a "padding" between the application and the module's
    initialization. For this reason, in projects using this bootstrap you may sometimes find an
    `module.php` file. This file returns a function which, given some parameters
    like the root project path, will return a [`ModuleInterface`][] instance.
    Another approach could be to use a named constructor, or even a dedicated
    factory class.

- Scanning certain paths.

    If modules do not conflict in any way, the module load order may be irrelevant.
    In this case, it is possible to auto-discover modules by, for example, scanning
    certain folders for some entrypoints or config files. Implement whatever
    auto-discovery mechanism you wish, as long as the module instances
    end up in the authoritative list.

##### External Modules
To add a module from another package, require that package with Composer
and add the `ModuleInterface` instance to the list.

##### Local Modules
To add a local module, add the module to the `modules` folder,
and do the same as for any other module. Local modules may also declare their own
dependencies by adding a `composer.json` file to their root folder.
These files will be picked up by Composer when updating dependencies in
the project root, thanks to the [`composer-merge-plugin`][], provided
that `composer update --lock` is run before `composer update`. This is
a great way to separate module dependencies from other dependencies.
Consult that Composer plugin's documentation for more information.

#### Adding Plugins
If your plugin depends on or can integrate with other plugins, you may want to add them
to the environment. In order to get IDE features such as auto-suggest for other plugin code,
you may want to add them to your `require-dev`.

In order to have the test WordPress website to have another plugin installed and active,
add an [appropriate WP-CLI command][wpcli-plugin-install] to the `docker/wp-entrypoint.sh` script.
Example:

```bash
wp plugin install bbpress --version=2.6.9 --activate --allow-root --path="${DOCROOT_PATH}"
```

Please note:

- The `--allow-root` flag is required, because this will be run by Docker as the superuser.
- The `--path` option is also required, and must be set to `$DOCROOT_PATH`, to make sure that
all tools work on the same path. You may also use other variables from `.env` in this file,
as long as they are configured to be passed into the service by `docker-compose.yml`.
- The `--activate` flag activates the plugin after it's installed.

#### QA
Run all QA tools at once by using the `qa` target in the included Makefile.
All QA is done in the `test` service.

```bash
docker compose run --rm test make qa
```

##### Testing Code

For unit test, we need to install sandbox wordpress environment, do next: 

Enter the container: 
```bash
docker compose exec wp_dev bash
```
once inside, go to plugin directory: 

```bash
cd /var/www/html/wp-content/plugins/plugin
```

then install wordpress environment
```bash
./install-wp-tests-docker.sh wordpress_test wordpress wordpress db_test:3306 6.6.2
```



Run all tests at once using the `test` target:

```bash
docker compose run --rm test make test
```

- **PHPUnit**

  This bootstrap includes [PHPUnit][]. It is already configured, and you can test
  that it's working by running the sample tests:

  ```bash
  docker compose run --rm test make test-php
  ```

  - Will also be run automatically on CI.
  - PHPStorm [integration][phpstorm-phpunit] included.

##### Static Analysis
Run all static analysis tools at once by using the `scan` target:

```bash
docker compose run --rm test make scan
```

- **Psalm**

  Run Psalm in project root:

    ```bash
    docker compose run --rm test vendor/bin/psalm
    ```

    - Will also be run automatically on CI.
    - PHPStorm [integration][phpstorm-psalm] included.

- **PHPCS**

  Run PHPCS/PHPCBF in project root:

    ```bash
    docker compose run --rm test vendor/bin/phpcs -s --report-source --runtime-set ignore_warnings_on_exit 1
    docker compose run --rm test vendor/bin/phpcbf
    ```

    - By default, uses [PSR-12][] and some rules from the [Slevomat Coding Standard][].
    - Will also be run automatically on CI.
    - PHPStorm [integration][phpstorm-phpcs] included.


##### Debugging
The bootstrap includes xDebug in the `test` service of the Docker environment,
and PHPStorm configuration. To use it, right click on any test or folder within
the `tests` directory, and choose "Debug". This will run the tests with xDebug
enabled. If you receive the error about [`xdebug.remote_host`][] being set
incorrectly and suggesting to fix the error, fix it by setting that variable
to [your machine's IP address][] on the local network in the window that
pops up. After this, breakpoints in any code reachable by PHPUnit tests,
including the code of tests themselves, will cause execution to pause,
allowing inspection of code.

If you change the PHP version of the `test` service, the debugger will stop working.
This is because different PHP versions use different versions of xDebug, and
because the path to the xDebug extension depends on its version, that path will
also change, invalidating the currently configured path.
To fix this, the "Debugger extension" fields in the interpreter settings screen
needs to be updated. You can run `docker compose run test ls -lah /usr/local/lib/php/extensions`
to see the list of extensions. One of them should say something like
`no-debug-non-zts-20170718`. Change the corresponding part of the "Debugger extension"
path value to that string.

At this time, inspection of code that runs _during a web request_ is not available.

#### Database UI
This bootstrap comes ready with configuration for PHPStorm's [database integration][].
To use it, its settings must be up to date with the value of `DB_USER_PASSWORD`.
Using it is highly recommended, as it is an integrated DB client, and will
provide assistance during coding.

Alternatively, you are welcome to install and configure a [phpMyAdmin][docker-phpmyadmin]
service or similar.

[modularity]: https://dev.to/xedinunknown/cross-platform-modularity-in-php-30bo
[Docker Machine]: https://github.com/docker/machine
[WP-CLI]: https://wp-cli.org/
[phpMyAdmin]: https://www.phpmyadmin.net/
[PSR-12]: https://www.php-fig.org/psr/psr-12/
[Slevomat Coding Standard]: https://github.com/slevomat/coding-standard
[PHPUnit]: https://phpunit.de/
[Psalm]: https://psalm.dev/
[PHPCS]: https://github.com/squizlabs/PHP_CodeSniffer
[PHPCBF]: https://github.com/squizlabs/PHP_CodeSniffer/wiki/Fixing-Errors-Automatically
[GNU Make]: https://www.gnu.org/software/make/manual/make.html
[GitHub Actions]: https://github.com/features/actions
[Dhii modules]: https://github.com/Dhii/module-interface
[hosts file]: https://www.howtogeek.com/howto/27350/beginner-geek-how-to-edit-your-hosts-file/
[your machine's IP address]: https://www.whatismybrowser.com/detect/what-is-my-local-ip-address
[composer integration]: https://www.jetbrains.com/help/phpstorm/using-the-composer-dependency-manager.html#updating-dependencies
[database integration]: https://www.jetbrains.com/help/phpstorm/configuring-database-connections.html
[`container_name`]: https://docs.docker.com/compose/compose-file/#container_name
[`composer-merge-plugin`]: https://github.com/wikimedia/composer-merge-plugin
[`php`]: https://hub.docker.com/_/php
[`wordpress`]: https://hub.docker.com/_/wordpress
[`docker-machine start`]: https://docs.docker.com/machine/reference/start/]
[`docker-machine env`]: https://docs.docker.com/machine/reference/env/
[`xdebug.remote_host`]: https://xdebug.org/docs/all_settings#remote_host
[`ModuleInterface`]: https://github.com/Dhii/module-interface/blob/develop/src/ModuleInterface.php
[WI-54242]: https://youtrack.jetbrains.com/issue/WI-54242
[phpstorm-phpunit]: https://www.jetbrains.com/help/phpstorm/using-phpunit-framework.html
[phpstorm-psalm]: https://www.jetbrains.com/help/phpstorm/using-psalm.html
[phpstorm-phpcs]: https://www.jetbrains.com/help/phpstorm/using-php-code-sniffer.html
[template-repo]: https://docs.github.com/en/repositories/creating-and-managing-repositories/creating-a-template-repository
[use-template-repo]: https://docs.github.com/en/repositories/creating-and-managing-repositories/creating-a-repository-from-a-template
[updating-dependencies]: #user-content-updating-dependencies
[wpcli-plugin-install]: https://developer.wordpress.org/cli/commands/plugin/install/
[adding-plugins]: #user-content-adding-plugins
[adding-modules]: #user-content-adding-modules
[docker-phpmyadmin]: https://hub.docker.com/_/phpmyadmin
