# 🚀 Quick Deployment Reference

## One-Time Setup

### 1. Push to GitHub
```bash
# Navigate to project
cd /Users/gobinath/study/E-commers/colage-pojects

# Add all files
git add .

# Commit
git commit -m "Ready for Koyeb deployment"

# Create repo on GitHub, then:
git remote add origin https://github.com/YOUR_USERNAME/6xpress-ecommerce.git
git push -u origin main
```

### 2. Deploy on Koyeb
1. Go to https://app.koyeb.com
2. Click **Create App**
3. Select **GitHub**
4. Choose your repository
5. Settings:
   - **Port**: `80`
   - **Instance**: Nano (free) or Small
   - **Region**: Choose closest to users
6. Click **Deploy**

## Future Updates

```bash
# Make changes to your code
git add .
git commit -m "Your update message"
git push origin main
# Koyeb auto-deploys! ✨
```

## Important Files Created

- ✅ `Dockerfile` - Container configuration
- ✅ `.dockerignore` - Excludes unnecessary files
- ✅ `.gitignore` - Prevents committing sensitive files
- ✅ `koyeb.yaml` - Koyeb deployment settings
- ✅ `README.md` - Project documentation
- ✅ `DEPLOYMENT_GUIDE.md` - Detailed deployment guide
- ✅ `deploy.sh` - Interactive deployment helper

## Quick Commands

```bash
# Check git status
git status

# View deployment helper
./deploy.sh

# Test locally with Docker
docker build -t 6xpress .
docker run -p 8080:80 6xpress
# Visit: http://localhost:8080

# View logs on Koyeb
# Dashboard → Your App → Logs
```

## Koyeb Configuration

**Port**: 80 (Apache default)  
**Health Check**: `/` (homepage)  
**Auto-deploy**: Enabled (on git push)  
**Free Tier**: 1 Nano instance, 100GB bandwidth/month

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Build fails | Check Dockerfile syntax, view Koyeb logs |
| App not loading | Verify port is 80, check health check |
| 404 errors | Ensure .htaccess is included |
| CSV data missing | Files are in Docker image, check paths |

## URLs

- **Koyeb Dashboard**: https://app.koyeb.com
- **GitHub**: https://github.com
- **Your App**: `https://6xpress-<id>.koyeb.app` (after deployment)

## Support

- 📖 Full guide: `DEPLOYMENT_GUIDE.md`
- 🐛 Issues: Create GitHub issue
- 💬 Koyeb: community.koyeb.com

---

**Ready to deploy? Run `./deploy.sh` to get started!** 🚀
