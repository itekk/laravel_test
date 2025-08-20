# Laravel Task Manager

A simple task management web application built with Laravel. This application allows users to create, edit, delete, and reorder tasks with drag-and-drop functionality. Tasks can be organized into projects for better management.

## Features

- **Task Management**: Create, edit, and delete tasks
- **Drag & Drop Reordering**: Reorder tasks by dragging and dropping them
- **Priority System**: Tasks are automatically prioritized based on their order (#1 priority at top)
- **Project Organization**: Group tasks into projects
- **Project Filtering**: Filter tasks by specific projects
- **Responsive Design**: Works on desktop, tablet, and mobile devices
- **Real-time Updates**: AJAX-powered task reordering without page refresh

## Requirements

- PHP 8.2 or higher
- Composer
- MySQL 5.7+ or MariaDB 10.3+
- Web server (Apache/Nginx) or PHP built-in server for development

## Installation & Setup

### 1. Clone or Download the Project
```bash
# If you have git
git clone <repository-url>
cd laravel-task-manager

# Or extract the ZIP file to your desired directory
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Environment Configuration
```bash
# Copy the environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Setup

Edit the `.env` file and configure your database connection:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Create the database in MySQL:
```sql
CREATE DATABASE task_manager;
```

### 5. Run Database Migrations
```bash
php artisan migrate
```

## Running the Application

### Development Server
```bash
php artisan serve
```
The application will be available at `http://localhost:8000`

### Production Deployment

For production deployment:

1. **Configure Web Server**: Point your web server's document root to the `public` directory
2. **Set Environment**: Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
3. **Optimize Application**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
4. **Set Permissions**: Ensure the `storage` and `bootstrap/cache` directories are writable

## Usage

### Tasks
1. **Create Task**: Click "Add Task" button, enter task name and optionally select a project
2. **Edit Task**: Click the edit button (pencil icon) next to any task
3. **Delete Task**: Click the delete button (trash icon) and confirm
4. **Reorder Tasks**: Drag and drop tasks to reorder them. Priority numbers update automatically
5. **Filter by Project**: Use the dropdown to view tasks from specific projects

### Projects
1. **View Projects**: Click "Projects" in the navigation
2. **Create Project**: Click "Add Project" and enter name and description
3. **Edit Project**: Click the edit button on any project card
4. **Delete Project**: Click the delete button (this will also delete all associated tasks)
5. **View Project Tasks**: Click "View Tasks" to see only tasks from that project


