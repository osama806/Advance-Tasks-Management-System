# Task Management API

The **Task Management API** is a RESTful web service built with PHP and MySQL that allows to admins and managers to assign tasks to users and user can be delivery task when finish it. As well as filtering tasks by priority and task status.

## Table of Contents

-   [Task Management API](#task-management-api)
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

4. Users

-   Create new user, admin and manager
-   Retrieve details of all users by just admin
-   Retrieve details of a specific user by owned account
-   Login for user, admin and manager
-   Refresh token 
-   Logout for user, admin and manager
-   Update user profile by just admin
-   Delete user account by owned account
-   Retrive users after deleted it from system by just admin (Soft-Delete)
-   Delivery task to manager in limited time

## Getting Started

These instructions will help you set up and run the Task Management System on your local machine for development and testing purposes.

### Prerequisites

-   **PHP** (version 7.4 or later)
-   **MySQL** (version 5.7 or later)
-   **Apache** or **Nginx** web server
-   **Composer** (PHP dependency manager, if you are using any PHP libraries)

### Installation

1. **Clone the repository**:

    ```
    git clone https://github.com/osama806/Task-Management-System.git
    cd Task-Management-System
    ```

2. **Set up the environment variables:**:

Create a .env file in the root directory and add your database configuration:

```
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=task-management-api
DB_USERNAME=root
DB_PASSWORD=password
```

3. **Set up the MySQL database:**:

-   Create a new database in MySQL:
    ```
    CREATE DATABASE task-management-api;
    ```
-   Run the provided SQL script to create the necessary tables:
    ```
    mysql -u root -p task-management-api < database/schema.sql
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
-   The API will be accessible at http://localhost/task-management-api.

### Postman Collection

-   Link:
    ```
    https://documenter.getpostman.com/view/32954091/2sAXjRX9tL
    ```
