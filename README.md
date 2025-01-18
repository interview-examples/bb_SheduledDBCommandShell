***Project: Shedulled DBCommand Shell.***
*Documentation*

**Introduction**

This project is application that can take commands, such as "Write to DB in 15 minutes"
and it will execute said command at exactly that time. Commands can be received via command-line interface (CLI) or via some frontend.
The project written in PHP 8.3, using Composer, PDO, TWIG, CARBON. As a database used MySQL (MarioDB).

**Installation**

To install the project, please follow these steps:

1. Install **_PHP 8.3_** and **_MySQL_** on your system.
2. Update the **_database configuration file_** (***_/config/database.php_***) with your database credentials.
3. Install **_Composer_** and run the **_composer install_** command to install the project dependencies.
4. Install supervisor to run the project as a service (**_sudo supervisorctl start tasks_***). Settings can be found in the **_supervisor configuration file_** (***_/bin/tasks.conf_*** and should be placed to the **_supervisor config directory_**).
5. Run the project and manage your task list.
6.

**Usage command-line interface (CLI)**

The project provides the following commands:

* `task:add [command] [description] [executeAt]`: adds a new task
* `task:edit [id] [executeAt]`: edits an existing task
* `task:delete [id]`: deletes a task
* `tasks:list [start_page]`: lists all tasks
* `tasks:removeAll`: removes all tasks

Available commands for `task:add`:

* "Write to DB"
* "Send email"
* "Out to screen"

**Commands**

### task:add

Adds a new task to the database.

* `command`: one of the available commands (*_see above_*)
* `description`: a brief description of the task
* `executeAt`: the date and time when the task should be executed. The format is "YYYY-MM-DD HH:MM:SS" or "+15m" or "HH:MM" etc.

#### Examples

* `task:add "Write to DB" "Hello World!" "2023-01-01 12:00:00"`
* `task:add "send Email" "Good morning, boss!" "+15m"`
### task:edit

Edits an execute time of an existing task in the database.

* `id`: the ID of the task to edit
* `executeAt`: the new date and time when the task should be executed

### task:delete

Deletes a task from the database.

* `id`: the ID of the task to delete

### tasks:list

Lists all tasks in the database.

* `start_page`: the starting page number for pagination (***_PAGINATION_***)

### tasks:removeAll

Removes all tasks from the database.

**Configuration**

The project uses a **_configuration file_** (***_/config/database.php_***) to store database credentials. Please update this file with ***your*** database credentials.

**Database**

The project uses a **_MySQL database_** (***_BeachBum_***) to store tasks. The database has the following tables:

* **_tasks table_** (***_TASKS_TABLE_***): stores task information
* **_task_logs table_** (***_TASK_LOGS_TABLE_***): stores tasks execution for command **_"Write to DB"_**

**Folder structure**

....................

```
ShedulledDBCommandShell/
│
├── src/
│   ├── Controller/          # Controllers for processing requests
│   ├── Model/               # Data Models
│   ├── Repository/          # Repositories for interacting with the database
│   ├── Utils/               # Auxiliary utilities and helpers
│   ├── Framework/           # Main components of the framework (Router etc.)
│   ├── Observer/            # Implementation of the "Observer" pattern
│   └── Strategy/            # Implementation of the "Strategy" pattern
│
├── config/                  # Configuration files
│   ├── app.php              # Basic application settings (not currently used)
│   └── database.php         # Database connection settings
│
├── public/                  # Public directory of the web interface
│   ├── js/              
│   ├── css/              
│   └── index.php            # Application entry point (web interface)
│
├── tests/                   # Tests
│   ├── Unit/                # 
│   └── Feature/             # 
│
├── vendor/                  # Libraries and dependencies (Composer)
│
├── views/                  # Libraries and dependencies (Composer)
│   └── tasks/              # HTML-templates TWIG
│
├── bin/                     # Scripts to be executed from the command line
│   ├── cli                  # CLI script for adding/managing tasks
│   ├── cli-help             # 
│   └── execute-tasks        # script for automatically performing tasks
│
├── .env                     # Environment configuration file
├── composer.json            # Composer configuration
└── README.md                # Project description
```

**Error Handling**

The project uses **_error handling mechanisms_** (***_ERROR_HANDLING_***) to handle errors and exceptions. Please refer to the **_error handling documentation_** (***_ERROR_HANDLING_DOC_***) for more information.

**Troubleshooting**

If you encounter any issues with the project, please refer to the **_troubleshooting guide_** (***_TROUBLESHOOTING_GUIDE_***).

**Conclusion**

This task manager project provides a simple and efficient way to manage tasks using a command-line interface. With its flexible configuration options and robust error handling mechanisms, this project is suitable for a wide range of applications.

**Tests**

The project includes **_unit tests_** (***_vendor/bin/phpunit --testdox tests/Unit/TaskRepositoryTest.php_***) and **_feature tests_** (***_vendor/bin/phpunit --testdox tests/Feature/TaskControllerTest.php_***). 