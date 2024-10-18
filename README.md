# Advance Tasks Management System

The **Advance Tasks Management System** is a RESTful web service built with PHP and MySQL that allows to admins and managers to assign tasks to users and user can be change status tasks. As well as filtering tasks by priority and task status.
Add comments and attachment files to users and tasks. 

## Table of Contents

-   [Advance Tasks Management System](#advance-tasks-management-system)
    -   [Table of Contents](#table-of-contents)
    -   [Features](#features)
    -   [Getting Started](#getting-started)
        -   [Prerequisites](#prerequisites)
        -   [Installation](#installation)
        -   [Postman Test](#postman-test)

## Features

1. Tasks

-   Add new tasks to the system
-   Retrieve details of a specific task or all tasks
-   Update task information by admin and manager
-   Search for tasks by priority and status task
-   Assignee task to users with limited time
-   Delete tasks from the system by admin
-   Retrive tasks after deleted it from system (Soft-Delete)
-   Add comment to task
-   Add attachment file to task

2. Users

-   Create new user, admin and manager
-   Retrieve details of all users by just admin
-   Retrieve details of a specific user by owned account
-   Login for user, admin and manager
-   Refresh token
-   Logout for user, admin and manager
-   Update user profile by just admin
-   Delete user account by owned account
-   Retrive users after deleted it from system by just admin (Soft-Delete)
-   Change status task
-   Add comment to user
-   Add attachment file to user

3. Comments
-   Create new comments and assigned them to users and tasks
-   Display specified comment by admin
-   Display all comments by admin
-   Delete specified comment by admin

4. Attachments
-   Create new attachments and assigned them to users and tasks
-   Display specified attachment by admin
-   Display all attachments by admin
-   Delete specified attachment by admin

5. Roles
-   Create new role and assigned it to user
-   Display roles by admin
-   Display specified role by admin 

## Getting Started

These instructions will help you set up and run the Advance Tasks Management System on your local machine for development and testing purposes.

### Prerequisites

-   **PHP** (version 7.4 or later)
-   **MySQL** (version 5.7 or later)
-   **Apache** or **Nginx** web server
-   **Composer** (PHP dependency manager, if you are using any PHP libraries)

### Installation

1. **Clone the repository**:

    ```
    git clone https://github.com/osama806/Advance-Tasks-Management-System.git
    cd Advance-Tasks-Management-System
    ```

2. **Set up the environment variables:**:

Create a .env file in the root directory and add your database configuration:

```
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=advance-tasks-management-system
DB_USERNAME=root
DB_PASSWORD=password
```

3. **Set up the MySQL database:**:

-   Create a new database in MySQL:
    ```
    CREATE DATABASE advance-tasks-management-system;
    ```
-   Run the provided SQL script to create the necessary tables:
    ```
    mysql -u root -p advance-tasks-management-system < database/schema.sql
    ```

4. **Configure the server**:

-   Ensure your web server (Apache or Nginx) is configured to serve PHP files.
-   Place the project in the appropriate directory (e.g., /var/www/html for Apache on Linux).

5. **Install dependencies (if using Composer)**:

```
composer install
```

6. **Start the server:**:

-   For Apache or Nginx, ensure the server is running.
-   The API will be accessible at http://localhost/advance-tasks-management-system.

### Postman Collection

-   Link:
    ```
    https://documenter.getpostman.com/view/32954091/2sAXxWZp2s
    ```
