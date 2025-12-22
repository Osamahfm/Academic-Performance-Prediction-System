# Utilities Folder

This folder contains utility scripts and tools for the EduPredict application.

## Files

### Database Setup
- **quick-setup.php** - Complete database setup wizard
  - Creates database and all tables
  - Inserts default users and sample data
  - Access: `http://localhost/projecty/utilities/quick-setup.php`

- **setup-database.php** - Alternative database setup script
  - Access: `http://localhost/projecty/utilities/setup-database.php`

### Database Management
- **database-viewer.php** - Admin database viewer tool
  - View all tables and data
  - Requires admin login
  - Access: `http://localhost/projecty/utilities/database-viewer.php`

### Database Migrations
- **database/menu_items_migration.sql** - Menu items table migration
  - Run this SQL file to create the dynamic menu system

## Usage

1. **First Time Setup**: Run `quick-setup.php` to set up the database
2. **Menu System**: Run `database/menu_items_migration.sql` to enable dynamic menus
3. **Database Viewing**: Use `database-viewer.php` (admin only) to view database contents

## Security Note

These utilities should be protected in production. Consider:
- Moving to a protected directory
- Adding IP whitelist restrictions
- Removing after initial setup








