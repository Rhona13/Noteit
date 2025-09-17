# NoteIt - Note Taking Application

A modern, responsive note-taking application with user authentication and database integration.

## Features

- **User Authentication**: Secure login and registration system
- **Note Management**: Create, edit, delete, and organize notes
- **Favorites**: Mark important notes as favorites
- **Archive**: Archive old notes to keep workspace clean
- **Search**: Find notes quickly with search functionality
- **Color Coding**: Organize notes with different colors
- **Responsive Design**: Works on desktop and mobile devices

## Setup Instructions

### Prerequisites
- XAMPP (or similar local server with PHP and MySQL)
- Web browser

### Installation

1. **Start XAMPP**
   - Start Apache and MySQL services in XAMPP Control Panel

2. **Place Files**
   - Copy all files to your XAMPP htdocs folder (e.g., `C:\xampp\htdocs\NoteIt`)

3. **Setup Database**
   - Open your web browser and go to: `http://localhost/NoteIt/setup.php`
   - This will create the database and tables automatically
   - You should see a success message

4. **Access the Application**
   - Go to: `http://localhost/NoteIt/`
   - Click "Register" to create a new account or use the default credentials:
     - Username: `admin`
     - Password: `admin123`

## File Structure

```
NoteIt/
├── api/
│   ├── auth.php          # Authentication API
│   ├── check_auth.php    # Session validation
│   └── notes.php         # Notes CRUD API
├── config/
│   └── database.php      # Database configuration
├── database/
│   └── schema.sql        # Database schema
├── js/
│   └── admin.js          # Dashboard JavaScript
├── index.html            # Homepage
├── login.html            # Login page
├── register.html         # Registration page
├── admin.html            # Dashboard (main app)
├── styles.css            # Main styles
├── admin.css             # Dashboard styles
├── setup.php             # Database setup script
└── README.md             # This file
```

## Database Schema

### Users Table
- `id` - Primary key
- `username` - Unique username
- `email` - Unique email address
- `password` - Hashed password
- `created_at` - Account creation timestamp
- `updated_at` - Last update timestamp

### Notes Table
- `id` - Primary key
- `user_id` - Foreign key to users table
- `title` - Note title
- `content` - Note content
- `color` - Note color (hex code)
- `is_favorite` - Boolean for favorite status
- `is_archived` - Boolean for archive status
- `created_at` - Note creation timestamp
- `updated_at` - Last update timestamp

## Usage

1. **Registration/Login**: Create an account or login with existing credentials
2. **Create Notes**: Click "Add Notes" button to create new notes
3. **Edit Notes**: Click the edit icon on any note to modify it
4. **Organize Notes**: Use favorites and archive features to organize your notes
5. **Search**: Use the search bar to find specific notes
6. **Filter**: Use the sidebar to filter notes by category (All, Favorites, Archived)

## Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention with prepared statements
- Session-based authentication
- Input validation and sanitization
- XSS protection with HTML escaping

## Browser Compatibility

- Chrome (recommended)
- Firefox
- Safari
- Edge

## Troubleshooting

### Database Connection Issues
- Ensure MySQL is running in XAMPP
- Check database credentials in `config/database.php`
- Verify database exists and tables are created

### Login Issues
- Make sure you've run `setup.php` first
- Check if the user exists in the database
- Verify password is correct

### Permission Issues
- Ensure web server has read/write permissions
- Check file permissions on the project directory

## Development

To extend this application:

1. **Add New Features**: Modify the API endpoints in the `api/` folder
2. **Update UI**: Edit the HTML and CSS files
3. **Database Changes**: Update `database/schema.sql` and run setup again
4. **Add JavaScript**: Extend `js/admin.js` for new functionality

## License

This project is open source and available under the MIT License.
