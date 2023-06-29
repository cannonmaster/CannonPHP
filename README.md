# CannonPHP MVC Framework

[![License: MIT](https://img.shields.io/badge/license-MIT-green)](https://opensource.org/licenses/MIT)

Cannon MVC is an exceptional web application framework that embraces an expressive and elegant syntax. We strongly believe that development should be an enjoyable and creative experience, leading to true fulfillment. With Cannon MVC, you can bid farewell to the cumbersome aspects of development by effortlessly handling common tasks encountered in numerous web projects. Some of the remarkable benefits and features of Cannon MVC include:

## Features

- **Routing**: The framework offers a powerful routing system that effortlessly maps incoming requests to controller actions.
- **Di container**: Simplifies the management of dependencies, promoting loose coupling and flexible object creation and handling.
- **session and cache storage**: Provides seamless integration with multiple session and cache drivers for efficient data storage.
- **Tempalte engine**: Supports the popular Twig template engine out of the box, or allows the use of your preferred choice.
- **Hooks**: Enables the execution of custom code before and after controller actions for enhanced customization.
- **Database ORM**: Includes an intuitive and feature-rich database ORM that simplifies database operations within the framework.

## Requirements

- PHP version 8 or higher
- Composer (https://getcomposer.org) for dependency management

## Installation

1. create a new CannonPHP application using composer’s create-project command:

```bash
composer create-project --prefer-dist cannonphp/app
```

2. `cd app`, run `composer install` to install the required dependencies.
3. Configure your web server to point to the public directory as the document root.
4. Customize the framework's `Config.php` file located in the `App` directory, such as database settings, routes, etc.
5. Start building your application by creating controllers, models, and views in their respective directories.

## Usage

1. Define your application routes in the `routes.php` file located in the `App` directory.
2. Create controllers in the `App/Controller` directory to handle different actions.
3. Define models in the `App/Model` directory to interact with the database.
4. Create views in the `App/View` directory to render the presentation layer.
5. Customize the `BaseController` class according to your application's needs if necessary.
6. Extends the framework by adding your own service to the framework using service provider if necessary.

## Contributing

Contributions are welcome! If you find any issues or have suggestions for improvements, please open an issue or submit a pull request.

## License

This MVC framework is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT).
