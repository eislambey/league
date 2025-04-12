# Football Tournament Simulator

This is a football tournament simulator built with Laravel and Vue.js. It allows users to simulate football tournaments, manage teams, and view match results in an interactive and user-friendly interface.

## Features

- **Team Management**: Add, edit, and delete teams participating in the tournament.
- **Match Simulation**: Simulate matches and generate results dynamically.
- **Tournament Brackets**: Visualize tournament progress with brackets.
- **Real-time Updates**: Leverages Inertia.js for seamless updates without page reloads.
- **Responsive Design**: Fully responsive UI for desktop and mobile devices.

## Tech Stack

- **Backend**: Laravel 12.x
- **Frontend**: Vue 3.x with Vite
- **Database**: SQLite (default) or other supported Laravel databases
- **Styling**: Tailwind CSS and Bootstrap
- **Additional Tools**: RoadRunner, Ziggy, and Laravel Octane for performance optimization

## Prerequisites

- PHP 8.2 or higher
- Node.js 18.x or higher
- Composer 2.x
- SQLite (default) or another database of your choice

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-repo/football-tournament-simulator.git
   cd football-tournament-simulator
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install Node.js dependencies:
   ```bash
   npm install
   ```

4. Set up the environment file:
   ```bash
   cp .env.example .env
   ```

5. Generate the application key:
   ```bash
   php artisan key:generate
   ```

6. Set up the database (SQLite):
   - Ensure `database/database.sqlite` exists:
     ```bash
     touch database/database.sqlite
     ```
   - Run migrations:
     ```bash
     php artisan migrate
     ```
 
7. Set up the database (MySQL / MariaDB): 
    - Update `.env` file with your database credentials:
      ```env
      DB_CONNECTION=mysql 
      DB_HOST=127.0.0.1
      DB_PORT=3306
      DB_DATABASE=league
      DB_USERNAME=root
      DB_PASSWORD=password
      ```

   - Run migrations:
     ```bash
     php artisan migrate
     ```

8. Build frontend assets:
   ```bash
   npm run build
   ```

## Development

1. Start the development server:
   ```bash
   npm run dev
   ```

2. Start the Laravel server:
   ```bash
   php artisan serve
   ```

3. Open the app in your browser at `http://localhost:8000`.

## Testing

- Run PHP tests:
  ```bash
  php artisan test
  ```

- Run with coverage:
  ```bash
  php artisan test --coverage
  ```
