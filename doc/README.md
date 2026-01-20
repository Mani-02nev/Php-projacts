# 6Xpress - E-Commerce Platform

A modern, feature-rich e-commerce platform built with PHP, featuring a clean black and white design with Bootstrap Icons.

## Features

- 🛍️ Product catalog with search and filtering
- 🛒 Shopping cart functionality
- 👤 User authentication (login/register)
- 📦 Product details with ratings
- 💳 Checkout process
- 📊 Admin panel for product management
- 📱 Responsive design
- 🎨 Modern UI with auto-scrolling carousel

## Tech Stack

- **Backend**: PHP 8.2
- **Frontend**: HTML5, CSS3, JavaScript
- **Icons**: Bootstrap Icons
- **Server**: Apache
- **Data Storage**: CSV files

## Local Development

1. Clone the repository:
   ```bash
   git clone <your-repo-url>
   cd colage-pojects
   ```

2. Start a local PHP server:
   ```bash
   php -S localhost:8000
   ```

3. Open your browser and navigate to `http://localhost:8000`

## Deployment on Koyeb

This application is configured for easy deployment on Koyeb using Docker.

### Prerequisites
- GitHub account
- Koyeb account (free tier available)
- Git installed locally

### Deployment Steps

1. **Push to GitHub** (if not already done):
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   git branch -M main
   git remote add origin <your-github-repo-url>
   git push -u origin main
   ```

2. **Deploy on Koyeb**:
   - Go to [Koyeb Dashboard](https://app.koyeb.com)
   - Click "Create App"
   - Select "GitHub" as deployment method
   - Connect your GitHub account and select this repository
   - Koyeb will auto-detect the Dockerfile
   - Configure:
     - **Name**: 6xpress (or your preferred name)
     - **Region**: Choose closest to your users
     - **Instance**: Free (Nano) or higher
     - **Port**: 80
   - Click "Deploy"

3. **Access Your App**:
   - Once deployed, Koyeb will provide a URL like: `https://6xpress-<random>.koyeb.app`
   - Your application will be live!

## Project Structure

```
colage-pojects/
├── admin/              # Admin panel files
├── assets/             # Static assets
├── css/                # Stylesheets
├── data/               # CSV data files
│   ├── products.csv
│   ├── users.csv
│   └── orders.csv
├── includes/           # PHP includes
│   ├── config.php
│   ├── functions.php
│   ├── header.php
│   └── footer.php
├── js/                 # JavaScript files
├── index.php           # Homepage
├── products.php        # Product listing
├── product-detail.php  # Product details
├── cart.php            # Shopping cart
├── checkout.php        # Checkout page
├── login.php           # Login page
├── register.php        # Registration page
├── profile.php         # User profile
├── .htaccess           # Apache configuration
└── Dockerfile          # Docker configuration
```

## Configuration

The application uses CSV files for data storage located in the `data/` directory:
- `products.csv` - Product catalog
- `users.csv` - User accounts
- `orders.csv` - Order history

## Security Notes

- Session-based authentication
- Password hashing for user accounts
- XSS protection headers
- CSRF protection recommended for production

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## License

This project is created for educational purposes.

## Support

For issues or questions, please open an issue on GitHub.
