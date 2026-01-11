#!/bin/bash

# 6Xpress Deployment Script for Koyeb
# This script helps you deploy your PHP application to Koyeb via GitHub

echo "🚀 6Xpress Deployment Helper"
echo "=============================="
echo ""

# Check if git is installed
if ! command -v git &> /dev/null; then
    echo "❌ Git is not installed. Please install Git first."
    exit 1
fi

echo "✅ Git is installed"
echo ""

# Check if we're in a git repository
if [ ! -d .git ]; then
    echo "📦 Initializing Git repository..."
    git init
    echo "✅ Git repository initialized"
else
    echo "✅ Already in a Git repository"
fi

echo ""
echo "📋 Current Git Status:"
git status --short

echo ""
echo "📝 Next Steps:"
echo ""
echo "1️⃣  Add all files to Git:"
echo "    git add ."
echo ""
echo "2️⃣  Commit your changes:"
echo "    git commit -m \"Initial commit - 6Xpress E-commerce Platform\""
echo ""
echo "3️⃣  Create a new repository on GitHub:"
echo "    → Go to https://github.com/new"
echo "    → Name it: 6xpress-ecommerce"
echo "    → Don't initialize with README"
echo "    → Click 'Create repository'"
echo ""
echo "4️⃣  Link to GitHub (replace YOUR_USERNAME):"
echo "    git branch -M main"
echo "    git remote add origin https://github.com/YOUR_USERNAME/6xpress-ecommerce.git"
echo "    git push -u origin main"
echo ""
echo "5️⃣  Deploy on Koyeb:"
echo "    → Go to https://app.koyeb.com"
echo "    → Click 'Create App'"
echo "    → Select 'GitHub'"
echo "    → Choose your repository"
echo "    → Set port to 80"
echo "    → Click 'Deploy'"
echo ""
echo "📖 For detailed instructions, see DEPLOYMENT_GUIDE.md"
echo ""

# Ask if user wants to add and commit now
read -p "Would you like to add and commit all files now? (y/n) " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "📦 Adding files..."
    git add .
    
    echo "💾 Committing..."
    git commit -m "Initial commit - 6Xpress E-commerce Platform"
    
    echo ""
    echo "✅ Files committed successfully!"
    echo ""
    echo "🔗 Now create a GitHub repository and run:"
    echo "   git remote add origin https://github.com/YOUR_USERNAME/6xpress-ecommerce.git"
    echo "   git push -u origin main"
fi

echo ""
echo "🎉 Good luck with your deployment!"
