# 🚀 Deploying 6Xpress to Koyeb

This guide will walk you through deploying your PHP e-commerce application to Koyeb using GitHub.

## 📋 Prerequisites

Before you begin, make sure you have:

- ✅ A GitHub account ([Sign up here](https://github.com/join))
- ✅ A Koyeb account ([Sign up here](https://app.koyeb.com/auth/signup))
- ✅ Git installed on your computer
- ✅ Your project files ready

## 🔧 Step 1: Prepare Your Project

Your project is already configured with:
- ✅ `Dockerfile` - For containerization
- ✅ `.dockerignore` - To exclude unnecessary files
- ✅ `.gitignore` - To prevent committing sensitive files
- ✅ `.htaccess` - Apache configuration

## 📤 Step 2: Push to GitHub

### 2.1 Initialize Git Repository (if not already done)

Open your terminal in the project directory and run:

```bash
cd /Users/gobinath/study/E-commers/colage-pojects
git init
```

### 2.2 Add All Files

```bash
git add .
```

### 2.3 Commit Your Changes

```bash
git commit -m "Initial commit - 6Xpress E-commerce Platform"
```

### 2.4 Create a New Repository on GitHub

1. Go to [GitHub](https://github.com)
2. Click the **"+"** icon in the top right
3. Select **"New repository"**
4. Name it: `6xpress-ecommerce` (or your preferred name)
5. Keep it **Public** or **Private** (both work with Koyeb)
6. **DO NOT** initialize with README (we already have one)
7. Click **"Create repository"**

### 2.5 Link and Push to GitHub

Replace `YOUR_USERNAME` with your GitHub username:

```bash
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/6xpress-ecommerce.git
git push -u origin main
```

## 🌐 Step 3: Deploy on Koyeb

### 3.1 Create a Koyeb Account

1. Go to [Koyeb](https://app.koyeb.com/auth/signup)
2. Sign up (free tier available)
3. Verify your email

### 3.2 Connect GitHub to Koyeb

1. Log in to your [Koyeb Dashboard](https://app.koyeb.com)
2. Click **"Create App"**
3. Select **"GitHub"** as the deployment method
4. Click **"Connect GitHub"**
5. Authorize Koyeb to access your GitHub account
6. Select **"All repositories"** or choose specific repositories

### 3.3 Configure Your Deployment

1. **Select Repository**: Choose `6xpress-ecommerce` (or your repo name)
2. **Branch**: Select `main`
3. **Builder**: Koyeb will auto-detect **Dockerfile** ✅
4. **App Name**: Enter `6xpress` (or your preferred name)
5. **Region**: Choose the closest region to your users:
   - `fra` - Frankfurt (Europe)
   - `was` - Washington DC (US East)
   - `sin` - Singapore (Asia)
6. **Instance Type**: 
   - Free tier: **Nano** (512MB RAM, 0.1 vCPU)
   - Paid: **Small** or higher for better performance
7. **Port**: Enter `80` (this is important!)
8. **Health Check Path**: `/` (optional but recommended)

### 3.4 Advanced Settings (Optional)

Click **"Advanced"** to configure:

- **Environment Variables**: Add any if needed
- **Scaling**: Auto-scaling settings
- **Volumes**: Persistent storage (if needed)

### 3.5 Deploy!

1. Review your configuration
2. Click **"Deploy"**
3. Wait for deployment (usually 2-5 minutes)

## 🎉 Step 4: Access Your Application

Once deployed:

1. Koyeb will provide a URL like: `https://6xpress-YOUR-APP.koyeb.app`
2. Click the URL to open your live application
3. Your 6Xpress e-commerce platform is now live! 🚀

## 🔄 Step 5: Update Your Application

Whenever you make changes:

```bash
# Make your changes to the code
git add .
git commit -m "Description of changes"
git push origin main
```

Koyeb will automatically detect the changes and redeploy your application!

## 🐛 Troubleshooting

### Application Not Loading?

1. **Check Logs**: 
   - Go to Koyeb Dashboard → Your App → Logs
   - Look for error messages

2. **Port Configuration**:
   - Ensure port is set to `80` in Koyeb settings
   - Dockerfile exposes port 80

3. **Build Failed**:
   - Check if all files are committed to GitHub
   - Verify Dockerfile syntax
   - Check Koyeb build logs

### CSV Files Not Working?

- CSV files are included in the Docker image
- For persistent data, consider using Koyeb volumes or external database

### Performance Issues?

- Upgrade from Nano to Small instance
- Enable auto-scaling in Koyeb settings
- Consider using a CDN for static assets

## 📊 Monitoring

In Koyeb Dashboard, you can monitor:
- **Metrics**: CPU, Memory, Network usage
- **Logs**: Real-time application logs
- **Deployments**: History of all deployments
- **Health**: Application health status

## 💰 Pricing

- **Free Tier**: 
  - 1 Nano instance
  - 100GB bandwidth/month
  - Perfect for testing and small projects

- **Paid Plans**: 
  - Start at $5/month
  - More resources and instances
  - Better performance

## 🔒 Security Tips

1. **Environment Variables**: Store sensitive data in Koyeb environment variables
2. **HTTPS**: Koyeb provides free SSL certificates automatically
3. **Database**: Consider using external database for production
4. **Backups**: Regularly backup your CSV data files

## 📚 Additional Resources

- [Koyeb Documentation](https://www.koyeb.com/docs)
- [Koyeb GitHub Integration](https://www.koyeb.com/docs/deploy/github)
- [Docker Documentation](https://docs.docker.com/)

## 🆘 Need Help?

- Koyeb Community: [community.koyeb.com](https://community.koyeb.com)
- Koyeb Support: support@koyeb.com
- GitHub Issues: Create an issue in your repository

---

**Congratulations! Your 6Xpress e-commerce platform is now deployed on Koyeb! 🎊**
