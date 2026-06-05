# Project 2: Task Management System (Trello-like)

A board-based task management application. Users create boards, organize tasks into lists (To Do, In Progress, Done), add cards with descriptions and due dates, and move cards between lists.

## Learning Objectives

- **OOP**: Models with relationships (Board has Lists, List has Cards)
- **PDO**: Parameterized queries for CRUD operations
- **Session Management**: Authentication state, flash messages
- **MVC**: Clean separation across models, views, controllers
- **Namespaces & PSR-4**: Composer autoloading
- **Form Handling**: POST forms for create, move, delete operations
- **Date Handling**: Due date tracking and overdue detection
- **User Assignment**: Assign cards to users

## Features

- User registration and login
- Create boards with automatic default lists (To Do, In Progress, Done)
- Add, move, and delete cards within lists
- Card descriptions and due dates
- Overdue card highlighting
- Card-to-user assignment
- Board deletion with cascading card removal
- Responsive board layout

## Setup Instructions

```bash
cd php-mastery-course/Level-2-Intermediate-PHP/projects/project-02-task-manager

composer install

mysql -u root -p < migrations/init.sql

# Edit config/database.php with your MySQL credentials

composer serve
# Visit http://localhost:8001
```

## Usage Examples

1. Register an account at `/register`
2. Create a board with the inline form
3. Add cards to each list using the "Add Card" toggle
4. Move cards between lists using the dropdown selector
5. Set due dates to see overdue highlighting
6. Delete cards or entire boards as needed

## Database Schema

```
users        (id, username, email, password, created_at)
boards       (id, title, description, user_id, created_at)
board_lists  (id, title, board_id, position)
cards        (id, title, description, board_list_id, assigned_user_id, due_date, position, created_at)
```

## Code Structure

```
project-02-task-manager/
├── composer.json
├── config/database.php
├── migrations/init.sql
├── public/index.php
├── src/
│   ├── Core/
│   │   ├── Database.php
│   │   ├── Request.php
│   │   ├── Router.php
│   │   ├── Session.php
│   │   └── View.php
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── BoardController.php
│   │   └── CardController.php
│   └── Models/
│       ├── Board.php
│       ├── BoardList.php
│       ├── Card.php
│       └── User.php
└── templates/
    ├── layout.php
    ├── auth/login.php
    ├── auth/register.php
    ├── boards/index.php
    ├── boards/show.php
    └── errors/404.php
```

## Concepts Practiced

| Concept | Usage |
|---------|-------|
| OOP | Models, Controllers, Core utilities |
| PDO | Prepared statements for all queries |
| MVC | Routes → Controllers → Models → Views |
| Namespaces | PSR-4 App\ namespace |
| Session Auth | Login persistence |
| CRUD | Full create, read, move/update, delete for cards |
| Exception Handling | Database connection errors |
| Relationships | Board → List → Card (one-to-many) |
